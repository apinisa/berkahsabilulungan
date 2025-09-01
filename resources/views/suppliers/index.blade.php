@extends('suppliers.layout')


@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Supplier</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <!-- Tombol Tambah -->
        <a href="{{ route('suppliers.create') }}">
            <i class="fa-solid fa-user-plus mr-1"></i>
            <span class="font-weight-bold">Tambah</span>
        </a>

        <!-- Form Search -->
        <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex" role="search">
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
            <th>ID Supplier</th>
            <th>Nama</th>
            <th>Contact Person</th>
            <th>No Telepon</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($suppliers as $index => $supplier)
          <tr>
            <td>{{ $supplier->supplier_id }}</td>
            <td>{{ $supplier->name }}</td>
            <td>{{ $supplier->contact_person }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>{{ $supplier->email }}</td>
            <td>{{ $supplier->address }}</td>
            <td>
              <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin mau data hapus ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
