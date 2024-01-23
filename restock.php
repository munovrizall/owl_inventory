<?php

include "connection.php";

$queryBahan = "SELECT * FROM masterbahan ORDER BY nama";
$resultBahan = $conn->query($queryBahan);

// Fetch data from masterkelompok table
$queryKelompok = "SELECT kelompok_id, nama_kelompok FROM masterkelompok ORDER BY nama_kelompok";
$resultKelompok = $conn->query($queryKelompok);

if (!$resultKelompok) {
    die("Error fetching kelompok data: " . $conn->error);
}

if (isset($_POST['kelompok'])) {
    $selectedKelompok = $_POST['kelompok'];

    // Fetch bahan options based on the selected kelompok
    $queryBahan = "SELECT * FROM masterbahan WHERE kelompok = ? ORDER BY nama";
    $stmt = $conn->prepare($queryBahan);
    $stmt->bind_param("s", $selectedKelompok);
    $stmt->execute();
    $resultBahan = $stmt->get_result();

    $bahanOptions = array();

    while ($row = $resultBahan->fetch_assoc()) {
        $bahanOptions[] = $row;
    }

    $stmt->close();

    // Return bahan options as JSON
    echo json_encode($bahanOptions);
    exit();
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
    $newStockQuantity = $stockQuantity + $submittedQuantity;
    $updateQueryStock = "UPDATE masterbahan SET quantity = ? WHERE stok_id = ?";
    $updateStmt = $conn->prepare($updateQueryStock);
    $updateStmt->bind_param("ii", $newStockQuantity, $selectedItemId);
    $updateStmt->execute();
    $updateStmt->close();

    // Insert a new record into the 'historis' table
    $insertQueryHistoris = "INSERT INTO historis (pengguna, stok_id, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Restock', ?)";
    $insertStmt = $conn->prepare($insertQueryHistoris);
    $insertStmt->bind_param("siis", $pengguna, $selectedItemId, $submittedQuantity, $_POST['deskripsi']);

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
    <title>Restock</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
                            <a href="../maintenance.php" class="nav-link">
                                <i class="nav-icon fas fa-wrench"></i>
                                <p>
                                    Maintenance
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="maintenance/input.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="maintenance/monitoring.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="maintenance/update.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Update</p>
                                    </a>
                                </li>
                            </ul>
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
                            <a href="restock.php" class="nav-link active">
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
                            <h1>Restock</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Restock</li>
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
                            <h3 class="card-title">Menambah Stok Bahan</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="restockForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="pilihNamaKelompok">Pilih Kelompok<span class="gray-italic-text">
                                                (opsional)</span></label>
                                    </div>
                                    <select class="form-control select2" id="pilihNamaKelompok" name="kelompok">
                                        <option value="">--- Pilih Kelompok ---</option>
                                        <?php
                                        while ($row = $resultKelompok->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_kelompok'] . '">' . $row['nama_kelompok'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" id="bahanDropdownContainer">
                                    <label for="pilihBahanRestock">Pilih Bahan <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihBahanRestock" name="selectedItem">
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
                                    <label for="deskripsi">Deskripsi<span class="gray-italic-text"> (opsional)</span>
                                    </label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan keterangan pembelian bahan ..."></textarea>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" id="submitButton" class="btn btn-primary" onclick="if(validateForm()) { validateSuccess(); resetForm(); }">Submit</button>
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
    <!-- Select2 -->
    <script src="assets/adminlte/plugins/select2/js/select2.full.min.js"></script>

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
        });

        $("#pilihNamaKelompok").change(function() {
            var selectedKelompok = $(this).val();

            // Use Ajax to fetch data based on the selected kelompok
            $.ajax({
                type: "POST",
                url: "restock.php", // Replace with the actual file that fetches bahan based on kelompok
                data: { kelompok: selectedKelompok },
                dataType: "json",
                success: function(response) {
                    // Clear existing options
                    $('#pilihBahanRestock').empty();

                    // Add new options based on the fetched data
                    $.each(response, function(index, value) {
                        $('#pilihBahanRestock').append('<option value="' + value.stok_id + '">' + value.nama + '</option>');
                    });

                    // Trigger change event to update Select2
                    $('#pilihBahanRestock').trigger('change');
                },
                error: function(error) {
                    alert("Error fetching bahan data.");
                }
            });
        });

        $(function() {
            bsCustomFileInput.init();

            // Add an event listener to the select element
            $("#pilihBahanRestock").change(function() {
                validateCurrentStock();
            });
        });

        function validateForm() {
            var selectedItem = document.getElementById("pilihBahanRestock").value;
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
            stockMessage.innerText = "Stok Bahan Tersisa: " + (<?php echo $stockQuantity; ?> + selectedQuantity);
        }

        function validateCurrentStock() {
            // Get the form data
            var formData = $("#restockForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock quantity
            $.ajax({
                type: "POST",
                url: "restock.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    // Hide the stock message
                    document.getElementById("successMessage").style.display = "none";
                    // Update the stock message with the fetched quantity
                    document.getElementById("stockMessage").innerText = "Stok Bahan Tersisa: " + response
                        .currentStock;
                },
                error: function(error) {
                    alert("Error, refresh the page!");
                }
            });
        }

        function validateSuccess() {
            // Get the form data
            var formData = $("#restockForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock quantity
            $.ajax({
                type: "POST",
                url: "restock.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    document.getElementById("stockMessage").innerText = "Stok Bahan Tersisa: ";

                    Swal.fire({
                        icon: 'success',
                        title: 'Stok berhasil ditambahkan!',
                        text: 'Stok terbaru adalah ' + response.newStock + ' bahan',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK (enter)'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetForm();
                        }
                    });

                },
                error: function(error) {
                    alert("Error fetching new stock quantity.");
                }
            });
        }

        function resetForm() {
            document.getElementById("restockForm").reset();
            resetDropdown();
            disableQuantityInput();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihBahanRestock");
            dropdown.selectedIndex = 0; // reset ke pilihan pertama

            // jika multiple selection
            dropdown.querySelectorAll("option:checked").forEach(option => {
                option.selected = false;
            });

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
        }

        // Quantity input disabled to prevent bugs
        document.addEventListener("DOMContentLoaded", function() {
            disableQuantityInput();
        });

        function disableQuantityInput() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Pilih kelompok dan bahan terlebih dahulu";
            quantityInput.disabled = true;
        }

        $("#pilihBahanRestock").change(function() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Masukkan jumlah stok bahan yang dibeli";
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