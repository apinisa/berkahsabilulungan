@extends('payments.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Pembayaran PO</h1>
</div>

@if (request('start_date') && request('end_date'))
    <div class="alert alert-info">
        Menampilkan data dari <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</strong>
        sampai <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</strong>
    </div>
@endif

<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('payments.create') }}">
      <i class="fas fa-plus mr-1"></i>
      <span class="font-weight-bold">Tambah</span>
    </a>

    <div class="d-flex align-items-center">
        <form id="filterForm" action="{{ route('payments.index') }}" method="GET" class="d-flex align-items-center">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
            <span class="mx-1">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm mx-1" onchange="document.getElementById('filterForm').submit();">
        </form>

        <form action="{{ route('payments.print') }}" method="GET" target="_blank" class="d-flex align-items-center ml-2">
            <input type="hidden" name="start_date" id="print_start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" id="print_end_date" value="{{ request('end_date') }}">
            <button type="submit" class="btn btn-sm btn-secondary ml-2 d-flex align-items-center">
                <i class="fa fa-print mr-1"></i>
                <span class="font-weight-bold">Cetak</span>
            </button>
        </form>

        <form action="{{ route('payments.index') }}" method="GET" class="d-flex align-items-center ml-2" role="search">
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
        <thead>
          <tr>
            {{--<th>ID Pembayaran</th>--}}
            <th>No Order</th>
            <th>Supplier</th>
            <th>Tanggal Bayar</th>
            <th>Jumlah Bayar</th>
            <th>Sisa</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $payment)
          <tr>
            {{--<td>{{ $payment->payment_number }}</td>--}}
            <td>{{ $payment->purchaseOrder->order_number }}</td>
            <td>{{ $payment->purchaseOrder->supplier->name }}</td>
            <td>{{ $payment->payment_date }}</td>
            {{--<td>{{ $payment->purchaseOrder->paid_off_date }}</td>--}}
            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($payment->remaining_amount, 0, ',', '.') }}</td>
            <td>{{ $payment->status }}</td>

            <td>
                @php
                    $po = $payment->purchaseOrder;
                    $isPaidOff = $payment->remaining_amount == 0;
                @endphp
               @if(strtolower($payment->status) === 'lunas')
                    <button class="btn btn-secondary btn-sm" title="Sudah lunas" disabled>
                        <i class="fas fa-money-bill-1"></i>
                    </button>
                @else
                    <a href="{{ route('payments.edit', $payment->purchase_order_id) }}" class="btn btn-primary btn-sm" title="Lunasi">
                        <i class="fas fa-money-bill-1"></i>
                    </a>
                @endif
                <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info btn-sm" title="Detail">
                    <i class="fas fa-info-circle"></i>
                </a>
                {{-- Tambahkan tombol hapus jika perlu --}}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
