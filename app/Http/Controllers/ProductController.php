<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('supplier')->get();
        $groupedProducts = $products->groupBy('supplier_id');
        return view('products.index', compact('products', 'groupedProducts'));
    }

    public function create()
    {
        $suppliers = Supplier::all();

        // Generate product_id seperti BRG001, BRG002, dst.
        $lastProduct = Product::where('product_id', 'like', 'BRG%')
            ->orderBy('product_id', 'desc')
            ->first();

        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct->product_id, 3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $product_id = 'BRG' . $nextNumber;

        return view('products.create', compact('suppliers', 'product_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|unique:products,product_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
        ]);

        $product = new Product();
        $product->product_id = $request->product_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->selling_price = $request->selling_price;
        $product->stock = $request->stock;
        $product->supplier_id = $request->supplier_id;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
        ]);

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->selling_price = $request->selling_price;
        $product->stock = $request->stock;
        $product->supplier_id = $request->supplier_id;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors('Gagal menghapus produk: ' . $e->getMessage());
        }
    }
    public function show($id)
{
    $product = Product::with('supplier.products')->findOrFail($id);
    return view('products.show', compact('product'));
}


}
