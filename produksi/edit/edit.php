<?php

include "../../connection.php";

$queryClient = "SELECT * FROM client ORDER BY nama_client";
$resultClient = $conn->query($queryClient);

if (!$resultClient) {
    die("Error fetching kelompok data: " . $conn->error);
}

if (isset($_GET['id'])) {
    $getId = $_GET['id'];

    $query = "SELECT * FROM inventaris_produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $getId);
    $stmt->execute();

    $result = $stmt->get_result();
} else {
    echo "ID not provided.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit QC</title>

    <link rel="icon" href="../../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../../assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../../assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        #successMessage {
            display: none;
            /* Hide the success message initially */
        }

        .gray-italic-text {
            color: #808080;
            font-style: italic;
        }

        .table-container {
            display: flex;
            flex-direction: row;
            width: 100%;
            overflow-x: auto;
        }

        .thead-column {
            flex: 0 0 20%;
            font-weight: bold;
        }

        .tr-column {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: none;
            /* menghilangkan garis pada sel tabel */
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
                            <a href="../../produksi.php" class="nav-link active">
                                <i class="nav-icon fas fa-toolbox"></i>
                                <p>
                                    Produksi
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="../produksi.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Produksi Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../quality_control.php" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Quality Control</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../inventaris_device.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inventaris Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../pengiriman.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pengiriman Device</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="../../maintenance.php" class="nav-link">
                                <i class="nav-icon fas fa-wrench"></i>
                                <p>
                                    Maintenance
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="../../maintenance/input.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../maintenance/monitoring.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../maintenance/update.php" class="nav-link">
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
                        <li class="nav-item">
                            <a href="perusahaan.php" class="nav-link">
                                <i class="nav-icon fas fa-industry"></i>
                                <p>
                                    Perusahaan
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="../tambah_perusahaan.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tambah Perusahaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../list_perusahaan.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Perusahaan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-header">PELAPORAN</li>
                        <li class="nav-item">
                            <a href="../../stok_bahan.php" class="nav-link">
                                <i class="nav-icon ion ion-pie-graph"></i>
                                <p>
                                    Stok Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../../stok_produk.php" class="nav-link">
                                <i class="nav-icon fas fa-microchip"></i>
                                <p>
                                    Stok Produk
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
                            <h1>Edit Quality Control</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Produksi</li>
                                <li class="breadcrumb-item active"><a href="../quality_control.php">Quality Control</a></li>
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
                            <h3 class="card-title">Quality Control #Namaproduk</h3>
                        </div>
                        <!-- form start -->
                        <form id="qcForm" method="post"> <!-- Added method="post" -->
                            <input type="hidden" name="client_id"> <!-- Hidden input to pass client_id -->
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="tipeProduk">Tipe Produk</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="tipeProduk" name="tipeProduk" placeholder="Masukkan tipe device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="chipID">Chip ID</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="chipID" name="chipID" placeholder="Masukkan nomor Chip ID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="nomorSN">Nomor SN Device</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="nomorSN" name="nomorSN" placeholder="Masukkan nomor SN">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pilihClient">Pilih PT</label>
                                    <select class="form-control select2" id="pilihClient" name="client">
                                        <option value="">--- Pilih PT ---</option>
                                        <?php
                                        while ($row = $resultClient->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_client'] . '">' . $row['nama_client'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="garansiAwal">Tanggal Garansi Berawal</label>
                                    <div class="input-group date" id="datepicker" data-target-input="nearest">
                                        <input type="date" class="form-control" id="garansiAwal" name="garansiAwal" placeholder="Masukkan tanggal transaksi" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="garansiAkhir">Tanggal Garansi Berakhir</label>
                                    <div class="input-group date" id="datepicker" data-target-input="nearest">
                                        <input type="date" class="form-control" id="garansiAkhir" name="garansiAkhir" placeholder="Masukkan tanggal transaksi" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="ipAddress">IP Address</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="ipAddress" name="ipAddress" placeholder="Masukkan IP Address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="macWifi">MAC Address Wifi</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="macWifi" name="macWifi" placeholder="Masukkan MAC address wifi device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="macBluetooth">MAC Address Bluetooth</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="macBluetooth" name="macBluetooth" placeholder="Masukkan MAC address bluetooth device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="firmwareVersion">Versi Firmware</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="firmwareVersion" name="firmwareVersion" placeholder="Masukkan versi firmware device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="hardwareVersion">Versi Hardware</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="hardwareVersion" name="hardwareVersion" placeholder="Masukkan versi hardware device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="freeRam">Free RAM</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="freeRam" name="freeRam" placeholder="Masukkan free RAM device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="minRam">Min RAM</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="minRam" name="minRam" placeholder="Masukkan min RAM device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="battLow">Battery Low</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="battLow" name="battLow" placeholder="Masukkan battery low device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="battHigh">Battery High</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="battHigh" name="battHigh" placeholder="Masukkan battery high device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="temp">Temperature Device</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="temp" name="temp" placeholder="Masukkan temperature device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusError">Status Error</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="statusError" name="statusError" placeholder="Masukkan status error device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="gpsLatitude">GPS Latitude</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="gpsLatitude" name="gpsLatitude" placeholder="Masukkan GPS latitude device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="gpsLongitude">GPS Longitude</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="gpsLongitude" name="gpsLongitude" placeholder="Masukkan GPS longitude device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor1">Status QC Sensor 1</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor1" name="statusSensor1" placeholder="Masukkan status sensor 1 device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor2">Status QC Sensor 2</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor2" name="statusSensor2" placeholder="Masukkan status sensor 2 device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor3">Status QC Sensor 3</label>
                                        <input type="text" class="form-control form-control-border border-width-3" id="statusSensor3" name="statusSensor3" placeholder="Masukkan status sensor 3 device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor4">Status QC Sensor 4</label>
                                        <input type="text" class="form-control form-control-border border-width-4" id="statusSensor4" name="statusSensor4" placeholder="Masukkan status sensor 4 device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor5">Status QC Sensor 5</label>
                                        <input type="text" class="form-control form-control-border border-width-5" id="statusSensor5" name="statusSensor5" placeholder="Masukkan status sensor 5 device">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor6">Status QC Sensor 6</label>
                                        <input type="text" class="form-control form-control-border border-width-6" id="statusSensor6" name="statusSensor6" placeholder="Masukkan status sensor 6 device">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" id="submitButton" class="btn btn-primary" onclick="submitForm()">Submit</button>
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
    <script src="../../assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="../../assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../assets/adminlte/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 Toast -->
    <script src="../../assets/adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Select2 -->
    <script src="../../assets/adminlte/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page specific script -->
    <script>
        // Select2 Dropdown
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                containerCssClass: 'height-40px',
            });
        });
    </script>
</body>

</html>