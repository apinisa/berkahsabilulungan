<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Berkah Sabilulungan</title>

  <!-- Custom fonts and styles -->
  <link href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
  <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

  <style>
    .bg-purple {
      background: linear-gradient(180deg, #9B74AC 10%, #8C52A0 100%) !important;
    }
  </style>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-purple sidebar sidebar-dark accordion" id="accordionSidebar">
      <div class="text-center mb-4">
        <img src="{{ asset('assets/img/logoberkah.png') }}" alt="Logo Berkah Sabilulungan" class="img-fluid" style="max-width: 80%;">
      </div>
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <!-- Nav Item - Supplier -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('suppliers.index') }}">
          <i class="fas fa-fw fa-user"></i>
          <span class="title">Supplier</span>
        </a>
      </li>

      <!-- Tambahan menu lainnya -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('products.index') }}">
          <i class="fas fa-fw fa-box-open"></i>
          <span>Produk</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('purchase_orders.index') }}">
          <i class="fas fa-fw fa-cart-shopping"></i>
          <span>Pembelian / PO</span>
        </a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="{{ route('payments.index') }}">
          <i class="fas fa-fw fa-wallet"></i>
          <span>Pembayaran</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('returns.index') }}">
          <i class="fas fa-fw fa-rotate-right"></i>
          <span>Pengembalian / Return</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('sales.index') }}">
          <i class="fas fa-fw fa-cash-register"></i>
          <span>Penjualan</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="true" aria-controls="collapseReports">
          <i class="fas fa-fw fa-file"></i>
          <span>Laporan</span>
        </a>
        <div id="collapseReports" class="collapse" aria-labelledby="headingReports" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('reports.income') }}">Pemasukan</a>
            <a class="collapse-item" href="{{ route('reports.expense') }}">Pengeluaran</a>
            <a class="collapse-item" href="{{ route('reports.summary') }}">Ringkasan Laba Bersih</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider">
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End Sidebar -->


    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>
        <!-- End of Topbar -->

        <!-- Main Content Area -->
        <div class="container-fluid">
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
          @yield('content')
        </div>
      </div>

    </div>
    <!-- End Content Wrapper -->
  </div>
  <!-- End Wrapper -->

  <!-- Scripts -->
  <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

  @yield('scripts')
</body>
</html>
