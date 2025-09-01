@extends('reports.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Laporan Pengeluaran</h1>
</div>

<div class="card shadow mb-4">
  <div class="card-body">
<form id="filterForm" action="{{ route('reports.expense') }}" method="GET" class="form-inline mb-3">
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
    <span class="mx-1">s/d</span>
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
    {{--<select name="supplier_id" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
        <option value="">Semua Supplier</option>
        @foreach(\App\Models\Supplier::all() as $supplier)
            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
        @endforeach
    </select>--}}
    <a href="{{ route('reports.expense.print', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'supplier_id' => request('supplier_id')]) }}" target="_blank" class="btn btn-primary btn-sm ml-2" style="background-color: #8C52A0; color: white;">
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
        <thead>
          <tr>
            <th>Tanggal Pembayaran</th>
            <th>No Order</th>
            <th>Nama Supplier</th>
            <th>Jumlah yang Dibayar</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $payment)
          <tr>
            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
            <td>{{ $payment->purchaseOrder->order_number }}</td>
            <td>{{ $payment->purchaseOrder->supplier->name }}</td>
            <td class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">Tidak ada data pengeluaran.</td>
          </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-right">Total Pengeluaran</th>
            <th class="text-end">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
