<?php
session_start();

$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "databaseinventory";

$conn = new mysqli($serverName, $userName, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaDevice = $_POST["namaDevice"];
    $namaBahan = $_POST["namaBahan"];
    $quantity = $_POST["quantity"];

    // Check if either 'produk' or 'namaBahan' already exists
    $checkQuery = "SELECT COUNT(*) FROM produksi WHERE produk = ? AND nama_bahan = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $namaDevice, $namaBahan);  
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();   
    $checkStmt->close();

    if ($count > 0) {
        echo "Error: Either 'produk' and 'namaBahan' already exists in the database.";
    } else {
        // Insert the new record
        $query = "INSERT INTO produksi (produk, nama_bahan, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $namaDevice, $namaBahan, $quantity);  // Assuming 'quantity' is an integer parameter

        if ($stmt->execute()) {
            echo "Data berhasil ditambahkan ke tabel produksi.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch data from produksi table
$queryMasterBahan = "SELECT nama FROM masterbahan ORDER BY nama";
$resultMasterBahan = $conn->query($queryMasterBahan);

if (!$resultMasterBahan) {
    die("Error fetching kelompok data: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Bahan</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <style>
        .input-group-append label {
            margin-right: 24px;
        }
    </style>


</head>

<body class="hold-transition sidebar-mini">
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
                            <a href="./homepage.php" class="nav-link">
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
                            <a href="master_device.php" class="nav-link active">
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
                            <h1>Master Device</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Master Device</li>
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
                            <h3 class="card-title">Menambah Jenis Device untuk Diproduksi</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="masterDeviceForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col">
                                            <label for="namaDevice">Masukkan Nama Device : <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2" id="namaDevice" name="namaDevice" placeholder="Masukkan nama device baru">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="namaBahan">Nama Bahan : <span style="color: red;">*</span></label>
                                    <select class="custom-select form-control-border border-width-2" id="namaBahan" name="namaBahan" searchable="Search here...">
                                        <option value="" selected disabled>Pilih Bahan</option>
                                        <?php
                                        while ($row = $resultMasterBahan->fetch_assoc()) {
                                            echo '<option value="' . $row['nama'] . '">' . $row['nama'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Kuantitas : <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <!-- Input untuk kuantitas -->
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="" placeholder="Masukkan jumlah kuantitas yang dibutuhkan">

                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="if(validateForm()) { validateSuccess(); resetForm(); }">Submit</button>
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
    <!-- SweetAlert2 Toast -->
    <script src="assets/adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Page specific script -->
    <script>

        function validateForm() {
            var selectedItem = document.getElementById("namaDevice").value;
            var nama = document.getElementById("namaBahan").value;
            var quantity = document.getElementById("quantity").value;

            if (selectedItem === "" || nama === "" ||quantity === "" || quantity <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                });
                return false;
            }

            return true;
        }


        function validateSuccess() {
            // Get the form data
            var formData = $("#masterDeviceForm").serialize();

            $.ajax({
                type: "POST",
                url: "master_device.php",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Device berhasil didaftarkan!',
                        showConfirmButton: false,
                        timer: 3000
                    });

                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Barang sudah terdaftar!',
                        text: 'Masukkan nama baru!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetForm();
                        }
                    });
                }
            });
        }

        function resetForm() {
            document.getElementById("masterDeviceForm").reset();
        }

    </script>
</body>

</html>