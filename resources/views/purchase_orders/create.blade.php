@extends('purchase_orders.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Purchase Order</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('purchase_orders.store') }}" method="POST" id="purchase-form">
                @csrf

                <div class="row">
                    <!-- Nomor Order -->
                    <div class="form-group col-md-4">
                        <label for="order_number" class="form-label">Nomor Order</label>
                        <input type="text" name="order_number" class="form-control" value="{{ $orderNumber }}" readonly required>
                    </div>

                    <!-- Tanggal Order -->
                    <div class="form-group col-md-4">
                        <label for="order_date" class="form-label">Tanggal Order</label>
                        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="installment_target">Target Jumlah Cicilan</label>
                       <input type="number" name="installment_target" class="form-control" value="{{ old('installment_target', $purchaseOrder->installment_target ?? '') }}">
                    </div>
                </div>

                <!-- Supplier -->
                <div class="form-group" style="width: 30%;">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Produk -->
                <div class="form-group d-flex align-items-end" style="width: 42%;">
                    <div style="flex: 1;">
                        <label for="product_id" class="form-label">Produk</label>
                        <select id="product_id" class="form-control">
                            <option value="">-- Pilih Produk --</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="visibility: hidden;">.</label>
                        <button type="button" class="btn" style="background-color: #8C52A0; color: white;" id="add-product">
                            Tambah Produk
                        </button>
                    </div>
                </div>


                <!-- Tabel Produk -->
                <h5>Daftar Produk</h5>
                <table class="table table-bordered" id="product-table">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Keseluruhan</th>
                            <th colspan="2">
                                <input type="text" name="grand_total" id="grand_total" class="form-control" readonly>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Tombol Submit -->
                <button type="submit" class="btn" style="background-color: #8C52A0; color: white;">
                    <i class="fas fa-save"></i>
                    Simpan </button>
                <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let selectedProducts = {};

    $('#add-product').click(function () {
        const selected = $('#product_id option:selected');
        const productId = selected.val();
        const productName = selected.text();
        const productPrice = selected.data('price');

        if (!productId || selectedProducts[productId]) {
            alert('Produk belum dipilih atau sudah ditambahkan!');
            return;
        }

        selectedProducts[productId] = true;

        $('#product-table tbody').append(`
            <tr data-id="${productId}">
                <td>
                    ${productName}
                    <input type="hidden" name="products[]" value="${productId}">
                </td>
                <td>
                    <input type="text" class="form-control price" value="${productPrice}" readonly>
                </td>
                <td>
                    <input type="number" name="quantities[]" class="form-control quantity" min="1" value="1">
                </td>
                <td>
                    <input type="text" class="form-control total" value="${productPrice}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-product">Hapus</button>
                </td>
            </tr>
        `);

        updateGrandTotal();
    });

    $(document).on('input', '.quantity', function () {
        const row = $(this).closest('tr');
        const price = parseFloat(row.find('.price').val());
        const quantity = parseInt($(this).val());
        const total = price * quantity;
        row.find('.total').val(total);
        updateGrandTotal();
    });

    $(document).on('click', '.remove-product', function () {
        const row = $(this).closest('tr');
        const productId = row.data('id');
        delete selectedProducts[productId];
        row.remove();
        updateGrandTotal();
    });

    function updateGrandTotal() {
        let grandTotal = 0;
        $('.total').each(function () {
            grandTotal += parseFloat($(this).val()) || 0;
        });
        $('#grand_total').val(grandTotal);
    }

    $('#supplier_id').change(function () {
        const supplierId = $(this).val();

        // Reset produk yang sudah dipilih
        selectedProducts = {};
        $('#product-table tbody').empty();
        $('#grand_total').val('');

        if (!supplierId) {
            $('#product_id').html('<option value="">-- Pilih Produk --</option>');
            return;
        }

        $.ajax({
            url: `/get-products-by-supplier/${supplierId}`,
            method: 'GET',
            success: function (products) {
                let options = '<option value="">-- Pilih Produk --</option>';
                products.forEach(product => {
                    options += `<option value="${product.product_id}" data-price="${product.price}">${product.name}</option>`;
                });
                $('#product_id').html(options);
            }
        });
    });
</script>
@endsection
