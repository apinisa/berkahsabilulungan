@extends('reports.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Laporan Pemasukan</h1>
</div>


<div class="card shadow mb-4">
  <div class="card-body">
<form id="filterForm" action="{{ route('reports.income') }}" method="GET" class="form-inline mb-3">
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
    <span class="mx-1">s/d</span>
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
    {{--<select name="payment_method" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
        <option value="">Semua Metode</option>
        <option value="Tunai" {{ request('payment_method') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
        <option value="Transfer" {{ request('payment_method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
    </select>--}}
    <a href="{{ route('reports.income.print', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'payment_method' => request('payment_method')]) }}" target="_blank" class="btn btn-primary btn-sm ml-2" style="background-color: #8C52A0; color: white;">
        <i class="fas fa-print"></i> Cetak</a>
</form>

@if(request('start_date') && request('end_date'))
  <div class="alert alert-success">
      Menampilkan data dari <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}</strong>
      sampai <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}</strong>.
  </div>
@endif

    <div class="table-responsive">
      <table class="table table-bordered" width="100%" cellspacing="0">
        <thead class="text-center">
          <tr>
            <th>Tanggal Transaksi</th>
            <th>No Penjualan</th>
            <th>Nama Pembeli</th>
            <th>Total Pembayaran</th>
            <th>Metode Pembayaran</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $sale)
          <tr>
            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</td>
            <td>{{ $sale->sale_number }}</td>
            <td>{{ $sale->buyer_name ?? '-' }}</td>
            <td class="text-end">Rp {{ number_format($sale->total_payment, 0, ',', '.') }}</td>
            <td class="text-center">{{ $sale->payment_method }}</td>
            <td>{{ $sale->note ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">Tidak ada data pemasukan.</td>
          </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-right">Total Pemasukan</th>
            <th class="text-end">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</th>
            <th colspan="2"></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
