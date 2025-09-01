@extends('reports.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Laporan Pemasukan & Pengeluaran</h1>

    <!-- Filter tanggal -->
    <form method="GET" action="{{ route('reports.index') }}" class="mb-4">
        <div class="form-row">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Table laporan -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Transaksi</th>
                            <th>Keterangan</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp

                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction['tanggal'])->format('d-m-Y') }}</td>
                                <td>{{ $transaction['jenis'] }}</td>
                                <td>{{ $transaction['keterangan'] }}</td>
                                <td class="text-right">Rp {{ number_format($transaction['jumlah'], 0, ',', '.') }}</td>
                            </tr>
                            @php $total += $transaction['jumlah']; @endphp
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total</th>
                            <th class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
