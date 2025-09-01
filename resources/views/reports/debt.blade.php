@extends('reports.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Laporan Hutang Usaha</h1>
</div>

<form id="filterForm" action="{{ route('reports.debt') }}" method="GET" class="form-inline mb-3">
    <select name="supplier_id" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
        <option value="">Semua Supplier</option>
        @foreach(\App\Models\Supplier::all() as $supplier)
            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
        @endforeach
    </select>
</form>

<div class="card shadow mb-4">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>No. PO</th>
            <th>Nama Supplier</th>
            <th>Total PO</th>
            <th>Sudah Dibayar</th>
            <th>Sisa Hutang</th>
          </tr>
        </thead>
        <tbody>
          @forelse($purchaseOrders as $po)
          <tr>
            <td>{{ $po->order_number }}</td>
            <td>{{ $po->supplier->name }}</td>
            <td class="text-end">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($po->payments_sum_amount ?? 0, 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($po->grand_total - ($po->payments_sum_amount ?? 0), 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">Tidak ada data hutang usaha.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
