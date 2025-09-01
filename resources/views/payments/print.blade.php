<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Pembayaran PO</title>
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

        <h2 class="text-center mb-4">Rekapan Pembayaran PO</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Order</th>
                    <th>Supplier</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah Bayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $totalAmount = 0;
                    $totalPaid = 0;
                    $totalUnpaid = 0;
                @endphp
                @foreach($payments as $payment)
                @php
                    $totalAmount += $payment->amount;
                    if(strtolower($payment->status) == 'lunas'){
                        $totalPaid += $payment->amount;
                    } else {
                        $totalUnpaid += $payment->remaining_amount;
                    }
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $payment->purchaseOrder->order_number }}</td>
                    <td>{{ $payment->purchaseOrder->supplier->name }}</td>
                    <td>{{ $payment->payment_date }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($payment->remaining_amount, 0, ',', '.') }}</td>
                    <td>{{ $payment->status }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total</th>
                    <th>Rp {{ number_format($totalPaid, 0, ',', '.') }}</th>
                    <th colspan="7">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">Total Keseluruhan</th>
                    <th colspan="7">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
