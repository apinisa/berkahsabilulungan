<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Berkah Sabilulungan</title>
    <style>
        .bg-purple {
            background: linear-gradient(180deg, #9B74AC 10%, #8C52A0 100%) !important;
        }
    </style>
    <!-- Custom fonts for this template-->
    <link href="assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-purple sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <div class="text-center mb-4">
                <!-- Logo Gambar -->
                <img src="assets/img/logoberkah.png" alt="Logo Berkah Sabilulungan" class="img-fluid" style="max-width: 80%;">
              </div>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active"><a class="nav-link" href="{{ url('/') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>

            <!-- Nav Item - Supplier -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('suppliers.index') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span class="title">Supplier</span>
                </a>
            </li>

            <!-- Nav Item - Produk -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ route('products.index') }}" >
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

            <li class="nav-item">
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

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings Card Supplier -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="{{ route('suppliers.index') }}" style="text-decoration: none; color: inherit;">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Supplier
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $supplierCount }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Earnings Card Produk -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="{{ route('products.index') }}" style="text-decoration: none; color: inherit;">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Produk</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productCount }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="{{ route('purchase_orders.index') }}" style="text-decoration: none; color: inherit;">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Purchase Order
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $purchaseOrderCount }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-cart-shopping fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="{{ route('returns.index') }}" style="text-decoration: none; color: inherit;">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Return</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $purchaseReturnCount }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-rotate-right fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="assets/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="assets/js/demo/chart-area-demo.js"></script>
    <script src="assets/js/demo/chart-pie-demo.js"></script>


    @if ($lowStockProducts->count() > 0 || $unpaidOrders->count() > 0)
    <div style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 9999;
    ">
        @if ($unpaidOrders->count() > 0)
        <div style="
            width: 320px;
            background-color: #fff8e1;
            border-left: 6px solid #f6c23e;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 15px;
            font-family: 'Nunito', sans-serif;
            color: #856404;
        ">
            <div style="font-weight: bold;">
                <i class="fas fa-exclamation-circle"></i> Pembayaran Belum Lunas!
            </div>
            <ul style="margin: 10px 0; padding-left: 18px;">
                @foreach ($unpaidOrders as $order)
                    <li>
                        {{ $order->order_number }} - {{ $order->supplier->name }}<br>
                        {{--Total: Rp{{ number_format($order->grand_total, 0, ',', '.') }}<br>
                        Dibayar: <strong>Rp{{ number_format($order->totalPaid(), 0, ',', '.') }}</strong><br>--}}
                        Sisa: Rp{{ number_format($order->grand_total - $order->totalPaid(), 0, ',', '.') }}
                    </li>
                @endforeach
            </ul>
            <button onclick="this.parentElement.style.display='none'" style="
                background-color: transparent;
                border: none;
                color: #856404;
                float: right;
                font-size: 14px;
                cursor: pointer;
            ">Tutup</button>
        </div>
        @endif

        @if ($lowStockProducts->count() > 0)
        <div style="
            width: 320px;
            background-color: #fff8e1;
            border-left: 6px solid #f6c23e;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 15px;
            font-family: 'Nunito', sans-serif;
            color: #856404;
        ">
            <div style="font-weight: bold;">
                <i class="fas fa-exclamation-triangle"></i> Stok Menipis!
            </div>
            <ul style="margin: 10px 0; padding-left: 18px;">
                @foreach ($lowStockProducts as $product)
                    <li>{{ $product->name }} ({{ $product->stock }} pcs)</li>
                @endforeach
            </ul>
            <button onclick="this.parentElement.style.display='none'" style="
                background-color: transparent;
                border: none;
                color: #856404;
                float: right;
                font-size: 14px;
                cursor: pointer;
            ">Tutup</button>
        </div>
        @endif
    </div>
    @endif




</body>

</html>
