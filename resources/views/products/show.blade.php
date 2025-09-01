@extends('products.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Produk - {{ $product->supplier->name ?? '-' }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <p><strong>Supplier:</strong> {{ $product->supplier->name ?? '-' }}</p>
            <p><strong>Alamat:</strong> {{ $product->supplier->address ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $product->supplier->phone ?? '-' }}</p>

            <hr>

            <h5 class="mb-3">Daftar Produk</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>ID Produk</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Harga Beli</th>
                            <th class="text-right">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $products = $product->supplier->products ?? collect();
                        @endphp

                        @forelse($products as $p)
                            <tr>
                                <td>{{ $p->product_id }}</td>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->description }}</td>
                                <td class="text-right">Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                                <td class="text-right">Rp{{ number_format($p->selling_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
