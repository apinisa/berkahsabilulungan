@extends('returns.layout')

@section('content')
<div class="container-fluid">
    <h2>Edit Return Order</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="card mt-4">
    <div class="card-body">
    <form action="{{ route('returns.update', $returnOrder->id) }}" method="POST" id="returnForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="return_number">Nomor Return</label>
            <input type="text" name="return_number" class="form-control" value="{{ $returnOrder->return_number }}" readonly required>
        </div>

        <div class="form-group">
            <label for="return_date">Tanggal Return</label>
            <input type="date" name="return_date" class="form-control" value="{{ old('return_date', $returnOrder->return_date) }}" required>
        </div>

        <div class="form-group">
            <label for="purchase_order_id">Purchase Order</label>
            <select name="purchase_order_id" class="form-control" id="purchase_order_id" required>
                <option value="">-- Pilih PO --</option>
                @foreach ($purchaseOrders as $po)
                    <option value="{{ $po->id }}" {{ $returnOrder->purchase_order_id == $po->id ? 'selected' : '' }}>
                        {{ $po->order_number }} - {{ $po->supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="product_id">Produk</label>
            <select id="product_select" class="form-control">
                <option value="">-- Pilih PO terlebih dahulu --</option>
            </select>
        </div>

        <div class="row">
            <div class="form-group col-md-3">
                <label for="return_qty">Jumlah Return</label>
                <input type="number" id="return_qty" placeholder="Jumlah Return" class="form-control" min="1">
            </div>
            <div class="form-group col-md-3">
                <label for="return_reason">Alasan</label>
                <input type="text" id="return_reason" placeholder="Alasan" class="form-control">
            </div>
             <div class="form-group col-md-4 d-flex align-items-end">
                <label class="form-label" style="visibility: hidden;">.</label>
                <button type="button" class="btn" style="background-color: #8C52A0; color: white;" id="add_product_btn">
                Tambah Produk
                </button>
            </div>
        </div>
        {{--<button type="button" class="btn btn-sm btn-primary mt-2" style="background-color: #8C52A0;" id="add_product_btn">Tambah Produk</button>--}}

        <hr>

        <h5>Daftar Produk Return</h5>

        <table class="table table-bordered" id="product_table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah PO</th>
                    <th>Jumlah Return</th>
                    <th>Total</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($returnOrder->items as $index => $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->price }}</td>
                    <td>{{ $item->quantity + $item->quantity }}</td>
                    <td>
                        <input type="number" name="products[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" class="form-control" style="width: 100%;" />
                        <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item->product->product_id }}" />
                    </td>
                    <td>{{ $item->total }}</td>
                    <td>
                        <input type="text" name="products[{{ $index }}][reason]" value="{{ $item->reason }}" class="form-control" style="width: 100%;" />
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item" data-index="{{ $index }}" data-total="{{ $item->total }}">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total Keseluruhan</th>
                    <th colspan="3">
                        <input type="text" name="grand_total" id="grand_total" class="form-control" readonly value="0">
                    </th>
                </tr>
            </tfoot>
        </table>

        {{-- Hidden inputs untuk form submission --}}
        <div id="product_inputs">
        </div>

        <button type="submit" class="btn btn-primary" style="background-color: #8C52A0; color: white;">
            <i class="fas fa-save"></i>
            Update</button>
        <a href="{{ route('returns.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
    </div>
</div>

</div>

<script>
    let selectedItems = {};
    let grandTotal = 0;
    let index = {{ $returnOrder->items->count() }};

    // Initialize grand total from existing items
    @foreach ($returnOrder->items as $item)
        grandTotal += {{ $item->total }};
    @endforeach
    document.getElementById('grand_total').value = grandTotal;

    document.getElementById('purchase_order_id').addEventListener('change', function () {
        const poId = this.value;
        const productSelect = document.getElementById('product_select');
        productSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/returns/purchase-order/${poId}`)
            .then(res => res.json())
            .then(data => {
                productSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
                if (data.items) {
                    data.items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.product.product_id;
                        opt.dataset.name = item.product.name;
                        opt.dataset.price = item.price;
                        opt.dataset.qty = item.quantity;
                        opt.textContent = `${item.product.name} (Qty PO: ${item.quantity})`;
                        productSelect.appendChild(opt);
                    });
                }
            });
    });

    document.getElementById('add_product_btn').addEventListener('click', function () {
        const select = document.getElementById('product_select');
        const selected = select.options[select.selectedIndex];
        const id = select.value;
        const name = selected.dataset.name;
        const price = parseFloat(selected.dataset.price || 0);
        const qtyPO = parseInt(selected.dataset.qty || 0);
        const qtyReturn = parseInt(document.getElementById('return_qty').value || 0);
        const reason = document.getElementById('return_reason').value;

        if (!id || !qtyReturn || qtyReturn < 1 || qtyReturn > qtyPO) {
            alert('Jumlah return tidak valid');
            return;
        }

        const total = qtyReturn * price;

        const tbody = document.querySelector('#product_table tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${name}</td>
                <td>${price}</td>
                <td>${qtyPO}</td>
                <td>${qtyReturn}</td>
                <td>${total}</td>
                <td>${reason}</td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item" data-index="${index}" data-total="${total}">Hapus</button></td>
            `;
        tbody.appendChild(row);

        // Tambah input tersembunyi ke form
        const inputArea = document.getElementById('product_inputs');
        inputArea.innerHTML += `
            <div id="input-row-${index}">
                <input type="hidden" name="products[${index}][product_id]" value="${id}">
                <input type="hidden" name="products[${index}][quantity]" value="${qtyReturn}">
                <input type="hidden" name="products[${index}][reason]" value="${reason}">
            </div>
        `;

        // Recalculate grand total
        grandTotal = 0;
        document.querySelectorAll('#product_table tbody tr').forEach(row => {
            const totalCell = row.children[4];
            const rowTotal = parseFloat(totalCell.textContent.replace(/[^0-9.-]+/g,"")) || 0;
            grandTotal += rowTotal;
        });

        document.getElementById('grand_total').value = 'Rp' + grandTotal.toLocaleString('id-ID');

        // Reset input
        document.getElementById('return_qty').value = '';
        document.getElementById('return_reason').value = '';
        select.selectedIndex = 0;

        index++;
    });

    // New code to update total when quantity input changes
        document.querySelectorAll('input[name^="products"][name$="[quantity]"]').forEach(input => {
            input.addEventListener('input', function () {
                const row = this.closest('tr');
                const price = parseFloat(row.children[1].innerText.replace(/[^0-9.-]+/g,""));
                const qty = parseInt(this.value) || 0;
                const totalCell = row.children[4];

        // Calculate new total
        const newTotal = price * qty;
        totalCell.innerText = newTotal;

        // Update grand total
        let newGrandTotal = 0;
        document.querySelectorAll('#product_table tbody tr').forEach(r => {
            const t = parseFloat(r.children[4].innerText) || 0;
            newGrandTotal += t;
        });
        document.getElementById('grand_total').value = newGrandTotal;
            });
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const i = e.target.dataset.index;
                const total = parseFloat(e.target.dataset.total);
                grandTotal -= total;
                document.getElementById('grand_total').value = grandTotal;

                document.getElementById(`input-row-${i}`).remove();
                e.target.closest('tr').remove();
            }
        });
</script>

@endsection
