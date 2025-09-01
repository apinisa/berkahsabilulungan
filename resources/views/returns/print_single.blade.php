<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Order - {{ $returnOrder->return_number }}</title>
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
                    <th>No Return</th>
                    <th>Tanggal Return</th>
                    <th>Supplier</th>
                    <th>Produk</th>
                    <th>Harga Produk</th>
                    <th>Jumlah Return</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $no = 1;
                @endphp
                @foreach ([$returnOrder] as $order)
                    @php
                        $firstItem = true;
                    @endphp
                    @foreach($order->items as $item)
                        <tr>
                            @if ($firstItem)
                                <td rowspan="{{ $order->items->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->return_number }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->return_date }}</td>
                                <td rowspan="{{ $order->items->count() }}">{{ $order->purchaseOrder->supplier->name ?? '-' }}</td>
                                @php $firstItem = false; @endphp
                            @endif
                            <td>{{ $item->product ? $item->product->name : '-' }}</td>
                            <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
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
                <tr>
                    <th colspan="7" class="text-end">Status Ganti Rugi</th>
                    <th>
                        @if($returnOrder->status === 'sudah diganti')
                            Sudah Diganti<br>
                            <small>Tgl Ganti: {{ \Carbon\Carbon::parse($returnOrder->replaced_at)->format('d-m-Y') }}</small>
                        @else
                            Belum Diganti
                        @endif
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
