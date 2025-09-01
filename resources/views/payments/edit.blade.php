@extends('payments.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit (Pelunasan) Pembayaran</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('payments.update', $po->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">

                <div class="mb-3">
                    <label>Nomor PO</label>
                    <input type="text" class="form-control" value="{{ $po->order_number }}" disabled>
                </div>

                <div class="mb-3">
                    <label>Total Sudah Dibayar</label>
                    <input type="text" class="form-control" value="Rp {{ number_format($totalPaid, 0, ',', '.') }}" disabled>
                </div>

                <div class="mb-3">
                    <label>Sisa Pembayaran</label>
                    <input type="text" class="form-control" value="Rp {{ number_format($sisa, 0, ',', '.') }}" disabled>
                    <input type="hidden" name="remaining_amount" value="{{ $sisa }}">
                </div>


                <div class="mb-3">
                    <label>Tanggal Pelunasan</label>
                    <input type="date" name="payment_date" class="form-control" required>
                </div>


                <div class="mb-3">
                    <label>Jumlah yang Dibayar</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="Contoh: 50000" required>
                </div>

                <button type="submit" class="btn btn-success" style="background-color: #8C52A0; color: white;">
                    <i class="fas fa-save"></i>
                    Simpan</button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
