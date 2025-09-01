@extends('payments.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Pembayaran </h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($isPaidOff)
                <h4>Semua Pembayaran untuk : {{ $purchaseOrder->order_number }} - {{ $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-' }}</h4>
                <p><strong>Total Pembelian:</strong> Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Pembayaran</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Sisa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_number }}</td>
                            <td>{{ $payment->payment_date }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($payment->remaining_amount, 0, ',', '.') }}</td>
                            <td>{{ $payment->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table class="table table-bordered">
                    <tr>
                        <th>ID Pembayaran</th>
                        <td>{{ $payment->payment_number }}</td>
                    </tr>
                    <tr>
                        <th>Nomor PO</th>
                        <td>{{ $payment->purchaseOrder->order_number }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $payment->purchaseOrder->supplier->name }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Bayar</th>
                        <td>{{ $payment->payment_date }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Bayar</th>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Sisa</th>
                        <td>Rp {{ number_format($payment->remaining_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($payment->remaining_amount == 0)
                                ✅ Lunas
                            @elseif($payment->amount == 0)
                                ❌ Belum Dibayar
                            @else
                                ⚠️ Belum Lunas
                            @endif
                        </td>
                    </tr>
                </table>
            @endif

            @if($isPaidOff)
                <div class="mt-3">
                    <h5>Total Jumlah Bayar: Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</h5>
                </div>
            @endif

            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection
