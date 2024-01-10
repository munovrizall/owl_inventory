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
    $kelompok = $_POST["kelompok"];
    $nama = $_POST["nama"];
    $quantity = $_POST["quantity"];
    $deskripsi = $_POST["deskripsi"];

    // Check if the material name already exists
    $checkQuery = "SELECT COUNT(*) FROM masterbahan WHERE nama = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $nama);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    // If the material name already exists, display an error
    if ($count > 0) {
        echo "Error: Material name '$nama' already exists in the database.";
    } else {
        // Insert the new record
        $insertQuery = "INSERT INTO masterbahan (kelompok, nama, quantity, deskripsi) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssis", $kelompok, $nama, $quantity, $deskripsi);

        if ($insertStmt->execute()) {
            echo "Data berhasil ditambahkan ke tabel masterbahan.";
        } else {
            echo "Error: " . $insertStmt->error;
        }

        $insertStmt->close();
    }
}

// Fetch data from masterkelompok table
$queryKelompok = "SELECT kelompok_id, nama_kelompok FROM masterkelompok ORDER BY nama_kelompok";
$resultKelompok = $conn->query($queryKelompok);

if (!$resultKelompok) {
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
                            <a href="master_bahan.php" class="nav-link active">
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
                            <h1>Master Bahan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Master Bahan</li>
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
                            <h3 class="card-title">Menambah Jenis Master Bahan</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="masterBahanForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="pilihNamaKelompok">Pilih Kelompok <span style="color: red;">*</span></label>
                                    </div>
                                    <select class="form-select" id="pilihNamaKelompok" name="kelompok" searchable="Search here...">
                                        <option value="">Pilih Kelompok</option>
                                        <?php
                                        while ($row = $resultKelompok->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_kelompok'] . '">' . $row['nama_kelompok'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#modalBuatKelompok" style="margin-top: 10px; max-width: 180px;"><i class="fas fa-plus " style="margin-right: 16px;"></i>Kelompok Baru</button>
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama Bahan <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control form-control-border border-width-2" id="nama" name="nama" placeholder="Contoh : R060310k">
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Kuantitas <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <!-- Input untuk kuantitas -->
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="" placeholder="Masukkan jumlah stok bahan">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi<span class="gray-italic-text"> (opsional)</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan keterangan bahan ..."></textarea>
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

        <!-- Modal menambahkan kelompok baru -->
        <div class="modal fade" id="modalBuatKelompok" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Buat Kelompok Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Nama Kelompok</span></label>
                            <input type="text" class="form-control form-control-border border-width-2" id="nama" name="nama" placeholder="Masukkan nama kelompok yang ingin dibuat">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="addNewKelompok()">Submit</button>
                    </div>
                </div>
            </div>
        </div>

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
    <!-- bootstrap searchable dropdown -->
    <script src="assets/bootstrap-5/bootstrap.bundle.min.js"></script>
    <script src="assets/dselect.js"></script>
    <!-- Page specific script -->
    <script>
        function validateForm() {
            var selectedItem = document.getElementById("pilihNamaKelompok").value;
            var nama = document.getElementById("nama").value;
            var quantity = document.getElementById("quantity").value;

            if (selectedItem === "" || nama === "" || quantity === "" || quantity <= 0) {
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
            var formData = $("#masterBahanForm").serialize();

            $.ajax({
                type: "POST",
                url: "master_bahan.php",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bahan berhasil didaftarkan!',
                    });

                },
                error: function(error) {
                    alert("Error mendaftarkan barang.");
                }
            });
        }

        function addNewKelompok() {
            
        }

        function resetForm() {
            document.getElementById("masterBahanForm").reset();
            resetDropdown();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihNamaKelompok");
            dropdown.selectedIndex = 0; // reset ke pilihan pertama

            // jika multiple selection
            dropdown.querySelectorAll("option:checked").forEach(option => {
                option.selected = false;
            });

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
        }

        // Searchable dropdown
        var select_box_element = document.querySelector('#pilihNamaKelompok');
        dselect(select_box_element, {
            search: true,
        });
    </script>
</body>

</html>