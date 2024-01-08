<?php
// Check if the user is logged in
session_start();
$username = $_SESSION['username'];

$serverName = "localhost";
$userNameDb = "root";
$password = "";
$dbName = "databaseinventory";

$conn = new mysqli($serverName, $userNameDb, $password, $dbName);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM historis ORDER BY waktu DESC LIMIT 20";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>

  <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="assets/adminlte/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="assets/adminlte/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="assets/adminlte/plugins/summernote/summernote-bs4.min.css">
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
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
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
              <a href="./homepage.php" class="nav-link active">
                <i class="nav-icon fas fa-th"></i>
                <p>Home</p>
              </a>
            </li>
            </li>
            <li class="nav-header">TRANSAKSI</li>
            <li class="nav-item">
              <a href="produksi.php" class="nav-link">
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
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Halo <?php echo $username; ?>!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><sup style="font-size: 24px">Produksi</sup></h3>
                  <p>Memproduksi device</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-toolbox"></i>
                </div>
                <a href="produksi.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><sup style="font-size: 24px">Maintenance</sup></h3>
                  <p>Maintenance device</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-wrench"></i>
                </div>
                <a href="maintenance.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><sup style="font-size: 24px">Restock</sup></h3>
                  <p>Restock bahan</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-shopping-cart"></i>
                </div>
                <a href="restock.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><sup style="font-size: 24px">Laporan Stok</sup></h3>
                  <p>Cek ketersediaan stok bahan</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>

            <!-- ./col -->
          </div>
          <section class="content">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><b>Histori</b></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>User</th>
                        <th>Nama Barang</th>
                        <th>Kuantitas</th>
                        <th>Aktivitas</th>
                        <th>Waktu</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      while ($row = mysqli_fetch_assoc($result)) {
                        // Tentukan kelas badge berdasarkan nilai activity
                        $badgeClass = "";
                        switch ($row["activity"]) {
                          case "Produksi":
                            $badgeClass = "badge bg-info";
                            break;
                          case "Restock":
                            $badgeClass = "badge bg-success";
                            break;
                          case "Maintenance":
                            $badgeClass = "badge bg-danger";
                            break;
                          default:
                            // Set kelas default jika nilai activity tidak sesuai dengan kasus di atas
                            $badgeClass = "badge bg-secondary";
                            break;
                        }
                      ?>
                        <tr>
                          <td><?php echo $row["pengguna"]; ?></td>
                          <td><?php echo $row["stok_id"]; ?></td>
                          <td><?php echo $row["quantity"]; ?></td>
                          <!-- Tambahkan span dengan kelas badge sesuai dengan nilai activity -->
                          <td><span class="<?php echo $badgeClass; ?>"><?php echo $row["activity"]; ?></span></td>
                          <td><?php echo $row["waktu"]; ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>

                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- general form elements -->
              <!-- /.card -->

            </div><!-- /.container-fluid -->
          </section>
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
  <!-- jQuery UI 1.11.4 -->
  <script src="assets/adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="assets/adminlte/plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="assets/adminlte/plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="assets/adminlte/plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="assets/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="assets/adminlte/plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="assets/adminlte/plugins/moment/moment.min.js"></script>
  <script src="assets/adminlte/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="assets/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="assets/adminlte/plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/adminlte/dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="assets/adminlte/dist/js/pages/dashboard.js"></script>
</body>

</html>