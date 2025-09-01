<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Purchase Order</title>
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

        <h2 class="text-center mb-4">Rekapan Pembelian / Order</h2>

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
                    <th>Total Harga</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $no = 1;
                    $grandTotalAll = 0;
                @endphp

                @foreach ($purchaseOrders as $order)
                    @php
                        $firstItem = true;
                        $grandTotalAll += $order->grand_total;
                    @endphp
                    @foreach($order->items as $item)
                        <tr>
                            @if ($firstItem)
                                <td rowspan="{{ $order->items->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->order_number }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->supplier->name ?? '-' }}</td>
                            @endif
                            <td>{{ $item->product ? $item->product->name : '-' }}</td>
                            <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                            @if ($firstItem)
                                @php
                                    $isPaidOff = $order->grand_total - $order->payments->sum('amount') == 0;
                                @endphp
                                <td rowspan="{{ $order->items->count() }}">Rp.{{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                <td rowspan="{{ $order->items->count() }}">
                                    @if($isPaidOff)
                                        ✅ Lunas
                                    @elseif($order->payments->count() == 0)
                                        ❌ Belum Dibayar
                                    @else
                                        ⚠️ Belum Lunas
                                    @endif
                                </td>
                                @php $firstItem = false; @endphp
                            @endif
                        </tr>
                    @endforeach
                        @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-right">Total Keseluruhan</th>
                    <th colspan="9">Rp{{ number_format($grandTotalAll, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
