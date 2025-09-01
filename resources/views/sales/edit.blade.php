@extends('sales.layout')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Edit Penjualan</h1>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <strong>Oops!</strong> Ada kesalahan saat input.<br><br>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('sales.update', $sale->id) }}" method="POST" id="saleForm">
  @csrf
  @method('PUT')
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="form-group col-md-4">
        <label for="sale_number">Nomor Penjualan</label>
        <input type="text" name="sale_number" class="form-control" value="{{ old('sale_number', $sale->sale_number) }}" readonly>
      </div>

      <div class="form-group col-md-4">
        <label for="sale_date">Tanggal Penjualan</label>
        <input type="date" name="sale_date" class="form-control" value="{{ old('sale_date', \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d')) }}" required>
      </div>

      <div class="form-group col-md-4">
        <label for="buyer_name">Nama Pembeli</label>
        <input type="text" name="buyer_name" class="form-control" value="{{ old('buyer_name', $sale->buyer_name) }}" placeholder="Opsional">
      </div>

      <hr>
      <h5 class="font-weight-bold mb-3">Daftar Produk</h5>

      <div class="form-row align-items-end mb-3">
        <div class="form-group col-md-4">
          <label for="product_select">Produk</label>
          <select id="product_select" class="form-control">
            <option value="">-- Pilih Produk --</option>
            @foreach ($products as $product)
              <option value="{{ $product->product_id }}" data-price="{{ $product->selling_price }}">
                {{ $product->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="quantity_input">Jumlah</label>
          <input type="number" id="quantity_input" class="form-control" min="1" value="1">
        </div>

        <div class="form-group col-md-2">
          <button type="button" class="btn btn-primary" id="add_product_btn" style="background-color: #8C52A0; color: white; margin-top: 32px;">
            Tambah Produk
          </button>
        </div>
      </div>

      <table class="table table-bordered" id="product_table">
        <thead>
          <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach(old('products', $sale->items) as $index => $item)
          <tr data-index="{{ $index }}">
            <td>{{ $item->product->name }}</td>
            <td>{{ number_format($item->price, 2) }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove-product-btn">Hapus</button></td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">Total Keseluruhan</th>
            <th colspan="2">
              <input type="text" name="total_products" id="total_products" class="form-control" readonly value="{{ old('total_products', 0) }}">
              <input type="hidden" name="total_payment" id="total_payment" value="{{ old('total_payment', $sale->total_payment) }}">
            </th>
          </tr>
        </tfoot>
      </table>

      {{-- Hidden inputs for form submission --}}
      <div id="product_inputs">
        @foreach(old('products', $sale->items) as $index => $item)
          <div id="input-row-{{ $index }}">
            <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item->product_id }}">
            <input type="hidden" name="products[{{ $index }}][price]" value="{{ $item->price }}">
            <input type="hidden" name="products[{{ $index }}][quantity]" value="{{ $item->quantity }}">
          </div>
        @endforeach
      </div>

      <div class="form-group col-md-4">
        <label for="discount">Diskon (Rp)</label>
        <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount', $sale->discount) }}">
      </div>

      <div class="form-group col-md-4">
        <label>Total Bayar</label>
        <div class="form-control" readonly id="total_payment_display">{{ number_format(old('total_payment', $sale->total_payment), 2, '.', '') }}</div>
      </div>

      <div class="form-group col-md-4">
        <label for="payment_method">Metode Pembayaran</label>
        <select name="payment_method" class="form-control" required>
          <option value="Tunai" {{ old('payment_method', $sale->payment_method) == 'Tunai' ? 'selected' : '' }}>Tunai</option>
          <option value="Transfer" {{ old('payment_method', $sale->payment_method) == 'Transfer' ? 'selected' : '' }}>Transfer</option>
        </select>
      </div>

      <div class="form-group col-md-4">
        <label for="note">Catatan (Opsional)</label>
        <textarea name="note" class="form-control">{{ old('note', $sale->note) }}</textarea>
      </div>

      <button type="submit" class="btn btn-primary" style="background-color: #8C52A0; color: white;">
        <i class="fas fa-save"></i> Update
      </button>
      <a href="{{ route('sales.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
  </div>
</form>

@push('scripts')
  <script>
    let index = {{ count(old('products', $sale->items)) }};
    let grandTotal = 0;

    function recalculateGrandTotal() {
      grandTotal = 0;
      document.querySelectorAll('#product_table tbody tr').forEach(row => {
        const totalCell = row.querySelector('td:nth-child(4)');
        const rowTotal = parseFloat(totalCell.textContent.replace(/,/g, '')) || 0;
        grandTotal += rowTotal;
      });
      const discount = parseFloat(document.getElementById('discount').value) || 0;
      const totalPayment = grandTotal - discount;
      document.getElementById('total_products').value = grandTotal.toFixed(2);
      document.getElementById('total_payment').value = totalPayment.toFixed(2);

      // Update the new total payment display with formatted Rupiah
      const formattedTotal = totalPayment.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
      document.getElementById('total_payment_display').textContent = formattedTotal;
    }

    document.getElementById('add_product_btn').addEventListener('click', function () {
      const productSelect = document.getElementById('product_select');
      const quantityInput = document.getElementById('quantity_input');
      const selectedOption = productSelect.options[productSelect.selectedIndex];
      const productId = productSelect.value;
      const productName = selectedOption.textContent.trim();
      const price = parseFloat(selectedOption.dataset.price || 0);
      const quantity = parseInt(quantityInput.value) || 0;

      if (!productId) {
        alert('Silakan pilih produk.');
        return;
      }
      if (quantity < 1) {
        alert('Jumlah harus minimal 1.');
        return;
      }

      // Tambahkan baris ke tabel
      const tbody = document.querySelector('#product_table tbody');
      const row = document.createElement('tr');
      row.setAttribute('data-index', index);
      row.innerHTML = `
        <td>${productName}</td>
        <td>${price.toFixed(2)}</td>
        <td>${quantity}</td>
        <td>${(price * quantity).toFixed(2)}</td>
        <td><button type="button" class="btn btn-danger btn-sm remove-product-btn">Hapus</button></td>
      `;
      tbody.appendChild(row);

      // Tambahkan input tersembunyi
      const productInputs = document.getElementById('product_inputs');
      const inputRow = document.createElement('div');
      inputRow.setAttribute('id', `input-row-${index}`);
      inputRow.innerHTML = `
        <input type="hidden" name="products[${index}][product_id]" value="${productId}">
        <input type="hidden" name="products[${index}][price]" value="${price}">
        <input type="hidden" name="products[${index}][quantity]" value="${quantity}">
      `;
      productInputs.appendChild(inputRow);

      index++;

      // Reset input
      productSelect.selectedIndex = 0;
      quantityInput.value = 1;

      recalculateGrandTotal();
    });

    document.getElementById('discount').addEventListener('input', function () {
      recalculateGrandTotal();
    });

    document.querySelector('#product_table tbody').addEventListener('click', function (e) {
      if (e.target.classList.contains('remove-product-btn')) {
        const row = e.target.closest('tr');
        const index = row.getAttribute('data-index');
        row.remove();

        // Hapus input tersembunyi
        const inputRow = document.getElementById(`input-row-${index}`);
        if (inputRow) inputRow.remove();

        recalculateGrandTotal();
      }
    });

    // Initial calculation on page load
    recalculateGrandTotal();
  </script>
@endpush
@endsection
