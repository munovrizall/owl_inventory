<?php

include "../connection.php";

$queryBahan = "SELECT * FROM masterbahan ORDER BY nama";
$resultBahan = $conn->query($queryBahan);

$queryTransaksi =
    "SELECT detail_maintenance.*, transaksi_maintenance.nama_client
FROM detail_maintenance
INNER JOIN transaksi_maintenance ON detail_maintenance.transaksi_id = transaksi_maintenance.transaksi_id
WHERE cek_barang = 0 OR berita_as = 0 OR administrasi = 0 OR pengiriman = 0";
$resultTransaksi = $conn->query($queryTransaksi);

// Cek apakah hasil query kosong
if (empty($resultTransaksi)) {
    $resultTransaksi = "Tidak ada transaksi";
}

$stockQuantity = ""; // Default value, replace it with the actual stock quantity based on the selected item from the database
$newStockQuantity = "";

if (isset($_POST['quantity'])) {
    $selectedItemId = $_POST['selectedItem'];

    // Fetch the username from the POST data
    $pengguna = isset($_SESSION['username']) ? $_SESSION['username'] : '';


    // Fetch the stock quantity from the database based on the selected item
    $query = "SELECT quantity FROM masterbahan WHERE stok_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selectedItemId);
    $stmt->execute();
    $stmt->bind_result($stockQuantity);
    $stmt->fetch();
    $stmt->close();

    $submittedQuantity = $_POST['quantity'];
    if ($submittedQuantity == "") {
        echo json_encode(array('currentStock' => $stockQuantity, 'newStock' => $newStockQuantity));
        exit();
    } elseif ($submittedQuantity <= 0) {
        echo "Kuantitas yang dimasukkan harus lebih besar dari 0";
        exit();
    }

    // Update the database with the new stock quantity
    $newStockQuantity = $stockQuantity - $submittedQuantity;

    if ($newStockQuantity < 0) {
        echo "Stok bahan tidak mencukupi untuk keperluan maintenance.";
        exit();
    }

    $updateQueryStock = "UPDATE masterbahan SET quantity = ? WHERE stok_id = ?";
    $updateStmt = $conn->prepare($updateQueryStock);
    $updateStmt->bind_param("ii", $newStockQuantity, $selectedItemId);
    $updateStmt->execute();
    $updateStmt->close();

    $selectNamaBahan = "SELECT nama FROM masterbahan WHERE stok_id = ?";
    $selectStmt = $conn->prepare($selectNamaBahan);
    $selectStmt->bind_param("i", $selectedItemId);
    $selectStmt->execute();
    $selectStmt->bind_result($namaBahan);
    $selectStmt->fetch();
    $selectStmt->close();


    // Insert a new record into the 'historis' table
    $insertQueryHistoris = "INSERT INTO historis (pengguna, nama_barang, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Maintenance', ?)";
    $insertStmt = $conn->prepare($insertQueryHistoris);
    $insertStmt->bind_param("ssis", $pengguna, $namaBahan, $submittedQuantity, $_POST['deskripsi']);

    $insertStmt->execute();
    $insertStmt->close();

    // Return the updated stock quantity
    echo json_encode(array('currentStock' => $stockQuantity, 'newStock' => $newStockQuantity));
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Maintenance</title>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        #successMessage {
            display: none;
            /* Hide the success message initially */
        }

        .gray-italic-text {
            color: #808080;
            font-style: italic;
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
            <a href="../homepage.php" class="brand-link">
                <img src="../assets/adminlte/dist/img/OWLlogo.png" alt="OWL Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
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
                            <a href="../homepage.php" class="nav-link">
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
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="../produksi/produksi.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Produksi Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../produksi/quality_control.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Quality Control</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../produksi/inventaris_device.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inventaris Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../produksi/pengiriman.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pengiriman Device</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="../maintenance.php" class="nav-link active">
                                <i class="nav-icon fas fa-wrench"></i>
                                <p>
                                    Maintenance
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./input.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./monitoring.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./update.php" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Update</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="../prototype.php" class="nav-link">
                                <i class="nav-icon fas fa-screwdriver"></i>
                                <p>
                                    Prototype
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../restock.php" class="nav-link">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>
                                    Restock
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">TAMBAH DATA</li>
                        <li class="nav-item">
                            <a href="../master_bahan.php" class="nav-link">
                                <i class="nav-icon fa fa-pen"></i>
                                <p>
                                    Master Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../master_device.php" class="nav-link">
                                <i class="nav-icon fas fa-cube"></i>
                                <p>
                                    Master Device
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../harga_bahan.php" class="nav-link">
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
                                    <a href="../perusahaan/tambah_perusahaan.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tambah Perusahaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../perusahaan/list_perusahaan.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Perusahaan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-header">PELAPORAN</li>
                        <li class="nav-item">
                            <a href="../stok_bahan.php" class="nav-link">
                                <i class="nav-icon ion ion-pie-graph"></i>
                                <p>
                                    Stok Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../stok_produk.php" class="nav-link">
                                <i class="nav-icon fas fa-microchip"></i>
                                <p>
                                    Stok Produk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../histori_transaksi.php" class="nav-link">
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
                            <h1>Update Maintenance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Maintenance</li>
                                <li class="breadcrumb-item active">Update</li>
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
                            <h3 class="card-title">Mengurangi Bahan Untuk Keperluan Maintenance</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="maintenanceForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pilihBahanMaintenance">Pilih Bahan <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihBahanMaintenance" name="selectedItem">
                                        <option value="">--- Pilih Bahan ---</option>
                                        <?php
                                        while ($row = $resultBahan->fetch_assoc()) {
                                            echo '<option value="' . $row['stok_id'] . '">' . $row['nama'] . '</option>';
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
                                </div>
                                <p id="stockMessage">Stok Bahan Tersisa: <?php echo $stockQuantity; ?></p>
                                <p id="successMessage">Stok Bahan Terkini: <?php echo $newStockQuantity; ?></p>
                                <div class="form-group">
                                    <label for="pilihTransaksi">Pilih Transaksi <span class="gray-italic-text"> (opsional)</span></label>
                                    <select class="form-control select2" id="pilihTransaksi" name="selectedTransaksi">
                                        <option value="">--- Pilih Transaksi ---</option>
                                        <?php
                                        while ($row = $resultTransaksi->fetch_assoc()) {
                                            echo '<option
                                            value="Maintenance/' . $row['nama_client'] . '/' . $row['transaksi_id'] . '/' . $row['detail_id'] . '/' . $row['produk_mt'] . '">
                                            Maintenance/' . $row['nama_client'] . '/' . $row['transaksi_id'] . '/' . $row['detail_id'] . '/' . $row['produk_mt'] . '
                                            </option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="deksripsi">Deskripsi<span class="gray-italic-text"> (opsional)</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan keterangan penggunaan bahan ..."></textarea>
                                </div>
                            </div>

                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" id="submitButton" class="btn btn-primary" onclick="if(validateForm()) { validateSuccess();}">Submit</button>
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
    <script src="../assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="../assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../assets/adminlte/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 Toast -->
    <script src="../assets/adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Select2 -->
    <script src="../assets/adminlte/plugins/select2/js/select2.full.min.js"></script>

    <!-- Page specific script -->
    <script>
        // Select2 Dropdown
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                containerCssClass: 'height-40px',
            });

            $("#pilihTransaksi").change(function() {
                var selectedTransaksi = $("#pilihTransaksi").val();
                console.log(selectedTransaksi);
                // Extract the value you want from the selectedTransaksi
                var extractedValue = ""; // Update this based on your logic

                // Update the deskripsi field
                $("#deskripsi").val(selectedTransaksi);
            });
        });

        $(function() {
            bsCustomFileInput.init();

            // Add an event listener to the select element
            $("#pilihBahanMaintenance").change(function() {
                validateCurrentStock();
            });
        });

        function validateForm() {
            var selectedItem = document.getElementById("pilihBahanMaintenance").value;
            var quantity = document.getElementById("quantity").value;

            if (selectedItem === "" || quantity === "" || quantity <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetForm();
                    }
                });
                return false;
            }

            return true;
        }

        function updateStockMessage() {
            var stockMessage = document.getElementById("stockMessage");
            var selectedQuantity = parseInt(document.getElementById("quantity").value, 10);

            // Update stock message dynamically based on the selected item's stock quantity
            stockMessage.innerText = "Stok Bahan Tersisa: " + (<?php echo $stockQuantity; ?> - selectedQuantity);
        }

        function validateCurrentStock() {
            // Get the form data
            var formData = $("#maintenanceForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock quantity
            $.ajax({
                type: "POST",
                url: "update.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    // Hide the stock message
                    document.getElementById("successMessage").style.display = "none";
                    // Update the stock message with the fetched quantity
                    document.getElementById("stockMessage").innerText = "Stok Bahan Tersisa: " + response.currentStock;
                },
                error: function(error) {
                    alert("Error, refresh the page!");
                }
            });
        }

        function validateSuccess() {
            // Get the form data
            var formData = $("#maintenanceForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock quantity
            $.ajax({
                type: "POST",
                url: "update.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    // Hide the stock message
                    document.getElementById("stockMessage").innerText = "Stok Bahan Tersisa: ";

                    Swal.fire({
                        icon: 'success',
                        title: 'Stok berhasil diambil!',
                        text: 'Stok terbaru adalah ' + response.newStock + ' bahan',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK (enter)'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetForm();
                        }
                    });

                    resetForm();

                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Kurang',
                        text: 'Kurangi kuantitas yang diinput!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK (enter)'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetForm();
                        }
                    });
                }
            });
        }

        function resetForm() {
            document.getElementById("maintenanceForm").reset();
            resetDropdown();
            disableQuantityInput();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihBahanMaintenance");
            dropdown.selectedIndex = 0;
            // reset ke pilihan pertama
            const dropdownTransaksi = document.getElementById("pilihTransaksi");
            dropdownTransaksi.selectedIndex = 0; // reset ke pilihan pertama

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
            dropdownTransaksi.dispatchEvent(new Event('change'));
        }

        // Quantity input disabled to prevent bugs
        document.addEventListener("DOMContentLoaded", function() {
            disableQuantityInput();
        });

        function disableQuantityInput() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Pilih bahan terlebih dahulu";
            quantityInput.disabled = true;
        }

        $("#pilihBahanMaintenance").change(function() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Masukkan jumlah stok bahan yang ingin digunakan";
            quantityInput.disabled = false;
        });

        // When user press enter on keyboard
        var quantityInput = document.getElementById('quantity');
        quantityInput.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                submitForm();
            }
        });

        var deksripsiInput = document.getElementById('deskripsi');
        deksripsiInput.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                submitForm();
            }
        });

        function submitForm() {
            document.getElementById('submitButton').click();
        }
    </script>
</body>

</html>