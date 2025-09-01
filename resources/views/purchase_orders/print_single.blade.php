<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $purchaseOrder->order_number }}</title>
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1, .header p {
            margin: 0;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="header">
            <img src="{{ asset('assets/img/logoberkah.png') }}" alt="Logo Toko">
            <h1>Toko Berkah Sabilulungan</h1>
            <p>Jl. Raya Cililin No.123, Bandung Barat</p>
            {{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
        </div>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Order</th>
                    <th>Tanggal Order</th>
                    <th>Supplier</th>
                    <th>Produk</th>
                    <th>Harga Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $no = 1;
                @endphp
                @foreach ([$purchaseOrder] as $order)
                    @php
                        $firstItem = true;
                    @endphp
                    @foreach($order->items as $item)
                        <tr>
                            @if ($firstItem)
                                <td rowspan="{{ $order->items->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->order_number }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->order_date }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->supplier->name ?? '-' }}</td>
                                @php $firstItem = false; @endphp
                            @endif
                            <td>{{ $item->product ? $item->product->name : '-' }}</td>
                            <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $grandTotal += $item->total;
                        @endphp
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-end">Total Keseluruhan</th>
                    <th>Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        {{-- Payment status notification --}}
        <div class="mt-4">
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
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
