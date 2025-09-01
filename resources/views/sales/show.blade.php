@extends('sales.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Penjualan - {{ $sale->sale_number }}</h1>

    <div class="card shadow mb-4">
        {{-- Tombol Cetak di kanan atas --}}
        <a href="{{ route('sales.printSingle', $sale->id) }}"
        target="_blank"
        class="btn btn-sm btn-primary position-absolute"
        style="top: 1rem; right: 1rem; background-color: #8C52A0; color: white;">
        <i class="fas fa-print"></i> Cetak Nota
        </a>

        <div class="card-body">
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</p>
            <p><strong>Nama Pembeli:</strong> {{ $sale->buyer_name ?? '-' }}</p>
            <p><strong>Metode Pembayaran:</strong> {{ $sale->payment_method }}</p>
            <p><strong>Catatan:</strong> {{ $sale->note ?? '-' }}</p>

            <hr>

            <h5 class="mb-3">Daftar Produk Terjual</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>ID Produk</th>
                            <th>Nama Produk</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                        @endphp
                        @forelse($sale->items as $item)
                            @php
                                $grandTotal += $item->total;
                            @endphp
                            <tr>
                                <td>{{ $item->product->product_id }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada item penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total Keseluruhan</th>
                            <th class="text-right">Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">Diskon</th>
                            <th class="text-right">Rp{{ number_format($sale->discount, 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">Total Bayar</th>
                            <th class="text-right">Rp{{ number_format($sale->total_payment, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
