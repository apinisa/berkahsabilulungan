@extends('sales.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Penjualan</h1>
</div>

@if (request('start_date') && request('end_date'))
    <div class="alert alert-info">
        Menampilkan data dari <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</strong>
        sampai <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</strong>
    </div>
@endif

<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('sales.create') }}">
      <i class="fas fa-plus mr-1"></i>
      <span class="font-weight-bold">Tambah</span>
    </a>

    <div class="d-flex align-items-center">
        <form id="filterForm" action="{{ route('sales.index') }}" method="GET" class="d-flex align-items-center">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
            <span class="mx-1">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
        </form>

        <!-- Tombol Cetak -->
                <form action="{{ route('sales.print') }}" method="GET" target="_blank" class="d-flex align-items-center ml-2">
                    <input type="hidden" name="start_date" id="print_start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" id="print_end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-sm btn-secondary ml-2 d-flex align-items-center">
                        <i class="fas fa-print mr-1"></i>
                        <span class="font-weight-bold">Cetak</span>
                    </button>
                </form>

        <form action="{{ route('sales.index') }}" method="GET" class="d-flex align-items-center ml-2" role="search">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari...">
            <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center">
                <i class="fa fa-search mr-1"></i> Cari
            </button>
        </form>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" width="100%" cellspacing="0">
        <thead class="text-center">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No Penjualan</th>
            <th>Pembeli</th>
            <th>Total Bayar</th>
            <th>Metode</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $sale)
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</td>
            <td>{{ $sale->sale_number }}</td>
            <td>{{ $sale->buyer_name ?? '-' }}</td>
            <td class="text-end">Rp {{ number_format($sale->total_payment, 0, ',', '.') }}</td>
            <td class="text-center">{{ $sale->payment_method }}</td>
            <td class="text-center">
                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info btn-sm" title="Detail">
                    <i class="fas fa-info-circle"></i>
                </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">Belum ada data penjualan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
