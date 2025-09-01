<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemasukan - Cetak</title>
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

        <h2 class="text-center mb-4">Laporan Pemasukan</h2>

        @if($startDate && $endDate)
        <p>Menampilkan data dari <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
        sampai <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>.</p>
        @endif

        @if(count($sales))
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Transaksi</th>
                        <th>No. Penjualan</th>
                        <th>Nama Pembeli</th>
                        <th>Total Pembayaran</th>
                        <th>Metode Pembayaran</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</td>
                        <td>{{ $sale->sale_number }}</td>
                        <td>{{ $sale->buyer_name ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($sale->total_payment, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $sale->payment_method }}</td>
                        <td>{{ $sale->note ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total Pemasukan</th>
                        <th class="text-end">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        @else
            <p class="text-center">Tidak ada data pemasukan.</p>
        @endif
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
