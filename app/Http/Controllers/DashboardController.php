<?php

namespace App\Http\Controllers;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use App\Models\ReturnOrder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah supplier
        $supplierCount = Supplier::count();

        // Menghitung jumlah produk (jika diperlukan)
        $productCount = Product::count();

        // Menghitung jumlah purchase order (jika diperlukan)
        $purchaseOrderCount = PurchaseOrder::count();

        // Menghitung jumlah purchase return (jika diperlukan)
        $purchaseReturnCount = ReturnOrder::count();

        // Ambil produk dengan stok rendah (< 20)
        $lowStockProducts = Product::where('stock', '<', 10)->get();

        $unpaidOrders = PurchaseOrder::with(['supplier', 'payments'])
            ->get()
            ->filter(function ($po) {
                return $po->totalPaid() < $po->grand_total && $po->paid_off_date === null;
            });

        return view('welcome', compact('supplierCount', 'productCount', 'lowStockProducts','purchaseOrderCount','purchaseReturnCount','unpaidOrders'));

    }
}
