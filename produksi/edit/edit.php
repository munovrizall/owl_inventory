<?php
include "../../connection.php";

$queryClient = "SELECT * FROM client ORDER BY nama_client";
$resultClient = $conn->query($queryClient);

if (!$resultClient) {
    die("Error fetching kelompok data: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id'])) {
    $getId = $_GET['id'];

    // Assuming 'id' is your primary key
    $query = "UPDATE inventaris_produk SET 
              type_produk = ?,
              chip_id = ?,
              no_sn = ?,
              nama_client = ?,
              garansi_awal = ?,
              garansi_akhir = ?,
              garansi_void = ?,
              keterangan_void = ?,
              ip_address = ?,
              mac_wifi = ?,
              mac_bluetooth = ?,
              firmware_version = ?,
              hardware_version = ?,
              free_ram = ?,
              min_ram = ?,
              batt_low = ?,
              batt_high = ?,
              temperature = ?,
              status_error = ?,
              gps_latitude = ?,
              gps_longitude = ?,
              status_qc_sensor_1 = ?,
              status_qc_sensor_2 = ?,
              status_qc_sensor_3 = ?,
              status_qc_sensor_4 = ?,
              status_qc_sensor_5 = ?,
              status_qc_sensor_6 = ?
              WHERE id = ?";

    $stmt = $conn->prepare($query);

    $tipeProduk = empty($_POST['tipeProduk']) ? null : $_POST['tipeProduk'];
    $chipID = empty($_POST['chipID']) ? null : $_POST['chipID'];
    $nomorSN = empty($_POST['nomorSN']) ? null : $_POST['nomorSN'];
    $client = empty($_POST['client']) ? null : $_POST['client'];
    $garansiAwal = empty($_POST['garansiAwal']) ? null : $_POST['garansiAwal'];
    $garansiAkhir = empty($_POST['garansiAkhir']) ? null : $_POST['garansiAkhir'];
    $garansiVoid = $_POST['garansiVoid'];
    $keteranganVoid = empty($_POST['keteranganVoid']) ? null : $_POST['keteranganVoid'];
    $ipAddress = empty($_POST['ipAddress']) ? null : $_POST['ipAddress'];
    $macWifi = empty($_POST['macWifi']) ? null : $_POST['macWifi'];
    $macBluetooth = empty($_POST['macBluetooth']) ? null : $_POST['macBluetooth'];
    $firmwareVersion = empty($_POST['firmwareVersion']) ? null : $_POST['firmwareVersion'];
    $hardwareVersion = empty($_POST['hardwareVersion']) ? null : $_POST['hardwareVersion'];
    $freeRam = empty($_POST['freeRam']) ? null : $_POST['freeRam'];
    $minRam = empty($_POST['minRam']) ? null : $_POST['minRam'];
    $battLow = empty($_POST['battLow']) ? null : $_POST['battLow'];
    $battHigh = empty($_POST['battHigh']) ? null : $_POST['battHigh'];
    $temp = empty($_POST['temp']) ? null : $_POST['temp'];
    $statusError = empty($_POST['statusError']) ? null : $_POST['statusError'];
    $gpsLatitude = empty($_POST['gpsLatitude']) ? null : $_POST['gpsLatitude'];
    $gpsLongitude = empty($_POST['gpsLongitude']) ? null : $_POST['gpsLongitude'];
    $statusSensor1 = empty($_POST['statusSensor1']) ? null : $_POST['statusSensor1'];
    $statusSensor2 = empty($_POST['statusSensor2']) ? null : $_POST['statusSensor2'];
    $statusSensor3 = empty($_POST['statusSensor3']) ? null : $_POST['statusSensor3'];
    $statusSensor4 = empty($_POST['statusSensor4']) ? null : $_POST['statusSensor4'];
    $statusSensor5 = empty($_POST['statusSensor5']) ? null : $_POST['statusSensor5'];
    $statusSensor6 = empty($_POST['statusSensor6']) ? null : $_POST['statusSensor6'];

    $stmt->bind_param(
        "siisssissssssiiiididdssssssi",
        $tipeProduk,
        $chipID,
        $nomorSN,
        $client,
        $garansiAwal,
        $garansiAkhir,
        $garansiVoid,
        $keteranganVoid,
        $ipAddress,
        $macWifi,
        $macBluetooth,
        $firmwareVersion,
        $hardwareVersion,
        $freeRam,
        $minRam,
        $battLow,
        $battHigh,
        $temp,
        $statusError,
        $gpsLatitude,
        $gpsLongitude,
        $statusSensor1,
        $statusSensor2,
        $statusSensor3,
        $statusSensor4,
        $statusSensor5,
        $statusSensor6,
        $getId
    );

    $stmt->execute();
    $stmt->close();

    // Redirect to appropriate page after successful submission
    header("Location: ../quality_control.php");
    exit();
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
                            <?php
                            if (isset($_GET['id'])) {
                                $getId = $_GET['id'];
                                $row = mysqli_fetch_assoc($result);
                            } else {
                                echo ('ID not provided');
                            }
                            ?>
                            <h3 class="card-title">Quality Control <?php echo " #{$row["produk"]}" ?></h3>
                        </div>
                        <!-- form start -->
                        <form id="qcForm" method="post"> <!-- Added method="post" -->
                            <input type="hidden" name="client_id"> <!-- Hidden input to pass client_id -->
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="tipeProduk">Tipe Produk </label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="tipeProduk" name="tipeProduk" placeholder="Masukkan tipe device" value="<?php echo is_null($row['type_produk']) ? '' : $row['type_produk']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="chipID">Chip ID</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="chipID" name="chipID" placeholder="Masukkan nomor Chip ID" value="<?php echo is_null($row['chip_id']) ? '' : $row['chip_id']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="nomorSN">Nomor SN Device</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="nomorSN" name="nomorSN" placeholder="Masukkan nomor SN" value="<?php echo is_null($row['no_sn']) ? '' : $row['no_sn']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pilihClient">Pilih PT</label>
                                    <select class="form-control select2" id="pilihClient" name="client">
                                        <option value="">--- Pilih PT ---</option>
                                        <?php
                                        while ($clientRow = $resultClient->fetch_assoc()) {
                                            $selected = ($row['nama_client'] == $clientRow['nama_client']) ? 'selected' : '';
                                            echo '<option value="' . $clientRow['nama_client'] . '" ' . $selected . '>' . $clientRow['nama_client'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="garansiAwal">Tanggal Garansi Berawal</label>
                                    <div class="input-group date" id="datepicker" data-target-input="nearest">
                                        <input type="date" class="form-control" id="garansiAwal" name="garansiAwal" placeholder="Masukkan tanggal transaksi" value="<?php echo is_null($row['garansi_awal']) ? 'NULL' : $row['garansi_awal']; ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="garansiAkhir">Tanggal Garansi Berakhir</label>
                                    <div class="input-group date" id="datepicker" data-target-input="nearest">
                                        <input type="date" class="form-control" id="garansiAkhir" name="garansiAkhir" placeholder="Masukkan tanggal transaksi" value="<?php echo is_null($row['garansi_akhir']) ? 'NULL' : $row['garansi_akhir']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="garansiVoid">Apakah Garansi Void?</label>
                                    <select class="form-control select2" id="garansiVoid" name="garansiVoid">
                                        <option value="" <?php echo is_null($row['garansi_void']) ? 'selected' : ''; ?>>--- Pilih Void ---</option>
                                        <option value="1" <?php echo ($row['garansi_void'] === 1) ? 'selected' : ''; ?>>Void</option>
                                        <option value="0" <?php echo ($row['garansi_void'] === 0) ? 'selected' : ''; ?>>Tidak Void</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="keteranganVoid">Keterangan Void</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="keteranganVoid" name="keteranganVoid" placeholder="Masukkan Keterangan Void (tulis - jika garansi tidak void)" value="<?php echo is_null($row['keterangan_void']) ? '' : $row['keterangan_void']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="ipAddress">IP Address</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="ipAddress" name="ipAddress" placeholder="Masukkan IP Address" value="<?php echo is_null($row['ip_address']) ? '' : $row['ip_address']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="macWifi">MAC Address Wifi</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="macWifi" name="macWifi" placeholder="Masukkan MAC address wifi device" value="<?php echo is_null($row['mac_wifi']) ? '' : $row['mac_wifi']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="macBluetooth">MAC Address Bluetooth</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="macBluetooth" name="macBluetooth" placeholder="Masukkan MAC address bluetooth device" value="<?php echo is_null($row['mac_bluetooth']) ? '' : $row['mac_bluetooth']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="firmwareVersion">Versi Firmware</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="firmwareVersion" name="firmwareVersion" placeholder="Masukkan versi firmware device" value="<?php echo is_null($row['firmware_version']) ? '' : $row['firmware_version']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="hardwareVersion">Versi Hardware</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="hardwareVersion" name="hardwareVersion" placeholder="Masukkan versi hardware device" value="<?php echo is_null($row['hardware_version']) ? '' : $row['hardware_version']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="freeRam">Free RAM</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="freeRam" name="freeRam" placeholder="Masukkan free RAM device" value="<?php echo is_null($row['free_ram']) ? '' : $row['free_ram']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="minRam">Min RAM</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="minRam" name="minRam" placeholder="Masukkan min RAM device" value="<?php echo is_null($row['min_ram']) ? '' : $row['min_ram']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="battLow">Battery Low</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="battLow" name="battLow" placeholder="Masukkan battery low device" value="<?php echo is_null($row['batt_low']) ? '' : $row['batt_low']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="battHigh">Battery High</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="battHigh" name="battHigh" placeholder="Masukkan battery high device" value="<?php echo is_null($row['batt_high']) ? '' : $row['batt_high']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="temp">Temperature Device</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="temp" name="temp" placeholder="Masukkan temperature device" value="<?php echo is_null($row['temperature']) ? '' : $row['temperature']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusError">Status Error</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="statusError" name="statusError" placeholder="Masukkan status error device" value="<?php echo is_null($row['status_error']) ? '' : $row['status_error']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="gpsLatitude">GPS Latitude</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="gpsLatitude" name="gpsLatitude" placeholder="Masukkan GPS latitude device" value="<?php echo is_null($row['gps_latitude']) ? '' : $row['gps_latitude']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="gpsLongitude">GPS Longitude</label>
                                        <input type="number" class="form-control form-control-border border-width-2" id="gpsLongitude" name="gpsLongitude" placeholder="Masukkan GPS longitude device" value="<?php echo is_null($row['gps_longitude']) ? '' : $row['gps_longitude']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor1">Status QC Sensor 1</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor1" name="statusSensor1" placeholder="Masukkan status sensor 1 device" value="<?php echo is_null($row['status_qc_sensor_1']) ? '' : $row['status_qc_sensor_1']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor2">Status QC Sensor 2</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor2" name="statusSensor2" placeholder="Masukkan status sensor 2 device" value="<?php echo is_null($row['status_qc_sensor_2']) ? '' : $row['status_qc_sensor_2']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor3">Status QC Sensor 3</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor3" name="statusSensor3" placeholder="Masukkan status sensor 3 device" value="<?php echo is_null($row['status_qc_sensor_3']) ? '' : $row['status_qc_sensor_3']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor4">Status QC Sensor 4</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor4" name="statusSensor4" placeholder="Masukkan status sensor 4 device" value="<?php echo is_null($row['status_qc_sensor_4']) ? '' : $row['status_qc_sensor_4']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor5">Status QC Sensor 5</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor5" name="statusSensor5" placeholder="Masukkan status sensor 5 device" value="<?php echo is_null($row['status_qc_sensor_5']) ? '' : $row['status_qc_sensor_5']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="statusSensor6">Status QC Sensor 6</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="statusSensor6" name="statusSensor6" placeholder="Masukkan status sensor 6 device" value="<?php echo is_null($row['status_qc_sensor_6']) ? '' : $row['status_qc_sensor_6']; ?>">
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

        document.addEventListener("keydown", function(event) {
            // Memeriksa apakah tombol yang ditekan adalah "Enter" (kode 13)
            if (event.key === "Enter") {
                // Memanggil fungsi yang diinginkan (dalam contoh ini, submitForm)
                event.preventDefault();
                submitForm();
            }
        });

        function submitForm() {
            Swal.fire({
                icon: 'success',
                title: 'Data berhasil diupdate!',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK (enter)'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("qcForm").submit();
                }
            });
        }
    </script>
</body>

</html>