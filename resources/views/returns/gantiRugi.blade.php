@extends('returns.layout')

@section('content')
<div class="container-fluid" id="printableArea">
    <h1 class="h3 mb-4 text-gray-800">Ganti Rugi - {{ $returnOrder->return_number }}</h1>

    <div class="card shadow mb-4">
        <div class="card">
            <div class="card-header">
                <strong>No Return: {{ $returnOrder->return_number }}</strong>
            </div>
            <div class="card-body">
                <p><strong>Tanggal Return:</strong> {{ $returnOrder->return_date }}</p>
                <p><strong>Supplier:</strong> {{ $returnOrder->purchaseOrder->supplier->name }}</p>
                <p><strong>Status:</strong> {{ $returnOrder->status }}</p>

                <h4>Produk yang Dikembalikan:</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <form action="{{ route('returns.gantiRugi', $returnOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <div class="form-group col-md-4">
                        <label for="status">Status Ganti Rugi</label>
                        <select class="form-control" id="status" name="status">
                            <option value="sudah diganti" {{ $returnOrder->status == 'sudah diganti' ? 'selected' : '' }}>Sudah Diganti</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="replaced_at">Tanggal Ganti Rugi</label>
                        <input type="date" class="form-control" id="replaced_at" name="replaced_at" value="{{ old('replaced_at', $returnOrder->replaced_at ? $returnOrder->replaced_at->format('Y-m-d') : now()->format('Y-m-d')) }}"required>

                    </div>

                    <button type="submit" class="btn" style="background-color: #8C52A0; color: white;">
                        <i class="fas fa-save"></i>
                        Konfirmasi</button>
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
