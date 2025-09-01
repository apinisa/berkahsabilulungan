<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchasePaymentController;

use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReturnOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [DashboardController::class, 'index']);
//Route for supplier
Route::resource('suppliers',SupplierController::class);
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');

//Route for product
Route::resource('products',ProductController::class);
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::get('/products/check-id/{id}', [ProductController::class, 'checkProductId']);

//Route for Purchase Order/pembelian
Route::get('/purchase_orders/print', [PurchaseOrderController::class, 'print'])->name('purchase_orders.print');
Route::get('/purchase_orders/{id}/print', [PurchaseOrderController::class, 'printSingle'])->name('purchase_orders.printSingle');
Route::resource('purchase_orders',PurchaseOrderController::class);
Route::get('/get-products-by-supplier/{supplier_id}', [PurchaseOrderController::class, 'getProductsBySupplier']);

//Route for return/pengembalian
Route::get('/returns/print', [ReturnOrderController::class, 'print'])->name('returns.print');
Route::resource('returns', ReturnOrderController::class);
Route::get('/returns/{id}/ganti-rugi', [ReturnOrderController::class, 'gantiRugiView'])->name('returns.gantiRugiView');
Route::put('/returns/{id}/ganti-rugi', [ReturnOrderController::class, 'gantiRugi'])->name('returns.gantiRugi');
Route::get('returns/{id}/print', [ReturnOrderController::class, 'printSingle'])->name('returns.printSingle');
Route::get('/returns/purchase-order/{id}', [ReturnOrderController::class, 'getPurchaseOrderDetails']);

//Route for pembayaran
Route::get('/payments/print', [PurchasePaymentController::class, 'print'])->name('payments.print');
Route::resource('payments', PurchasePaymentController::class);
Route::get('payments/purchase-orders/{id}/details', [PurchasePaymentController::class, 'getPurchaseOrderDetails']);

//Route for penjualan
Route::get('/sales/print', [SaleController::class, 'print'])->name('sales.print');
Route::get('sales/{id}/print', [SaleController::class, 'printSingle'])->name('sales.printSingle');
Route::resource('sales', SaleController::class);

// Report routes
Route::prefix('reports')->group(function () {
    Route::get('/income', [ReportController::class, 'incomeReport'])->name('reports.income');
    Route::get('/expense', [ReportController::class, 'expenseReport'])->name('reports.expense');
    Route::get('/summary', [ReportController::class, 'summaryReport'])->name('reports.summary');
    Route::get('/debt', [ReportController::class, 'debtReport'])->name('reports.debt');
    Route::get('/summary/print', [ReportController::class, 'summaryReportPrint'])->name('reports.summary.print');
    Route::get('/income/print', [ReportController::class, 'incomeReportPrint'])->name('reports.income.print');
    Route::get('/expense/print', [ReportController::class, 'expenseReportPrint'])->name('reports.expense.print');
});
