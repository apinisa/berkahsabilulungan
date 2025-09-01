@extends('suppliers.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Supplier</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('suppliers.update', $supplier->supplier_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group" style="width: 50%;">
                    <label for="name">Nama Supplier</label>
                    <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                </div>

                <div class="form-group" style="width: 50%;">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" value="{{ $supplier->contact_person }}" required>
                </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label for="phone">No Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}" required>
                </div>

                <div class="form-group" style="width: 50%;">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}" required>
                </div>
            </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ $supplier->address }}</textarea>
                </div>

                <button type="submit" class="btn" style="background-color: #8C52A0; color: white;">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
