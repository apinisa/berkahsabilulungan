@extends('returns.layout')

@section('content')
<div class="container-fluid" id="printableArea">
    <h1 class="h3 mb-4 text-gray-800">Detail Return - {{ $returnOrder->return_number }}</h1>

    <div class="card shadow mb-4">
         {{-- Tombol Cetak di kanan atas --}}
    <a href="{{ route('returns.printSingle', $returnOrder->id) }}"
        target="_blank"
        class="btn btn-sm btn-primary position-absolute"
        style="top: 1rem; right: 1rem; background-color: #8C52A0; color: white;">
         <i class="fas fa-print"></i> Cetak
     </a>
        <div class="card-body">
            <p><strong>No Return:</strong> {{ $returnOrder->return_number }}</p>
            <p><strong>Tanggal Return:</strong> {{ $returnOrder->return_date }}</p>
            <p><strong>Supplier:</strong> {{ $returnOrder->purchaseOrder ? $returnOrder->purchaseOrder->supplier->name : '-' }}</p>
            <p><strong>Purchase Order:</strong> {{ $returnOrder->purchaseOrder ? $returnOrder->purchaseOrder->order_number : '-' }}</p>

            <p>
                <strong>Status:</strong>
                @if($returnOrder->status === 'sudah diganti')
                    <span class="badge badge-success">Sudah Diganti</span>
                    <br>
                   <p> <strong>Tanggal Ganti Rugi:</strong> {{ \Carbon\Carbon::parse($returnOrder->replaced_at)->format('d-m-Y') }}</p>
                @else
                    <span class="badge badge-warning">Belum Diganti</span>
                @endif
            </p>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Jumlah Return</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($returnOrder->items as $item)
                            <tr>
                                <td>{{ $item->product ? $item->product->name : '-' }}</td>
                                <td class="text-right">Rp.{{ number_format($item->product->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">Rp.{{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                            @php $grandTotal += $item->total; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Keseluruhan</th>
                            <th class="text-right font-weight-bold">Rp.{{ number_format($grandTotal, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 no-print" id="action-buttons">
                <a href="{{ route('returns.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
