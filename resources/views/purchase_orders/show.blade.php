@extends('purchase_orders.layout')

@section('content')
<div class="container-fluid" id="printableArea">
    <h1 class="h3 mb-4 text-gray-800">Detail Pembelian - {{ $purchaseOrder->order_number }}</h1>

    <div class="card shadow mb-4">
        {{-- Tombol Cetak di kanan atas --}}
    <a href="{{ route('purchase_orders.printSingle', $purchaseOrder->id) }}"
        target="_blank"
        class="btn btn-sm btn-primary position-absolute"
        style="top: 1rem; right: 1rem; background-color: #8C52A0; color: white;">
         <i class="fas fa-print"></i> Cetak
     </a>

        <div class="card-body">
            <p><strong>No Order:</strong> {{ $purchaseOrder->order_number }}</p>
            <p><strong>Tanggal Order:</strong> {{ $purchaseOrder->order_date }}</p>
            <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-' }}</p>

            {{-- Payment status notification --}}
            <div class="mb-3">
                @if($purchaseOrder->installment_target == 0)
                    @if($installmentCount > 0)
                        <div class="alert alert-success" role="alert">
                            ✅ Pembayaran Lunas
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            ⚠️ Belum Dibayar
                        </div>
                    @endif
                @elseif($isPaidOff && $installmentCount > 0)
                    <div class="alert alert-success" role="alert">
                        ✅ Pembayaran Lunas — Total {{ $installmentCount }} kali cicilan
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">
                        ⚠️ Belum Lunas — Cicilan ke-{{ $installmentCount }} dari {{ $installment_target }}
                    </div>
                @endif
            </div>


            <hr>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td>{{ $item->product ? $item->product->name : '-' }}</td>
                                <td class="text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                            @php $grandTotal += $item->total; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Keseluruhan</th>
                            <th class="text-right font-weight-bold">Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Payments table --}}
            <div class="mt-4">
                <h5>Daftar Pembayaran</h5>
                @if($payments->isEmpty())
                    <p>Belum ada pembayaran.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cicilan ke-</th>
                                    <th>Nomor Pembayaran</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th class="text-right">Jumlah Bayar</th>
                                    <th class="text-right">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->installment_number }}</td>
                                        <td>{{ $payment->payment_number }}</td>
                                        <td>{{ $payment->payment_date }}</td>
                                        <td class="text-right">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp{{ number_format($payment->remaining_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-4 no-print">
                <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">Kembali</a>
                {{--<a href="{{ route('purchase_orders.printSingle', $purchaseOrder->id) }}" target="_blank" class="btn btn-primary" style="background-color: #8C52A0; color: white;"
                    >Cetak</a>--}}
            </div>

        </div>
    </div>
</div>
@endsection
