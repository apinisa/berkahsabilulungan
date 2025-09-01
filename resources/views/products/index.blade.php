@extends('products.layout')

@section('content')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Produk</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <!-- Tombol Tambah -->
        <a href="{{ route('products.create') }}">
            <i class="fa-solid fa-plus mr-1"></i>
            <span class="font-weight-bold">Tambah</span>
        </a>

        <!-- Form Search -->
        <form action="{{ route('products.index') }}" method="GET" class="d-flex" role="search">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari...">
            <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center">
                <i class="fa fa-search mr-1"></i> Cari
            </button>
        </form>
    </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="supplierTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Supplier</th>
            <th>ID Produk</th>
            <th>Nama</th>
            {{--<th>Deskripsi</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>--}}
            <th>Stok</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            @foreach($groupedProducts as $supplierId => $products)
                @php
                    $rowspan = count($products);
                    $supplierName = $products->first()->supplier->name ?? '-'; // buat jaga-jaga kalau supplier null
                @endphp

                @foreach($products as $index => $product)
                    <tr>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}">{{ $supplierName }}</td>
                        @endif
                        <td>{{ $product->product_id }}</td>
                        <td>{{ $product->name }}</td>
                        {{--<td>{{ $product->description }}</td>
                        <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($product->selling_price, 0, ',', '.') }}</td>--}}
                        <td>{{ $product->stock }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('products.destroy', $product->product_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin mau data hapus ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-info" title="Detail Produk">
                                            <i class="fas fa-info-circle"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
