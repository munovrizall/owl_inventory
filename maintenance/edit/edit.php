<?php
// Check if the user is logged in
session_start();

$serverName = "localhost";
$userNameDb = "root";
$password = "";
$dbName = "databaseinventory";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($serverName, $userNameDb, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Transaksi</title>

    <link rel="icon" href="../../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <style>

        .lebar-kolom1 {
            width: 20%;
        }

        .lebar-kolom2 {
            width: 15%;
        }

        .lebar-kolom3 {
            width: 10%;
        }

        .lebar-kolom4 {
            width: 30%;
        }

        .lebar-kolom5 {
            width: 3%;
        }

        .lebar-kolom10 {
            width: 10%;
        }

        .tableKeterangan {
            width: 40%;
            border-collapse: collapse;
        }

        .tableKeterangan th,
        .tableKeterangan td {
            border: 1px solid black;
            padding: 8px;
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
            <a href="../../homepage.php" class="brand-link">
                <img src="../../assets/adminlte/dist/img/OWLlogo.png" alt="OWL Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
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
                            <a href="../../homepage.php" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        </li>
                        <li class="nav-header">TRANSAKSI</li>
                        <li class="nav-item">
                            <a href="../../produksi.php" class="nav-link">
                                <i class="nav-icon fas fa-toolbox"></i>
                                <p>
                                    Produksi
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../maintenance.php" class="nav-link active">
                                <i class="nav-icon fas fa-wrench"></i>
                                <p>
                                    Maintenance
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="../input.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../monitoring.php" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../update.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Update</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="../../prototype.php" class="nav-link">
                                <i class="nav-icon fas fa-screwdriver"></i>
                                <p>
                                    Prototype
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../restock.php" class="nav-link">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>
                                    Restock
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">TAMBAH DATA</li>
                        <li class="nav-item">
                            <a href="../../master_bahan.php" class="nav-link">
                                <i class="nav-icon fa fa-pen"></i>
                                <p>
                                    Master Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../master_device.php" class="nav-link">
                                <i class="nav-icon fas fa-cube"></i>
                                <p>
                                    Master Device
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">PELAPORAN</li>
                        <li class="nav-item">
                            <a href="../../laporan_stok.php" class="nav-link">
                                <i class="nav-icon ion ion-pie-graph"></i>
                                <p>
                                    Laporan Stok
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../histori_transaksi.php" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>
                                    Histori Transaksi
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
                            <h1>Monitoring Transaksi #123</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Maintenance</li>
                                <li class="breadcrumb-item active"><a href="../monitoring.php">Monitoring</a></li>
                                <li class="breadcrumb-item active">Edit</li>
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
                            <h3 class="card-title">Monitoring Transaksi PT. Origin Wiracipta Lestari</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="monitoringForm" onsubmit="return validateForm()" method="post">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><b>Detail Transaksi</b></h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center lebar-kolom1">Nama Barang</th>
                                                        <th class="text-center lebar-kolom2">Nama SN</th>
                                                        <th class="text-center lebar-kolom3">Garansi?</th>
                                                        <th class="text-center lebar-kolom4">Kerusakan</th>
                                                        <th class="text-center lebar-kolom5">DTG</th>
                                                        <th class="text-center lebar-kolom5">CEK</th>
                                                        <th class="text-center lebar-kolom5">BA</th>
                                                        <th class="text-center lebar-kolom5">ADM</th>
                                                        <th class="text-center lebar-kolom5">P</th>
                                                        <th class="text-center lebar-kolom10">No. Resi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="transaksiTable">
                                                    <tr>
                                                        <td>Satelit</td>
                                                        <td>0031</td>
                                                        <td style="text-align: center;"><span class="badge bg-danger">Tidak</span></td>
                                                        <td>Roket rusak</td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxBarangDatang">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxCekMasalah">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxBeritaAcara">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxAdministrasi">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxPengiriman">
                                                            </div>
                                                        </td>
                                                        <td>00148003341</td>
                                                    </tr>
                                                    <tr>
                                                        <td>FP301T</td>
                                                        <td>0021</td>
                                                        <td style="text-align: center;"><span class="badge bg-success">Ya</span></td>
                                                        <td>Sensor rusak</td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxBarangDatang">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxCekMasalah">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxBeritaAcara">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxAdministrasi">
                                                            </div>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="checkboxPengiriman">
                                                            </div>
                                                        </td>
                                                        <td>00148003628</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <div class="table-responsive">
                                    <table class="tableKeterangan">
                                        <tr>
                                            <th>Keterangan</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                        <tr>
                                            <td>DTG</td>
                                            <td>Barang Datang</td>
                                        </tr>
                                        <tr>
                                            <td>CEK</td>
                                            <td>Cek Masalah Barang</td>
                                        </tr>
                                        <tr>
                                            <td>BA</td>
                                            <td>Pembuatan Berita Acara Service</td>
                                        </tr>
                                        <tr>
                                            <td>ADM</td>
                                            <td>Pengurusan Administrasi</td>
                                        </tr>
                                        <tr>
                                            <td>P</td>
                                            <td>Pengiriman Barang</td>
                                        </tr>
                                    </table>

                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" name="submitForm" onclick="submitForm()">Submit</button>
                            </div>
                        </form>

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
    <script src="../../assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="../../assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../assets/adminlte/dist/js/adminlte.min.js"></script>
    <!-- bootstrap searchable dropdown -->
    <script src="../../assets/bootstrap-5/bootstrap.bundle.min.js"></script>
    <script src="../../assets/dselect.js"></script>
    <!-- Page specific script -->
    <!-- Page specific script -->
    <script>

        function submitForm() {
            // Assuming validateForm() is the function you want to call for validation
            if (validateForm()) {
                document.getElementById("produksiForm").submit();

            } else {
                alert("Validation failed. Please check your input.");
            }
        }

        function validateForm() {
            // Add your validation logic here
            // Return true if the form is valid, false otherwise
            var selectedDevice = document.getElementById("pilihProduksiDevice").value;
            var quantity = document.getElementById("quantity").value;

            // Example validation: Check if the selected device and quantity are not empty
            if (selectedDevice === "" || quantity === "") {
                alert("Please fill in all required fields.");
                return false;
            }

            // Add more validation as needed...

            return true; // If all validations pass
        }
    </script>

</body>

</html>