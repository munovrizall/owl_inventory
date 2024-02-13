<?php
include "connection.php";
include "sidebar.php";

$username = $_SESSION['username'];

$query = "SELECT * FROM historis ORDER BY historis.ID DESC LIMIT 20";
$result = mysqli_query($conn, $query);
if (!$result) {
  die('Error in query: ' . mysqli_error($conn));
}
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
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

  <style>
    .lebar-kolom1 {
      width: 10%;
    }

    .lebar-kolom2 {
      width: 22%;
    }

    .lebar-kolom3 {
      width: 5%;
    }

    .lebar-kolom4 {
      width: 5%;
    }

    .lebar-kolom5 {
      width: 40%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .lebar-kolom6 {
      width: 18%;
    }

    .lebar-kolom7 {
      width: 5%;
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

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Halo <?php echo $username ?>!</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Home</li>
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
                <a href="produksi/produksi.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="maintenance/update.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><sup style="font-size: 24px">Prototype</sup></h3>
                  <p>Prototype device</p>
                </div>
                <div class="icon">
                  <i class="fas fa-screwdriver"></i>
                </div>
                <a href="prototype.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
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

            <!-- ./col -->
          </div>
          <section class="content">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><b>Histori Transaksi Terakhir</b></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th class="text-center lebar-kolom1">User</th>
                        <th class="text-center lebar-kolom2">Nama Barang</th>
                        <th class="text-center lebar-kolom3">Kuantitas</th>
                        <th class="text-center lebar-kolom4">Aktivitas</th>
                        <th class="text-center lebar-kolom5">Deskripsi</th>
                        <th class="text-center lebar-kolom6">Waktu</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result->num_rows > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                          $tanggal = date('H:i d-m-Y', strtotime($row["waktu"]));
                          // Tentukan kelas badge berdasarkan nilai activity
                          $badgeClass = "";
                          switch ($row["activity"]) {
                            case "Produksi":
                              $badgeClass = "badge bg-info";
                              if (str_contains($row["quantity"], "-")) {
                                $badgeClass = "badge bg-danger";
                              }
                              break;
                            case "Restock":
                              $badgeClass = "badge bg-success";
                              break;
                            case "Maintenance":
                              $badgeClass = "badge bg-danger";
                              $row["quantity"] = "-" . $row["quantity"];
                              break;
                            case "Prototype":
                              $badgeClass = "badge bg-warning";
                              $row["quantity"] = "-" . $row["quantity"];
                              break;
                            case "Pengiriman":
                              $badgeClass = "badge bg-secondary";
                              $row["quantity"] = "-" . $row["quantity"];
                              break;
                            default:
                              $badgeClass = "badge bg-secondary";
                              break;
                          }
                      ?>
                          <tr>
                            <td><?php echo $row["pengguna"]; ?></td>
                            <td><?php echo $row["nama_barang"]; ?></td>
                            <td><?php echo $row["quantity"]; ?></td>
                            <td style="text-align: center;"><span class="<?php echo $badgeClass; ?>"><?php echo $row["activity"]; ?></span></td>
                            <td><?php echo $row["deskripsi"]; ?></td>
                            <td><?php echo $tanggal; ?></td>
                          </tr>
                      <?php
                        }
                      } else {
                        echo "No rows found in the result set.";
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
  <!-- Bootstrap 4 -->
  <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/adminlte/dist/js/adminlte.js"></script>
</body>

</html>