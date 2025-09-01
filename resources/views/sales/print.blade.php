<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Penjualan</title>
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

    <h2 class="text-center mb-4">Rekapan Penjualan</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No Penjualan</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Metode</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total Item</th>
                <th>Total Bayar</th>
            </tr>
        </thead>
        <tbody>
        @php $grandTotal = 0; @endphp
        @foreach ($sales as $index => $sale)
            @php
                $firstItem = true;
                $grandTotal += $sale->total_payment;
            @endphp
            @foreach ($sale->items as $item)
                <tr>
                    @if ($firstItem)
                        <td rowspan="{{ $sale->items->count() }}">{{ $index + 1 }}</td>
                        <td rowspan="{{ $sale->items->count() }}">{{ $sale->sale_number }}</td>
                        <td rowspan="{{ $sale->items->count() }}">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        <td rowspan="{{ $sale->items->count() }}">{{ $sale->buyer_name ?? '-' }}</td>
                        <td rowspan="{{ $sale->items->count() }}">{{ $sale->payment_method }}</td>
                    @endif
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                    @if ($firstItem)
                        <td rowspan="{{ $sale->items->count() }}">Rp{{ number_format($sale->total_payment, 0, ',', '.') }}</td>
                        @php $firstItem = false; @endphp
                    @endif
                </tr>
            @endforeach
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="9" class="text-right">Total Keseluruhan</th>
                <th>Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    window.print();
</script>
</body>
</html>
