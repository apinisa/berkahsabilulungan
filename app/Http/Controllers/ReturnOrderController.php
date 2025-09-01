<?php

namespace App\Http\Controllers;

use App\Models\ReturnOrder;
use App\Models\ReturnOrderItem;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class ReturnOrderController extends Controller
{
    // Menampilkan form untuk membuat return order baru
    public function create()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')->orderBy('order_number')->get();
        $products = Product::orderBy('name')->get();

        // Generate next return number
        $lastReturn = ReturnOrder::orderBy('id', 'desc')->first();
        if ($lastReturn) {
            $lastNumber = intval(substr($lastReturn->return_number, 3));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $nextReturnNumber = 'RTN' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('returns.create', compact('purchaseOrders', 'products', 'nextReturnNumber'));
    }

    // Menyimpan data return order ke database
    public function store(Request $request)
{
    $validated = $request->validate([
        'return_number' => 'required|string|unique:return_orders,return_number',
        'return_date' => 'required|date',
        'purchase_order_id' => 'required|exists:purchase_orders,id',
        'products' => 'required|array',
        'products.*.product_id' => 'required|exists:products,product_id',
        'products.*.quantity' => 'required|integer|min:1',
        'products.*.reason' => 'nullable|string',
    ]);

    $returnOrder = ReturnOrder::create([
        'return_number' => $validated['return_number'],
        'return_date' => $validated['return_date'],
        'purchase_order_id' => $validated['purchase_order_id'],
    ]);

    foreach ($validated['products'] as $productData) {
        $product = Product::where('product_id', $productData['product_id'])->first();

        if ($product && $product->stock >= $productData['quantity']) {
            $product->stock -= $productData['quantity'];
            $product->save();

            ReturnOrderItem::create([
                'return_order_id' => $returnOrder->id,
                'product_id' => $product->product_id,
                'quantity' => $productData['quantity'],
                'reason' => $productData['reason'] ?? null,
                'total' => $product->price * $productData['quantity'],
            ]);
        } else {
            return back()->withErrors(['error' => 'Stok tidak cukup untuk produk: ' . $product->name]);
        }
    }

    return redirect()->route('returns.index')->with('success', 'Return berhasil disimpan.');
}


    // Menampilkan daftar return orders
    public function index(Request $request)
    {
        $query = ReturnOrder::with('purchaseOrder.supplier', 'product');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', '%' . $search . '%')
                  ->orWhere('return_date', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhereHas('purchaseOrder', function ($q2) use ($search) {
                      $q2->where('order_number', 'like', '%' . $search . '%')
                         ->orWhereHas('supplier', function ($q3) use ($search) {
                             $q3->where('name', 'like', '%' . $search . '%');
                         });
                  })
                  ->orWhereHas('items.product', function ($q4) use ($search) {
                      $q4->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        // Filter berdasarkan rentang tanggal
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('return_date', [$request->start_date, $request->end_date]);
    }

        $returnOrders = $query->orderBy('id', 'desc')->get();
        return view('returns.index', compact('returnOrders'));
    }

    // Menampilkan detail return order
    public function show($id)
    {
        $returnOrder = ReturnOrder::with('purchaseOrder.supplier', 'product')->findOrFail($id);
        return view('returns.show', compact('returnOrder'));
    }

    public function getPurchaseOrderDetails($id)
{
    $purchaseOrder = PurchaseOrder::with('items.product')->find($id);
    return response()->json($purchaseOrder);
}

    // Menampilkan form edit return order
    public function edit($id)
    {
        $returnOrder = ReturnOrder::with('items.product')->findOrFail($id);
        $purchaseOrders = PurchaseOrder::with('supplier')->orderBy('order_number')->get();
        $products = Product::orderBy('name')->get();

        return view('returns.edit', compact('returnOrder', 'purchaseOrders', 'products'));
    }

    // Memperbarui data return order
    public function update(Request $request, $id)
    {
        $returnOrder = ReturnOrder::with('items')->findOrFail($id);

        $validated = $request->validate([
            'return_date' => 'required|date',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,product_id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.reason' => 'nullable|string',
        ]);

        // Revert stock for old return items
        foreach ($returnOrder->items as $item) {
            $product = Product::where('product_id', $item->product_id)->first();
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        // Update return order info
        $returnOrder->update([
            'return_date' => $validated['return_date'],
            'purchase_order_id' => $validated['purchase_order_id'],
        ]);

        // Delete old return items
        ReturnOrderItem::where('return_order_id', $returnOrder->id)->delete();

        // Add new return items and update stock
        foreach ($validated['products'] as $productData) {
            $product = Product::where('product_id', $productData['product_id'])->first();

            if ($product && $product->stock >= $productData['quantity']) {
                $product->stock -= $productData['quantity'];
                $product->save();

                ReturnOrderItem::create([
                    'return_order_id' => $returnOrder->id,
                    'product_id' => $product->product_id,
                    'quantity' => $productData['quantity'],
                    'reason' => $productData['reason'] ?? null,
                    'total' => $product->price * $productData['quantity'],
                ]);
            } else {
                return back()->withErrors(['error' => 'Stok tidak cukup untuk produk: ' . $product->name]);
            }
        }

        return redirect()->route('returns.index')->with('success', 'Return berhasil diperbarui.');
    }

    // Menghapus return order beserta itemnya dan mengembalikan stok produk
    public function destroy($id)
    {
        $returnOrder = ReturnOrder::with('items')->findOrFail($id);

        // Revert stock for return items
        foreach ($returnOrder->items as $item) {
            $product = Product::where('product_id', $item->product_id)->first();
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        // Delete return items
        ReturnOrderItem::where('return_order_id', $returnOrder->id)->delete();

        // Delete return order
        $returnOrder->delete();

        return redirect()->route('returns.index')->with('success', 'Return berhasil dihapus.');
    }

    public function printSingle($id)
    {
        $returnOrder = ReturnOrder::with('items.product', 'purchaseOrder.supplier')->findOrFail($id);
        return view('returns.print_single', compact('returnOrder'));
    }

    // Menampilkan halaman ganti rugi untuk return order
public function gantiRugiView($id)
{
    $returnOrder = ReturnOrder::with('items.product', 'purchaseOrder.supplier')->findOrFail($id);

    // Pastikan hanya return order dengan status 'belum diganti' yang bisa diganti rugi
    if ($returnOrder->status != 'belum diganti') {
        return redirect()->route('returns.index')->with('error', 'Return order tidak dapat diganti rugi.');
    }

    return view('returns.gantiRugi', compact('returnOrder'));
}

// Menangani aksi ganti rugi
public function gantiRugi(Request $request, $id)
{
    $returnOrder = ReturnOrder::findOrFail($id);

    // Pastikan status adalah 'belum diganti'
    if ($returnOrder->status == 'belum diganti') {
        // Validasi input tanggal ganti rugi
        $validated = $request->validate([
            'status' => 'required|string|in:sudah diganti',
            'replaced_at' => 'required|date',
        ]);

        // Update status menjadi 'sudah diganti' dan set tanggal ganti rugi
        $returnOrder->status = 'sudah diganti';
        $returnOrder->replaced_at = $validated['replaced_at']; // Menggunakan tanggal yang diinputkan
        $returnOrder->save();

        // Menambahkan stok produk yang diganti
        foreach ($returnOrder->items as $item) {
            $product = Product::where('product_id', $item->product_id)->first();
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        return redirect()->route('returns.index')->with('success', 'Return order telah diganti rugi.');
    }

    return redirect()->route('returns.index')->with('error', 'Return order tidak bisa diganti rugi.');
}

    public function print(Request $request)
{
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    $query = ReturnOrder::with(['purchaseOrder.supplier', 'items.product']);

    if ($startDate && $endDate) {
        $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    $returnOrders = $query->orderBy('return_date')->get();

    return view('returns.print', compact('returnOrders', 'startDate', 'endDate'));
}


}
