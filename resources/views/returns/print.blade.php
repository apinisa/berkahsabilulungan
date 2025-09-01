<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Return Order</title>
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

        <h2 class="text-center mb-4">Rekapan Return Order</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Return</th>
                    <th>Tanggal Return</th>
                    <th>Supplier</th>
                    <th>Produk</th>
                    <th>Harga Produk</th>
                    <th>Jumlah Return</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $no = 1;
                @endphp

                @foreach ($returnOrders as $order)
                    @php
                        $firstItem = true;
                    @endphp
                    @foreach($order->items as $item)
                        <tr>
                            @if ($firstItem)
                                <td rowspan="{{ $order->items->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->return_number }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->purchaseOrder->supplier->name ?? '-' }}</td>
                               
                            @endif
                            <td>{{ $item->product ? $item->product->name : '-' }}</td>
                            <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>

                            @if ($firstItem)
                                <td rowspan="{{ $order->items->count() }}">{{ $order->status }}</td>
                                 @php $firstItem = false; @endphp
                            @endif

                        </tr>

                        @php
                            $grandTotal += $item->total;
                        @endphp
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Total Keseluruhan</th>
                    <th colspan="8">Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
