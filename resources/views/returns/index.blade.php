@extends('returns.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Return / Pengembalian</h1>
</div>
@if (request('start_date') && request('end_date'))
    <div class="alert alert-info">
        Menampilkan data dari <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</strong>
        sampai <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</strong>
    </div>
@endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <!-- Tombol Tambah -->
            <a href="{{ route('returns.create') }}">
                <i class="fa-solid fa-plus mr-1"></i>
                <span class="font-weight-bold">Tambah</span>
            </a>

            <div class="d-flex align-items-center">
                <!-- Form Filter Tanggal -->
                <form id="filterForm" action="{{ route('returns.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1"
                        onchange="document.getElementById('filterForm').submit();">
                    <span class="mx-1">s/d</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1"
                        onchange="document.getElementById('filterForm').submit();">
                </form>

                <!-- Tombol Cetak -->
                <form action="{{ route('returns.print') }}" method="GET" target="_blank" class="d-flex align-items-center ml-2">
                    <input type="hidden" name="start_date" id="print_start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" id="print_end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-sm btn-secondary ml-2 d-flex align-items-center">
                        <i class="fas fa-print mr-1"></i>
                        <span class="font-weight-bold">Cetak</span>
                    </button>
                </form>

                <form action="{{ route('returns.index') }}" method="GET" class="d-flex align-items-center ml-2" role="search">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari...">
                    <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center">
                        <i class="fa fa-search mr-1"></i> Cari
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No Return</th>
                        <th>Tanggal Return</th>
                        <th>Supplier</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($returnOrders as $returnOrder)
                    @php $rowspan = count($returnOrder->items); @endphp
                    @foreach($returnOrder->items as $index => $item)
                        <tr>
                            @if($index == 0)
                                <td rowspan="{{ $rowspan }}">{{ $returnOrder->return_number }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $returnOrder->return_date }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $returnOrder->purchaseOrder->supplier->name }}</td>
                            @endif
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        @if($index == 0)
                                <td rowspan="{{ $rowspan }}">{{ $returnOrder->status }}</td>
                                <td rowspan="{{ $rowspan }}">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('returns.edit', $returnOrder->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('returns.destroy', $returnOrder->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin mau data hapus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>

                                    <!-- Tombol Lihat -->
                                    <a href="{{ route('returns.show', $returnOrder->id) }}" class="btn btn-sm btn-info" title="Detail Return">
                                        <i class="fas fa-info-circle"></i>
                                    </a>

                                    <!-- Tombol Ganti Rugi hanya muncul jika status 'belum diganti' -->
                                    @if($returnOrder->status == 'belum diganti')
                                        <a href="{{ route('returns.gantiRugiView', $returnOrder->id) }}" class="btn btn-warning" title="Ganti Rugi">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
