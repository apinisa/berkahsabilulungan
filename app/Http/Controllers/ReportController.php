<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\PurchasePayment;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Laporan Pemasukan
    public function incomeReport(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->orderBy('sale_date')->get();

        $totalIncome = $sales->sum('total_payment');

        return view('reports.income', compact('sales', 'totalIncome'));
    }

    // Laporan Pengeluaran
    public function expenseReport(Request $request)
    {
        $query = PurchasePayment::with('purchaseOrder.supplier');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('supplier_id')) {
            $query->whereHas('purchaseOrder.supplier', function ($q) use ($request) {
                $q->where('id', $request->supplier_id);
            });
        }

        $payments = $query->orderBy('payment_date')->get();

        $totalExpense = $payments->sum('amount');

        return view('reports.expense', compact('payments', 'totalExpense'));
    }

    public function summaryReport(Request $request)
{
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    // Jika tidak memilih tanggal, ambil semua data
    if (!$startDate || !$endDate) {
        $sales = Sale::select('sale_date as tanggal', DB::raw('"Penjualan" as tipe'), 'total_payment as jumlah')->get();
        $expenses = PurchasePayment::select('payment_date as tanggal', DB::raw('"Pembelian" as tipe'), 'amount as jumlah')->get();
    } else {
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->select('sale_date as tanggal', DB::raw('"Penjualan" as tipe'), 'total_payment as jumlah')
            ->get();

        $expenses = PurchasePayment::whereBetween('payment_date', [$startDate, $endDate])
            ->select('payment_date as tanggal', DB::raw('"Pembelian" as tipe'), 'amount as jumlah')
            ->get();
    }

    $transactions = $sales->concat($expenses)->sortBy('tanggal');

    $totalIncome = $sales->sum('jumlah');
    $totalExpense = $expenses->sum('jumlah');
    $netProfit = $totalIncome - $totalExpense;

    return view('reports.summary', compact(
        'startDate',
        'endDate',
        'transactions',
        'totalIncome',
        'totalExpense',
        'netProfit'
    ));

    }

    public function summaryReportPrint(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$startDate || !$endDate) {
            $sales = Sale::select('sale_date as tanggal', DB::raw('"Penjualan" as tipe'), 'total_payment as jumlah')->get();
            $expenses = PurchasePayment::select('payment_date as tanggal', DB::raw('"Pembelian" as tipe'), 'amount as jumlah')->get();
        } else {
            $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
                ->select('sale_date as tanggal', DB::raw('"Penjualan" as tipe'), 'total_payment as jumlah')
                ->get();

            $expenses = PurchasePayment::whereBetween('payment_date', [$startDate, $endDate])
                ->select('payment_date as tanggal', DB::raw('"Pembelian" as tipe'), 'amount as jumlah')
                ->get();
        }

        $transactions = $sales->concat($expenses)->sortBy('tanggal');

        $totalIncome = $sales->sum('jumlah');
        $totalExpense = $expenses->sum('jumlah');
        $netProfit = $totalIncome - $totalExpense;

        return view('reports.summary_print', compact(
            'startDate',
            'endDate',
            'transactions',
            'totalIncome',
            'totalExpense',
            'netProfit'
        ));
    }


    // Laporan Hutang Usaha (Optional)
    public function debtReport(Request $request)
    {
        $query = PurchaseOrder::with('supplier')
            ->withSum('payments', 'amount')
            ->havingRaw('grand_total > IFNULL(payments_sum_amount, 0)');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchaseOrders = $query->get();

        return view('reports.debt', compact('purchaseOrders'));
    }

    public function incomeReportPrint(Request $request)
{
    $query = Sale::query();

    $startDate = $request->start_date;
    $endDate = $request->end_date;

    if ($startDate && $endDate) {
        $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    $sales = $query->orderBy('sale_date')->get();
    $totalIncome = $sales->sum('total_payment');

    return view('reports.income_print', compact('sales', 'totalIncome', 'startDate', 'endDate'));
}

   public function expenseReportPrint(Request $request)
{
    $query = PurchasePayment::with('purchaseOrder.supplier');

    $startDate = $request->start_date;
    $endDate = $request->end_date;

    if ($startDate && $endDate) {
        $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    if ($request->filled('supplier_id')) {
        $query->whereHas('purchaseOrder.supplier', function ($q) use ($request) {
            $q->where('id', $request->supplier_id);
        });
    }

    $payments = $query->orderBy('payment_date')->get();
    $totalExpense = $payments->sum('amount');

    return view('reports.expense_print', compact('payments', 'totalExpense', 'startDate', 'endDate'));
}

}
