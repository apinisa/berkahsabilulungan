<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ringkasan (Laba Bersih) - Cetak</title>
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

        <h2 class="text-center mb-4">Laporan Ringkasan (Laba Bersih)</h2>

        @if($startDate && $endDate)
            <p>Menampilkan data dari <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
            sampai <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>.</p>
        @endif

        @if(count($transactions))
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Pemasukan (+)</th>
                        <th>Pengeluaran (-)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalIncome = 0;
                        $totalExpense = 0;
                    @endphp
                    @foreach ($transactions as $trx)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $trx->tipe }}</td>
                            @if ($trx->tipe == 'Penjualan')
                                @php $totalIncome += $trx->jumlah; @endphp
                                <td class="text-success">+ Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                                <td></td>
                            @else
                                @php $totalExpense += $trx->jumlah; @endphp
                                <td></td>
                                <td class="text-danger">- Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total</th>
                        <th class="text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</th>
                        <th class="text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">Laba Bersih</th>
                        <th colspan="2" class="text-primary">Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        @else
            <p>Belum ada data transaksi.</p>
        @endif
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
