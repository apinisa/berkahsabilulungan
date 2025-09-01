@extends('products.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Produk</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="card shadow mb-4">
        <div class="card-body">
    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        {{-- ID Produk (readonly) --}}
        <div class="form-group col-md-4">
            <label for="product_id">ID Produk</label>
            <input type="text" class="form-control" name="product_id" id="product_id" value="{{ $product_id }}" readonly>
        </div>

        {{-- Supplier --}}
        <div class="form-group col-md-4">
            <label for="supplier_id">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
                <option value="">Pilih Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nama Produk --}}
        <div class="form-group col-md-6">
            <label for="name">Nama Produk</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="nama produk..." required>
        </div>

        {{-- Deskripsi --}}
        <div class="form-group col-md-6">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" placeholder="deskripsi">{{ old('description') }}</textarea>
        </div>

        {{-- Harga & Stok --}}
        <div class="row col-md-10">
            <div class="form-group  col-md-4">
                <label for="price">Harga Beli</label>
                <input type="number" class="form-control" name="price" id="price" value="{{ old('price') }}" step="0.01" required>
            </div>
            {{-- Harga Jual --}}
            <div class="form-group col-md-4">
                <label for="selling_price">Harga Jual</label>
                <input type="number" class="form-control" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" step="0.01">
            </div>
            {{-- Stok --}}
        <div class="form-group col-md-3">
                <label for="stock">Stok</label>
                <input type="number" class="form-control" name="stock" id="stock" value="{{ old('stock') }}" required>
            </div>
        </div>



        {{-- Tombol --}}
        <div class="form-group">
            <button type="submit" class="btn btn-purple" style="background-color: #8C52A0; color: white;">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
    </div>
    </div>
</div>
@endsection
