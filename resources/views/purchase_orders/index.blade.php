@extends('purchase_orders.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Purchase Order / Pembelian</h1>
</div>

@if (request('start_date') && request('end_date'))
    <div class="alert alert-info">
        Menampilkan data dari <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</strong>
        sampai <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</strong>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
     <a href="{{ route('purchase_orders.create') }}">
         <i class="fa-solid fa-plus mr-1"></i>
         <span class="font-weight-bold">Tambah</span>
     </a>

     <div class="d-flex align-items-center">
         <!-- Form Filter Tabel (index) -->
         <form id="filterForm" action="{{ route('purchase_orders.index') }}" method="GET" class="d-flex align-items-center">
             <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
             <span class="mx-1">s/d</span>
             <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
         </form>

         <!-- Form Cetak -->
         <form action="{{ route('purchase_orders.print') }}" method="GET" target="_blank" class="d-flex align-items-center ml-2">
             <input type="hidden" name="start_date" id="print_start_date" value="{{ request('start_date') }}">
             <input type="hidden" name="end_date" id="print_end_date" value="{{ request('end_date') }}">
             <button type="submit" class="btn btn-sm btn-secondary ml-2 d-flex align-items-center">
                 <i class="fa-solid fa-print mr-1"></i>
                 <span class="font-weight-bold">Cetak</span>
             </button>
         </form>

         <!-- Form Search -->
            <form action="{{ route('purchase_orders.index') }}" method="GET" class="d-flex align-items-center ml-2" role="search">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari...">
            <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center">
                <i class="fa fa-search mr-1"></i> Cari
            </button>
            </form>
     </div>
 </div>


    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="purchaseOrderTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                <th>No Order</th>
                <th>Tanggal Order</th>
                <th>Supplier</th>
                <th>Produk</th>
                <th>Jumlah</th>
                {{--<th>Total</th>--}}
                <th>Total</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $order)
                        @php $rowspan = count($order->items); @endphp
                        @foreach($order->items as $index => $item)
                            <tr>
                                @if($index == 0)
                                    <td rowspan="{{ $rowspan }}">{{ $order->order_number }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $order->order_date }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $order->supplier ? $order->supplier->name : '-' }}</td>
                                @endif
                                <td>{{ $item->product ? $item->product->name : '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                {{-- <td>Rp.{{ number_format($item->total, 0, ',', '.') }}</td> --}}
                                @if($index == 0)
                                <td rowspan="{{ $rowspan }}">
                                    Rp.{{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $rowspan }}">
                                @if($order->installment_target == 0)
                                    @if($order->installmentCount > 0)
                                        <span class="text-success font-weight-bold">
                                            ✅ Lunas
                                        </span>
                                    @else
                                        <span class="text-warning font-weight-bold">
                                            ⚠️ Belum Dibayar
                                        </span>
                                    @endif
                                @elseif($order->remainingAmount <= 0 && $order->installmentCount > 0)
                                    <span class="text-success font-weight-bold">
                                        ✅ Lunas — Total {{ $order->installmentCount }} kali cicilan
                                    </span>
                                @else
                                    <span class="text-warning font-weight-bold">
                                        ⚠️ Belum Lunas — Cicilan ke-{{ $order->installmentCount }} dari {{ $order->installment_target }}
                                    </span>
                                @endif
                            </td>

                                    <td rowspan="{{ $rowspan }}">

                                        <!-- Tombol Edit -->
                                        <a href="{{ route('purchase_orders.edit', $order->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('purchase_orders.destroy', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin mau data hapus ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('purchase_orders.show', $order->id) }}" class="btn btn-sm btn-info" title="Detail Pembelian">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
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

<script>
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const printStartDate = document.getElementById('print_start_date');
    const printEndDate = document.getElementById('print_end_date');

    startDateInput.addEventListener('change', function() {
        printStartDate.value = this.value;
    });

    endDateInput.addEventListener('change', function() {
        printEndDate.value = this.value;
    });
</script>
@endsection
