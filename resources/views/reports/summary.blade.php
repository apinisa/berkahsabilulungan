@extends('reports.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Laporan Ringkasan (Laba Bersih)</h1>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
<form id="filterForm" action="{{ route('reports.summary') }}" method="GET" class="form-inline mb-3">
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
    <span class="mx-1">s/d</span>
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
    <a href="{{ route('reports.summary.print', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" target="_blank" class="btn btn-primary btn-sm ml-2" style="background-color: #8C52A0; color: white;">
        <i class="fas fa-print"></i> Cetak</a>
</form>

@if(count($transactions))
    @if($startDate && $endDate)
        <div class="alert alert-success">
            Menampilkan data dari <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
            sampai <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>.
        </div>
    @endif

    {{-- Tabel Transaksi Gabungan

        <div class="card-header"><strong>Detail Transaksi</strong></div>--}}

            <div class="table-responsive">
                @php
                    $totalIncome = 0;
                    $totalExpense = 0;
                @endphp
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
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">Belum ada data transaksi.</div>
@endif
@endsection
