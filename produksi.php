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

$resultProduksi = "";
$stokDibutuhkan = "";
$currentStock = "";

// Fetch data from produksi table
$queryProdukPilihan = "SELECT DISTINCT produk FROM produksi ORDER BY produk";
$resultProdukPilihan = $conn->query($queryProdukPilihan);

if (isset($_POST['selectedDevice'])) {
    $selectedDeviceName = $_POST['selectedDevice'];
    $pengguna = isset($_SESSION['username']) ? $_SESSION['username'] : '';

    // Milih bahan untuk produksi
    $query = "SELECT nama_bahan, quantity FROM produksi WHERE produk = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedDeviceName);
    $stmt->execute();
    $stmt->bind_result($namaBahan, $stokDibutuhkan);
    $resultProduksi = array();
    while ($stmt->fetch()) {
        $resultProduksi[] = array('namaBahan' => $namaBahan, 'stokDibutuhkan' => $stokDibutuhkan);
    }
    $stmt->close();

    if (isset($_POST['quantity'])) {
        $submittedQuantity = $_POST['quantity'];

        if ($submittedQuantity == "") {
            echo json_encode(array('error' => 'Quantity is required.'));
            exit();
        } elseif ($submittedQuantity <= 0) {
            echo json_encode(array('error' => 'Quantity must be greater than 0.'));
            exit();
        }

        // Initialize the array to store updated stock quantities
        $updatedStockQuantities = array();

        // Loop through $resultProduksi and update stock accordingly
        foreach ($resultProduksi as $row) {
            $namaBahan = $row['namaBahan'];
            $stokDibutuhkan = $row['stokDibutuhkan'];

            // Fetch current stock from masterbahan
            $queryCurrentStock = "SELECT quantity FROM masterbahan WHERE nama = ?";
            $stmtCurrentStock = $conn->prepare($queryCurrentStock);
            $stmtCurrentStock->bind_param("s", $namaBahan);
            $stmtCurrentStock->execute();
            $stmtCurrentStock->bind_result($currentStock);
            $stmtCurrentStock->fetch();
            $stmtCurrentStock->close();

            // Update the database with the new stock quantity
            $newStock = $currentStock - ($submittedQuantity * $stokDibutuhkan);

            // Store the updated stock quantities in the array
            $updatedStockQuantities[] = array('namaBahan' => $namaBahan, 'stokDibutuhkan' => $stokDibutuhkan, 'currentStock' => $currentStock, 'newStock' => $newStock);
        }
        
        $queryCurrentProductStock = "SELECT quantity FROM masterbahan WHERE nama = ?";
        $stmtCurrentProductStock = $conn->prepare($queryCurrentProductStock);
        $stmtCurrentProductStock->bind_param("s", $selectedDeviceName);
        $stmtCurrentProductStock->execute();
        $stmtCurrentProductStock->bind_result($currentProductStock);
        $stmtCurrentProductStock->fetch();
        $stmtCurrentProductStock->close();

        $newProductStock = $currentProductStock + $submittedQuantity;

        if (isset($_POST['submitForm'])) {
            // Update the masterbahan table
            $updateQueryStock = "UPDATE masterbahan SET quantity = ? WHERE nama = ?";
            $updateStmt = $conn->prepare($updateQueryStock);
            

            foreach ($updatedStockQuantities as $updatedStock) {
                $newStock = $updatedStock['newStock'];
                $namaBahan = $updatedStock['namaBahan'];

                $updateStmt->bind_param("is", $newStock, $namaBahan);
                $updateStmt->execute();
            }

            $stokIdQuery = "SELECT stok_id FROM masterbahan WHERE nama = ?";
            $stokIdStmt = $conn->prepare($stokIdQuery);
            $stokIdStmt->bind_param("s", $selectedDeviceName);
            $stokIdStmt->execute();
            $stokIdStmt->bind_result($stokId);
            $stokIdStmt->fetch();
            $stokIdStmt->close();

            // Insert into historis
            $insertQueryHistoris = "INSERT INTO historis (pengguna, stok_id, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Produksi', ?)";
            $insertStmt = $conn->prepare($insertQueryHistoris);
            $insertStmt->bind_param("siis", $pengguna, $stokId, $submittedQuantity, $_POST['deskripsi']);
            $insertStmt->execute();
            $insertStmt->close();

            // Update Produk quantity pada masterbahan
            $updateQueryMasterBahan = "UPDATE masterbahan SET quantity = ? WHERE nama = ?";
            $updateStmtMasterBahan = $conn->prepare($updateQueryMasterBahan);            
            $updateStmtMasterBahan->bind_param("is", $newProductStock, $selectedDeviceName);
            $updateStmtMasterBahan->execute();
            
            
            $updateStmtMasterBahan->close();            
            
            $updateStmt->close();
        }
        // Return the updated stock quantities
        $responseArray = array('resultProduksi' => $resultProduksi, 'updatedStockQuantities' => $updatedStockQuantities);

        if (!isset($_POST['submitForm'])) {
            echo json_encode($responseArray);
            exit();
        }
    }
}


?>

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

        .lebar-kolom1 {
            width: 5%;
        }

        .lebar-kolom2 {
            width: 40%;
        }

        .lebar-kolom3 {
            width: 20%;
        }

        .lebar-kolom4 {
            width: 20%;
        }

        .lebar-kolom5 {
            width: 15%;
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
                            <a href="prototype.php" class="nav-link">
                                <i class="nav-icon fas fa-screwdriver"></i>
                                <p>
                                    Prototype
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
                        <li class="nav-item">
                            <a href="histori_transaksi.php" class="nav-link">
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
                        <form id="produksiForm" onsubmit="return validateForm()" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleSelectBorderWidth2">Pilih Device <span style="color: red;">*</span></label>
                                    <select class="form-select" id="pilihProduksiDevice" name="selectedDevice">
                                        <option value="">Pilih Produk</option>
                                        <?php
                                        while ($row = $resultProdukPilihan->fetch_assoc()) {
                                            echo '<option value="' . $row['produk'] . '">' . $row['produk'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Kuantitas <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <!-- Input untuk kuantitas -->
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="">
                                    </div>
                                    <button type="button" class="btn btn-outline-info btn-block" id="cekButton" name="cekButton" style="margin-top: 10px; max-width: 180px;"><i class="fas fa-sync-alt" style="margin-right: 8px;" onclick="cekProduksi()"></i>Cek</button>
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
                                                        <th class="text-center lebar-kolom1">No</th>
                                                        <th class="text-center lebar-kolom2">Nama Bahan</th>
                                                        <th class="text-center lebar-kolom3">Stok yang Dibutuhkan</th>
                                                        <th class="text-center lebar-kolom4">Stok Tersisa</th>
                                                        <th class="text-center lebar-kolom5">Cukup?</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="produksiTable">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi<span class="gray-italic-text"> (opsional)</span></label>
                                    <textarea class="form-control" rows="3" placeholder="Masukkan keterangan produksi device ..."></textarea>
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
    <!-- Page specific script -->
    <script>
        $(function() {
            bsCustomFileInput.init();

            // Searchable dropdown
            var select_box_element = document.querySelector('#pilihProduksiDevice');
            dselect(select_box_element, {
                search: true,
            });

            // Event listener for "Cek" button click
            document.getElementById("cekButton").addEventListener("click", function() {
                console.log("Cek button clicked"); // Debugging statement
                cekProduksi();
            });
        });

        function cekProduksi() {
            console.log("Inside cekProduksi function"); // Debugging statement
            var selectedDevice = document.getElementById("pilihProduksiDevice").value;
            var quantity = document.getElementById("quantity").value;

            // Check if a device is selected
            if (selectedDevice === "") {
                alert("Pilih produk terlebih dahulu.");
                return;
            }
            // Check if quantity is provided
            if (quantity === "") {
                alert("Masukkan jumlah produksi device.");
                return;
            }
            // Fetch and update the table
            $.ajax({
                type: "POST",
                url: "produksi.php",
                data: {
                    selectedDevice: selectedDevice,
                    quantity: quantity
                },
                dataType: "json",
                success: function(response) {
                    console.log("AJAX request successful"); // Debugging statement
                    if (response.resultProduksi.length === 0) {
                        alert("Tidak ada data produksi untuk produk yang dipilih.");
                        return;
                    }
                    // Update table rows dynamically
                    var tableBody = document.getElementById("produksiTable");
                    tableBody.innerHTML = ""; // Clear existing rows
                    // Add new rows based on the response array
                    for (var i = 0; i < response.resultProduksi.length; i++) {
                        var isStockSufficient = response.updatedStockQuantities[i].currentStock >= (quantity * response.resultProduksi[i].stokDibutuhkan);
                        var badgeClass = isStockSufficient ? 'bg-success' : 'bg-danger';


                        var newRow = "<tr>" +
                            "<td style='text-align: center;'>" + (i + 1) + "</td>" +
                            "<td style='text-align: center;'>" + response.resultProduksi[i].namaBahan + "</td>" +
                            "<td style='text-align: center;'>" + response.resultProduksi[i].stokDibutuhkan * quantity + "</td>" +
                            "<td style='text-align: center;'>" + response.updatedStockQuantities[i].currentStock + "</td>" +
                            "<td style='text-align: center;'><span class='badge " + badgeClass + "'>" + (isStockSufficient ? "Ya" : "Tidak") + "</span></td>" +
                            "</tr>";
                        tableBody.innerHTML += newRow;
                    }

                    // Show the table
                    tableBody.style.display = "table table-striped";
                },
                error: function(error) {
                    console.error("Error fetching produksi data:", error);
                }
            });
        }

        // Quantity input disabled to prevent bugs
        document.addEventListener("DOMContentLoaded", function() {
            disableQuantityInput();
        });

        function disableQuantityInput() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Pilih produk terlebih dahulu";
            quantityInput.disabled = true;
        }

        $("#pilihProduksiDevice").change(function() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Masukkan jumlah produksi device";
            quantityInput.disabled = false;
        });

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