@extends('payments.layout')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Pembayaran Purchase Order</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Tanggal Pembayaran -->
                    <div class="form-group col-md-4">
                        <label for="payment_date">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>

                    <!-- Pilih Purchase Order -->
                    <div class="form-group col-md-6 d-flex align-items-end">
                        <div style="flex: 1;">
                            <label for="purchase_id" class="form-label">Pilih Purchase Order</label>
                            <select name="purchase_order_id" id="purchase_id" class="form-control" required>
                                <option value="">-- Pilih PO --</option>
                                @foreach ($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">
                                        {{ $po->order_number }} - {{ $po->supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="visibility: hidden;">.</label>
                            <button type="button" class="btn" style="background-color: #8C52A0; color: white;" id="load-po-details">
                                <i class="fa fa-eye"></i> Pilih PO
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Detail PO -->
                <div id="po-details" style="margin-top: 20px;">
                    <h5>Detail Produk</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                            <tbody id="product-details-body">
                                <!-- Diisi via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total Keseluruhan</th>
                                    <th><input type="text" id="total" class="form-control" readonly></th>
                                </tr>
                            </tfoot>
                        </table>
                        <small class="form-text text-muted">Masukkan jumlah pembayaran.</small>

                    <div class="row">
                        <!-- Jumlah Bayar -->
                        <div class="form-group col-md-4">
                            <label for="amount">Jumlah Dibayar</label>
                            <input type="number" name="amount" id="amount" class="form-control" required min="0" step="0.01">
                            <div class="invalid-feedback">Jumlah pembayaran melebihi total PO!</div>
                        </div>

                        <!-- Sisa/Kelebihan -->
                        <div class="form-group col-md-4">
                            <label for="difference">Sisa</label>
                            <input type="text" id="difference" class="form-control" readonly>
                            <input type="hidden" name="remaining_amount" id="remaining_amount" value="0">
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="mt-3">
                    <button type="submit" class="btn" style="background-color: #8C52A0; color: white;">Simpan</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    let grandTotal = 0;

    $('#load-po-details').click(function () {
        const purchaseId = $('#purchase_id').val();
        if (!purchaseId) {
            alert('Silakan pilih Purchase Order terlebih dahulu!');
            return;
        }

        $('#load-po-details').prop('disabled', true).html('Memuat...');

        $.ajax({
            url: `/payments/purchase-orders/${purchaseId}/details`,
            method: 'GET',
success: function (data) {
    console.log('DATA DITERIMA:', data); // debug

    let rows = '';

    if (data.items && data.items.length > 0) {
        data.items.forEach((item) => {
            rows += `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${formatCurrency(item.price)}</td>
                    <td>${item.quantity}</td>
                    <td>${formatCurrency(item.total)}</td>
                </tr>`;
        });

        $('#product-details-body').html(rows);
        $('#total').val(formatCurrency(data.grand_total));
    } else {
        $('#product-details-body').html('<tr><td colspan="4" class="text-center text-muted">Tidak ada data produk.</td></tr>');
        $('#total').val('Rp 0');
    }

                $('#po-details').show();
                $('#load-po-details').prop('disabled', false).html('<i class="fa fa-eye"></i> Pilih PO');

function parseCurrency(value) {
    if (!value) return 0;
    // Since formatCurrency now returns decimal string, just parseFloat
    return parseFloat(value) || 0;
}

// Update difference when amount input changes
$('#amount').off('input').on('input', function () {
    let amount = parseFloat($(this).val()) || 0;
    let totalValue = parseCurrency($('#total').val());
    const diff = totalValue - amount;
    $('#difference').val(formatCurrency(diff));
    $('#remaining_amount').val(diff.toFixed(2));

    if (amount > totalValue) {
        $(this).addClass('is-invalid');
    } else {
        $(this).removeClass('is-invalid');
    }
});
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Gagal mengambil detail PO.');
                $('#load-po-details').prop('disabled', false).html('<i class="fa fa-eye"></i> Pilih PO');
            }
        });
    });

function formatCurrency(value) {
    let number = Number(value);
    return number.toFixed(2);
}
</script>
@endsection
