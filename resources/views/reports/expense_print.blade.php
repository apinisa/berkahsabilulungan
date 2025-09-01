<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran - Cetak</title>
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

        <h2 class="text-center mb-4">Laporan Pengeluaran</h2>

        @if($startDate && $endDate)
            <p>Menampilkan data dari
                <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
                sampai
                <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>.
            </p>
        @endif


        @if(count($payments))
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Nama Supplier</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Jumlah yang Dibayar</th>
                        <th>Keterangan Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->purchaseOrder->order_number }}</td>
                        <td>{{ $payment->purchaseOrder->supplier->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                        <td class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>{{ $payment->purchaseOrder->note ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Pengeluaran</th>
                        <th class="text-end">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        @else
            <p class="text-center">Tidak ada data pengeluaran.</p>
        @endif
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
