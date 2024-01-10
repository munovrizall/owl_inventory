<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produksi</title>

  <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

  <style>
    .gray-italic-text {
      color: #808080;
      font-style: italic;
    }

    .form-select {
      display: block;
      width: 100%;
      padding: 0.375rem 2.25rem 0.375rem 0.75rem;
      -moz-padding-start: calc(0.75rem - 3px);
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      color: #212529;
      background-color: #fff;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 16px 12px;
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4 fixed">
      <!-- Brand Logo -->
      <a href="homepage.php" class="brand-link">
        <img src="assets/adminlte/dist/img/OWLlogo.png" alt="OWL Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-heavy">OWL RnD</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="./homepage.php" class="nav-link">
                <i class="nav-icon fas fa-th"></i>
                <p>Home</p>
              </a>
            </li>
            </li>
            <li class="nav-header">TRANSAKSI</li>
            <li class="nav-item">
              <a href="produksi.php" class="nav-link active">
                <i class="nav-icon fas fa-toolbox"></i>
                <p>
                  Produksi
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="maintenance.php" class="nav-link">
                <i class="nav-icon fas fa-wrench"></i>
                <p>
                  Maintenance
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="restock.php" class="nav-link">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>
                  Restock
                </p>
              </a>
            </li>
            <li class="nav-header">TAMBAH DATA</li>
            <li class="nav-item">
              <a href="master_bahan.php" class="nav-link">
                <i class="nav-icon fa fa-pen"></i>
                <p>
                  Master Bahan
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="master_device.php" class="nav-link">
                <i class="nav-icon fas fa-cube"></i>
                <p>
                  Master Device
                </p>
              </a>
            </li>
            <li class="nav-header">PELAPORAN</li>
            <li class="nav-item">
              <a href="laporan_stok.php" class="nav-link">
                <i class="nav-icon ion ion-pie-graph"></i>
                <p>
                  Laporan Stok
                </p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Produksi</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                <li class="breadcrumb-item active">Produksi</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <!-- general form elements -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Memproduksi Barang</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form>
              <div class="card-body">
                <div class="form-group">
                  <label for="exampleSelectBorderWidth2">Pilih Device <span style="color: red;">*</span></label>
                  <select class="form-select" id="pilihProduksiDevice">
                    <option value="">Pilih Produk</option>
                    <option>Value 1</option>
                    <option>Value 2</option>
                    <option>Value 3</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="quantity">Kuantitas :</label>
                  <div class="input-group">
                    <!-- Input untuk kuantitas -->
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="" placeholder="Masukkan jumlah produksi device">
                  </div>
                </div>
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"><b>Bahan yang Diperlukan</b></h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th style="width: 10px">No</th>
                            <th>Nama Bahan</th>
                            <th>Stok yang Dibutuhkan</th>
                            <th>Stok Tersisa</th>
                            <th>Cukup?</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1.</td>
                            <td>Transistor</td>
                            <td>30</td>
                            <td>20</td>
                            <td><span class="badge bg-danger">Tidak</span></td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>Resistor</td>
                            <td>20</td>
                            <td>20</td>
                            <td><span class="badge bg-success">Ya</span></td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>Kapasitor</td>
                            <td>50</td>
                            <td>50</td>
                            <td><span class="badge bg-success">Ya</span></td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>Induktor</td>
                            <td>10</td>
                            <td>10</td>
                            <td><span class="badge bg-success">Ya</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- /.card-body -->
                </div>
                <div class="form-group">
                  <label>Deskripsi</label>
                  <textarea class="form-control" rows="3" placeholder="Masukkan keterangan produksi device ..."></textarea>
                </div>
              </div>
              <!-- /.card-body -->
            </form>
            <div class="card-footer d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
          <!-- general form elements -->
          <!-- /.card -->

        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- bs-custom-file-input -->
  <script src="assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/adminlte/dist/js/adminlte.min.js"></script>
  <!-- bootstrap searchable dropdown -->
  <script src="assets/bootstrap-5/bootstrap.bundle.min.js"></script>
  <script src="assets/dselect.js"></script>
  <!-- Page specific script -->
  <script>
    $(function() {
      bsCustomFileInput.init();
    });
    // Searchable dropdown
    var select_box_element = document.querySelector('#pilihProduksiDevice');
    dselect(select_box_element, {
      search: true,
    });
  </script>
</body>

</html>