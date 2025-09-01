<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderItem;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['items.product', 'supplier', 'payments']);

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('order_date', 'like', '%' . $search . '%')
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('items.product', function($q3) use ($search) {
                      $q3->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $purchases = $query->orderBy('order_number', 'desc')->get();

        // Add payment status, installment count, and remaining amount attributes to each purchase order
        foreach ($purchases as $purchase) {
            $installmentCount = $purchase->payments->count();
            $purchase->installmentCount = $installmentCount;

            $totalPaid = $purchase->payments->sum('amount');
            $remainingAmount = $purchase->grand_total - $totalPaid;
            $purchase->remainingAmount = $remainingAmount;

            $purchase->isPaidOff = $remainingAmount <= 0 && $installmentCount > 0;
        }

        return view('purchase_orders.index', compact('purchases'));
    }

    public function create()
    {
        // Misalnya kamu ingin menampilkan form pembuatan purchase order
        $suppliers = Supplier::all(); // Pastikan kamu sudah use model Supplier
        $products = Product::all();   // Jika perlu produk


        // Buat nomor order, misalnya otomatis berdasarkan waktu atau ID terakhir
        $lastOrder = PurchaseOrder::orderByDesc('order_number')->first();

        if ($lastOrder && preg_match('/PO-(\d+)/', $lastOrder->order_number, $matches)) {
            $lastNumber = (int)$matches[1];
        } else {
                $lastNumber = 0;
        }


        $orderNumber = 'PO-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);


        return view('purchase_orders.create', compact('suppliers', 'products', 'orderNumber'));
    }

    public function store(Request $request)
{
    $request->validate([
        'order_number' => 'required|unique:purchase_orders',
        'order_date' => 'required|date',
        'supplier_id' => 'required|exists:suppliers,supplier_id',
        'products' => 'required|array',
        'quantities' => 'required|array',
        'installment_target' => 'nullable|integer|min:0',
    ]);

    // Hitung grand total dulu
    $grandTotal = 0;
    foreach ($request->products as $index => $productId) {
        $product = Product::where('product_id', $productId)->firstOrFail();
        $quantity = $request->quantities[$index];
        $grandTotal += $product->price * $quantity;
    }

    // Simpan ke purchase_orders
    $purchaseOrder = PurchaseOrder::create([
        'order_number' => $request->order_number,
        'order_date' => $request->order_date,
        'supplier_id' => $request->supplier_id,
        'grand_total' => $grandTotal,
        'installment_target' => $request->installment_target ?? 0, // default ke 0 jika kosong

    ]);

    // Simpan item
    foreach ($request->products as $index => $productId) {
        $product = Product::where('product_id', $productId)->firstOrFail();
        $quantity = $request->quantities[$index];
        $price = $product->price;
        $total = $price * $quantity;

        PurchaseOrderItem::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
        ]);

        $product->stock += $quantity;
        $product->save();
    }

    return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order berhasil dibuat.');
}


    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.product')->findOrFail($id);
        $suppliers = Supplier::all();
        $products = Product::all();

        $items = $purchaseOrder->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product ? $item->product->name : '-',
                'price' => $item->price,
                'quantity' => $item->quantity,
                'total' => $item->total,
            ];
        });

        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'products', 'items'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'order_date' => 'required|date',
        'supplier_id' => 'required|exists:suppliers,supplier_id',
        'products' => 'required|array',
        'products.*' => 'exists:products,product_id',
        'quantities' => 'required|array',
        'quantities.*' => 'integer|min:1',

    ]);

    $purchaseOrder = PurchaseOrder::findOrFail($id);
    $purchaseOrder->order_date = $request->order_date;
    $purchaseOrder->supplier_id = $request->supplier_id;
    $purchaseOrder->installment_target = $request->installment_target ?? 0;

    // Hapus item lama & rollback stok
    foreach ($purchaseOrder->items as $item) {
        $product = Product::where('product_id', $item->product_id)->first();
        if ($product) {
            $product->stock -= $item->quantity;
            $product->save();
        }
    }

    $purchaseOrder->items()->delete();

    // Tambah item baru & hitung grand total
    $grandTotal = 0;

    foreach ($request->products as $index => $productId) {
        $product = Product::where('product_id', $productId)->firstOrFail();
        $quantity = $request->quantities[$index];
        $price = $product->price;
        $total = $price * $quantity;
        $grandTotal += $total;

        PurchaseOrderItem::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
        ]);

        $product->stock += $quantity;
        $product->save();
    }

    $purchaseOrder->grand_total = $grandTotal;
    $purchaseOrder->save();

    return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order berhasil diperbarui.');
}


    public function getProductsBySupplier($supplier_id)
    {
        $products = Product::where('supplier_id', $supplier_id)->get();
        return response()->json($products);
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::with('payments')->findOrFail($id);

        // Tambahkan kembali stok produk terkait sebelum menghapus purchase order
        foreach ($purchaseOrder->items as $item) {
            $product = Product::where('product_id', $item->product_id)->first();
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        // Hapus pembayaran terkait purchase order
        foreach ($purchaseOrder->payments as $payment) {
            $payment->delete();
        }

        // Hapus item purchase order terlebih dahulu
        $purchaseOrder->items()->delete();

        // Hapus purchase order
        $purchaseOrder->delete();

        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order berhasil dihapus.');
    }

    public function print(Request $request)
    {
        $query = PurchaseOrder::with('supplier', 'items.product','payments');

        // Filter berdasarkan rentang waktu jika ada input tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }

        $purchaseOrders = $query->get();

        return view('purchase_orders.print', compact('purchaseOrders'));
    }

    public function printSingle($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.product', 'supplier', 'payments')->findOrFail($id);

        $payments = $purchaseOrder->payments()->orderBy('installment_number')->get();
        $installmentCount = $payments->count();

        $installment_target = $purchaseOrder->installment_target;
        $isPaidOff = $purchaseOrder->grand_total - $payments->sum('amount') == 0;

        return view('purchase_orders.print_single', compact('purchaseOrder', 'payments', 'installmentCount', 'installment_target','isPaidOff'));
    }


    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.product', 'supplier', 'payments')->findOrFail($id);

        $payments = $purchaseOrder->payments()->orderBy('installment_number')->get();
        $installmentCount = $payments->count();

        $installment_target = $purchaseOrder->installment_target;
         $isPaidOff = $purchaseOrder->grand_total - $payments->sum('amount') == 0;

        return view('purchase_orders.show', compact('purchaseOrder', 'payments', 'installmentCount', 'installment_target','isPaidOff'));
    }

    public function getPurchaseOrderDetails($id)
    {
    $purchaseOrder = PurchaseOrder::with('supplier', 'items.product')->findOrFail($id);

    return response()->json([
        'supplier' => $purchaseOrder->supplier,
        'items' => $purchaseOrder->items,
    ]);
    }




}
