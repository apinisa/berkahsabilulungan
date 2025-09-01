<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - {{ $sale->sale_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .nota-container { width: 80mm; margin: auto; }
        .nota-header { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        hr { border: 0.5px dashed #000; }
    </style>
</head>
<body onload="window.print()">
<div class="nota-container">
    <div class="nota-header">
            <h1>Toko Berkah Sabilulungan</h1>
            <p>Jl. Raya Cililin No.123, Bandung Barat</p>
    </div>

    <hr>

    <table>
        <tr>
            <td>Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Pembeli</td>
            <td>: {{ $sale->buyer_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td>: {{ $sale->payment_method }}</td>
        </tr>
    </table>

    <hr>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Sub</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <table>
        <tr>
            <td colspan="3"><strong>Subtotal</strong></td>
            <td class="text-right">Rp{{ number_format($sale->total_payment + $sale->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Diskon</strong></td>
            <td class="text-right">Rp{{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td class="text-right">Rp{{ number_format($sale->total_payment, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>
    <table>
        <tr>
            <td colspan="3"><strong>Catatan Pembeli</strong></td>
            <td class="text-right">{{ $sale->note ?? '-' }}</td>
        </tr>
    </table>
    <hr>

    <div class="text-center">
        <p>Terima kasih!</p>
    </div>
</div>
</body>
</html>
