<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
{
    $query = Sale::with('items.product')->latest();

    // Filter tanggal (jika diisi)
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
    }

     // Filter pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('buyer_name', 'like', "%{$search}%")
              ->orWhere('sale_number', 'like', "%{$search}%")
              ->orWhere('sale_date', 'like', "%{$search}%")
              ->orWhere('discount', 'like', "%{$search}%")
              ->orWhere('total_payment', 'like', "%{$search}%")
              ->orWhere('payment_method', 'like', "%{$search}%")
              ->orWhere('note', 'like', "%{$search}%");
        });
    }

    // Paginate (bisa ubah jumlah per halaman)
    $sales = $query->paginate(10);

    return view('sales.index', compact('sales'));
}


    public function create()
    {
        $products = Product::all();

        // Generate next global sale number
        $lastSale = Sale::orderBy('sale_number', 'desc')->first();
        $lastNumber = 0;
        if ($lastSale && preg_match('/SL(\d{3})$/', $lastSale->sale_number, $matches)) {
            $lastNumber = (int)$matches[1];
        }
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $saleNumber = 'SL' . $nextNumber;

        return view('sales.create', compact('products', 'saleNumber'));
    }

public function store(Request $request)
{
    $request->validate([
        'sale_date' => 'required|date',
        'buyer_name' => 'nullable|string|max:255',
        'products.*.product_id' => 'required|exists:products,product_id',
        'products.*.price' => 'required|numeric',
        'products.*.quantity' => 'required|integer|min:1',
        'discount' => 'nullable|numeric|min:0',
        'payment_method' => 'required|in:Tunai,Transfer',
    ]);

    DB::beginTransaction();
    try {
        $lastSale = Sale::orderBy('sale_number', 'desc')->first();
        $lastNumber = 0;
        if ($lastSale && preg_match('/SL(\d{3})$/', $lastSale->sale_number, $matches)) {
            $lastNumber = (int)$matches[1];
        }
        $saleNumber = 'SL' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Siapkan data produk
        $productIds = collect($request->products)->pluck('product_id');
        $productsMap = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        $totalItems = 0;
        foreach ($request->products as $item) {
            $product = $productsMap[$item['product_id']];
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Stok produk '{$product->name}' tidak mencukupi.");
            }
            $totalItems += $item['price'] * $item['quantity'];
        }

        $discount = $request->discount ?? 0;
        if ($discount > $totalItems) {
            throw new \Exception("Diskon tidak boleh lebih besar dari total harga.");
        }

        $sale = Sale::create([
            'sale_date' => $request->sale_date,
            'sale_number' => $saleNumber,
            'buyer_name' => $request->buyer_name,
            'discount' => $discount,
            'total_payment' => $totalItems - $discount,
            'payment_method' => $request->payment_method,
            'note' => $request->note,
        ]);

        foreach ($request->products as $item) {
            $product = $productsMap[$item['product_id']];
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->product_id,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['price'] * $item['quantity'],
            ]);
            $product->decrement('stock', $item['quantity']);
        }

        DB::commit();
        return redirect()->route('sales.create')->with('sale_success', $sale->sale_number);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->withErrors(['error' => 'Gagal menyimpan penjualan: ' . $e->getMessage()]);
    }
}


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::with('items')->findOrFail($id);

            // Restore stock for each sale item
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // Delete sale items
            $sale->items()->delete();

            // Delete sale
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors('Gagal menghapus penjualan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);
        $products = Product::all();

        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'sale_date' => 'required|date',
        'buyer_name' => 'nullable|string|max:255',
        'products.*.product_id' => 'required|exists:products,product_id',
        'products.*.price' => 'required|numeric',
        'products.*.quantity' => 'required|integer|min:1',
        'discount' => 'nullable|numeric|min:0',
        'payment_method' => 'required|in:Tunai,Transfer',
    ]);

    DB::beginTransaction();
    try {
        $sale = Sale::with('items')->findOrFail($id);

        // Kembalikan stok lama
        foreach ($sale->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        $sale->items()->delete();

        $productIds = collect($request->products)->pluck('product_id');
        $productsMap = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        $totalItems = 0;
        foreach ($request->products as $item) {
            $product = $productsMap[$item['product_id']];
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Stok produk '{$product->name}' tidak mencukupi.");
            }
            $totalItems += $item['price'] * $item['quantity'];
        }

        $discount = $request->discount ?? 0;
        if ($discount > $totalItems) {
            throw new \Exception("Diskon tidak boleh lebih besar dari total harga.");
        }

        $sale->update([
            'sale_date' => $request->sale_date,
            'buyer_name' => $request->buyer_name,
            'discount' => $discount,
            'total_payment' => $totalItems - $discount,
            'payment_method' => $request->payment_method,
            'note' => $request->note,
        ]);

        foreach ($request->products as $item) {
            $product = $productsMap[$item['product_id']];
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->product_id,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['price'] * $item['quantity'],
            ]);
            $product->decrement('stock', $item['quantity']);
        }

        DB::commit();
        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->withErrors(['error' => 'Gagal memperbarui penjualan: ' . $e->getMessage()]);
    }
}

public function show($id)
{
    $sale = Sale::with('items.product')->findOrFail($id);
    return view('sales.show', compact('sale'));
}

public function printSingle($id)
{
    $sale = Sale::with('items.product')->findOrFail($id);
    return view('sales.printSingle', compact('sale'));
}

public function print(Request $request)
{
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    $query = Sale::with(['items.product']);

    if ($startDate && $endDate) {
        $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('buyer_name', 'like', "%{$search}%")
              ->orWhere('sale_number', 'like', "%{$search}%")
              ->orWhere('sale_date', 'like', "%{$search}%")
              ->orWhere('discount', 'like', "%{$search}%")
              ->orWhere('total_payment', 'like', "%{$search}%")
              ->orWhere('payment_method', 'like', "%{$search}%")
              ->orWhere('note', 'like', "%{$search}%");
        });
    }

    $sales = $query->orderBy('sale_date')->get();

    return view('sales.print', compact('sales', 'startDate', 'endDate'));
}

}
