<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchasePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchasePayment::with('purchaseOrder');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', '%' . $search . '%')
                  ->orWhere('amount', 'like', '%' . $search . '%')
                  ->orWhere('remaining_amount', 'like', '%' . $search . '%')
                  ->orWhereHas('purchaseOrder', function ($q2) use ($search) {
                      $q2->where('order_number', 'like', '%' . $search . '%')
                         ->orWhereHas('supplier', function ($q3) use ($search) {
                             $q3->where('name', 'like', '%' . $search . '%');
                         });
                  });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->latest()->get();

        return view('payments.index', compact('payments'));
    }

    public function print(Request $request)
    {
        $query = PurchasePayment::with('purchaseOrder');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->latest()->get();

        return view('payments.print', compact('payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::findOrFail($request->purchase_order_id);
        $totalPaid = $po->totalPaid();
        $remaining = $po->grand_total - $totalPaid;

        $totalPaymentAmount = $request->amount;
        $remainingAfterPayment = $remaining - $totalPaymentAmount;

        Log::info('Payment validation', [
            'totalPaymentAmount' => $totalPaymentAmount,
            'remaining' => $remaining,
        ]);

        /* Disabled validation to allow saving payment regardless of remaining balance */
        #if ($totalPaymentAmount > $remaining + 0.01) {
        #    return back()->withErrors(['amount' => 'Jumlah yang dibayar melebihi sisa pembayaran.']);
        #}

        // Generate payment_number
        $lastPayment = PurchasePayment::orderBy('id', 'desc')->first();
        if ($lastPayment && preg_match('/^PYM(\d+)$/', $lastPayment->payment_number, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }
        $paymentNumber = 'PYM' . str_pad($number, 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
        $installmentNumber = PurchasePayment::where('purchase_order_id', $request->purchase_order_id)->count() + 1;

        $payment = PurchasePayment::create([
            'purchase_order_id' => $request->purchase_order_id,
            'payment_number' => $paymentNumber,
            'installment_number' => $installmentNumber,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'remaining_amount' => $request->remaining_amount,
        ]);

        // Update paid_off_date in purchase order
        $po = PurchaseOrder::findOrFail($request->purchase_order_id);
        if ($po->remaining() == 0) {
            $po->paid_off_date = $payment->payment_date;
        } else {
            $po->paid_off_date = null;
        }
        $po->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan pembayaran.']);
        }

        return redirect()->route('payments.index')->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereRaw('grand_total > (SELECT IFNULL(SUM(amount), 0) FROM purchase_payments WHERE purchase_order_id = purchase_orders.id) > 0')
            ->get();


        return view('payments.create', compact('purchaseOrders'));
    }

    public function getPurchaseOrderDetails($id)
    {
        $po = PurchaseOrder::with('items.product')->findOrFail($id);

        $items = $po->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'total' => $item->total,
            ];
        });

        $grandTotal = $po->items->sum('total');

        return response()->json([
            'items' => $items,
            'grand_total' => $grandTotal,
        ]);
    }

    public function edit($id)
    {
        $po = PurchaseOrder::with('payments', 'items.product')->findOrFail($id);

        $totalPaid = $po->totalPaid();
        $grandTotal = $po->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $sisa = $grandTotal - $totalPaid;


        return view('payments.edit', compact('po', 'sisa', 'grandTotal', 'totalPaid'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'remaining_amount' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::findOrFail($id);
        // Hitung total yang sudah dibayar sebelum ini
        $totalPaid = $po->totalPaid();
        $grandTotal = $po->grand_total;

        // Sisa sebelum pembayaran ini
        $sisaSebelumnya = $grandTotal - $totalPaid;

        // Jumlah yang dibayar sekarang
        $dibayarSekarang = $request->amount;

        // Hitung sisa baru
        $remaining = max(0, $sisaSebelumnya - $dibayarSekarang);

        // Logic generate nomor baru
        $lastPayment = PurchasePayment::orderBy('id', 'desc')->first();
        if ($lastPayment && preg_match('/^PYM(\d+)$/', $lastPayment->payment_number, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }
        $paymentNumber = 'PYM' . str_pad($number, 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
        $installmentNumber = PurchasePayment::where('purchase_order_id', $id)->count() + 1;

        $payment = PurchasePayment::create([
            'purchase_order_id' => $id,
            'payment_number' => $paymentNumber,
            'installment_number' => $installmentNumber,
            'payment_date' => $request->payment_date,
            'amount' => $dibayarSekarang,
            'remaining_amount' => $remaining,
        ]);

        // Update paid_off_date in purchase order
        if ($po->remaining() == 0) {
            $po->paid_off_date = $payment->payment_date;
        } else {
            $po->paid_off_date = null;
        }
        $po->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan pelunasan.']);
        }

        return redirect()->route('payments.index')->with('success', 'Pelunasan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $payment = PurchasePayment::with('purchaseOrder.supplier', 'purchaseOrder.payments')->findOrFail($id);
        $purchaseOrder = $payment->purchaseOrder;

        $isPaidOff = $purchaseOrder->remaining() == 0;

        if ($isPaidOff) {
            $payments = $purchaseOrder->payments()->with('purchaseOrder.supplier')->get();
            $totalPaidAmount = $payments->sum('amount');
            return view('payments.show', compact('payments', 'purchaseOrder', 'isPaidOff', 'totalPaidAmount', 'payment'));
        } else {
            $payments = collect([$payment]);
            $totalPaidAmount = $payment->amount;
            return view('payments.show', compact('payments', 'purchaseOrder', 'isPaidOff', 'totalPaidAmount', 'payment'));
        }
    }

}
