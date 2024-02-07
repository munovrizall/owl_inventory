<?php

include "../../connection.php";

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
    <title>Detail Produk</title>

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

        .table-head {
            width: 240px;
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
                                    <a href="../quality_control.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Quality Control</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../inventaris_device.php" class="nav-link active">
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
                            <a href="../../harga_bahan.php" class="nav-link">
                                <i class="nav-icon fas fa-dollar-sign"></i>
                                <p>
                                    Harga Bahan
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
                        <li class="nav-item" style="margin-bottom: 40px;">
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
                            <h1>Detail Produk</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Produksi</li>
                                <li class="breadcrumb-item active"><a href="../inventaris_device.php">Inventaris
                                        Device</a></li>
                                <li class="breadcrumb-item active">Detail</li>
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
                            <?php
                            if (isset($_GET['id'])) {
                                $getId = $_GET['id'];
                                $row = mysqli_fetch_assoc($result);
                            } else {
                                echo ('ID not provided');
                            }
                            ?>
                            <h3 class="card-title">Detail Produk <?php echo " #{$row["id"]}" ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="produkForm" method="post">
                            <!-- Added method="post" -->
                            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                            <!-- Hidden input to pass client_id -->
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="table-container">
                                        <div class="tr-column">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="table-head" style="min-width: 180px"><b>Nama Perusahaan :</b></td>
                                                        <td>
                                                            <?php
                                                            $nama_client = $row["nama_client"];
                                                            echo $nama_client !== null ? $nama_client : '-';
                                                            ?>
                                                        </td>
                                                        <td class="table-head" style="min-width: 180px"><b>Free RAM :</b></td>
                                                        <td>
                                                            <?php
                                                            $free_ram = $row["free_ram"];
                                                            echo "{$free_ram}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Nama Produk :</b></td>
                                                        <td>
                                                            <?php
                                                            $produk = $row["produk"];
                                                            echo "{$produk}";
                                                            ?>
                                                        </td>
                                                        <td><b>Min RAM :</b></td>
                                                        <td>
                                                            <?php
                                                            $min_ram = $row["min_ram"];
                                                            echo "{$min_ram}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Tipe Produk :</b></td>
                                                        <td>
                                                            <?php
                                                            $type_produk = $row["type_produk"];
                                                            echo "{$type_produk}";
                                                            ?>
                                                        </td>
                                                        <td class="table-head"><b>Battery Low :</b></td>
                                                        <td>
                                                            <?php
                                                            $batt_low = $row["batt_low"];
                                                            echo "{$batt_low}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="table-head"><b>Nomor SN :</b></td>
                                                        <td>
                                                            <?php
                                                            $no_sn = $row["no_sn"];
                                                            echo "{$no_sn}";
                                                            ?>
                                                        </td>
                                                        <td><b>Battery High :</b></td>
                                                        <td>
                                                            <?php
                                                            $batt_high = $row["batt_high"];
                                                            echo "{$batt_high}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Chip ID :</b></td>
                                                        <td>
                                                            <?php
                                                            $chip_id = $row["chip_id"];
                                                            echo "{$chip_id}";
                                                            ?>
                                                        </td>
                                                        <td><b>Temperature :</b></td>
                                                        <td>
                                                            <?php
                                                            $temperature = $row["temperature"];
                                                            echo "{$temperature}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Garansi Awal :</b></td>
                                                        <td>
                                                            <?php
                                                            $garansi_awal = $row["garansi_awal"];
                                                            $formatted_date = $garansi_awal !== null ? date("d/m/Y", strtotime($garansi_awal)) : '-';
                                                            echo $formatted_date;
                                                            ?>
                                                        </td>
                                                        <td><b>Status Error :</b></td>
                                                        <td>
                                                            <?php
                                                            $status_error = $row["status_error"];
                                                            echo "{$status_error}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Garansi Akhir :</b></td>
                                                        <td>
                                                            <?php
                                                            $garansi_akhir = $row["garansi_akhir"];
                                                            $formatted_date = $garansi_awal !== null ? date("d/m/Y", strtotime($garansi_akhir)) : '-';
                                                            echo $formatted_date;
                                                            ?>
                                                        </td>
                                                        <td><b>GPS Latitude :</b></td>
                                                        <td>
                                                            <?php
                                                            $gps_latitude = $row["gps_latitude"];
                                                            echo "{$gps_latitude}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Garansi Void :</b></td>
                                                        <td>
                                                            <?php
                                                            $voidClass = 'badge bg-success';
                                                            $voidText = 'Tidak Void';
                                                            
                                                            if ($row["garansi_void"] == 1) {
                                                                $voidClass = "badge bg-danger";
                                                                $voidText = "Void";
                                                            }
                                                            ?>

                                                            <div class="<?php echo $voidClass; ?>"><?php echo $voidText; ?></div>
                                                            
                                                        </td>
                                                        <td><b>GPS Longitude :</b></td>
                                                        <td>
                                                            <?php
                                                            $gps_longitude = $row["gps_longitude"];
                                                            echo "{$gps_longitude}";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Keterangan Void:</b></td>
                                                        <td>
                                                            <?php
                                                            $keterangan_void = $row["keterangan_void"];
                                                            echo "{$keterangan_void}";
                                                            ?>
                                                        </td>
                                                        <td><b>Status QC Sensor 1 :</b></td>
                                                        <td>
                                                            <span class="badge bg-success">OK</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Status Garansi :</b></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = 'badge bg-success'; // Default class
                                                            $statusText = 'Ya'; // Default text

                                                            if (strtotime($row["garansi_akhir"]) < strtotime('today') || $row["garansi_void"] == 1) {
                                                                $statusClass = 'badge bg-danger';
                                                                $statusText = 'Tidak';
                                                            }
                                                            ?>
                                                            <div class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
                                                        </td>
                                                        <td><b>Status QC Sensor 2 :</b></td>
                                                        <td>
                                                            <span class="badge bg-danger">FAIL</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>IP Address :</b></td>
                                                        <td>
                                                            <?php
                                                            $ip_address = $row["ip_address"];
                                                            echo "{$ip_address}";
                                                            ?>
                                                        </td>
                                                        <td><b>Status QC Sensor 3 :</b></td>
                                                        <td>
                                                            <span class="badge bg-success">OK</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>MAC Address WIFI :</b></td>
                                                        <td>
                                                            <?php
                                                            $mac_wifi = $row["mac_wifi"];
                                                            echo "{$mac_wifi}";
                                                            ?>
                                                        </td>
                                                        <td><b>Status QC Sensor 4 :</b></td>
                                                        <td>
                                                            <span class="badge bg-success">OK</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>MAC Address Bluetooth:</b></td>
                                                        <td>
                                                            <?php
                                                            $mac_bluetooth = $row["mac_bluetooth"];
                                                            echo "{$mac_bluetooth}";
                                                            ?>
                                                        </td>
                                                        <td><b>Status QC Sensor 5 :</b></td>
                                                        <td>
                                                            <span class="badge bg-success">OK</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Versi Firmware :</b></td>
                                                        <td>
                                                            <?php
                                                            $firmware_version = $row["firmware_version"];
                                                            echo "{$firmware_version}";
                                                            ?>
                                                        </td>
                                                        <td><b>Status QC Sensor 6 :</b></td>
                                                        <td>
                                                            <span class="badge bg-success">OK</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Versi Hardware :</b></td>
                                                        <td>
                                                            <?php
                                                            $hardware_version = $row["hardware_version"];
                                                            echo "{$hardware_version}";
                                                            ?>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <!-- /.card-body -->
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button id="backButton" class="btn btn-info" onclick="goBack()"><i class="fas fa-arrow-left" style="padding-right: 8px"></i>Kembali</button>
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
    <!-- Page specific script -->
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>