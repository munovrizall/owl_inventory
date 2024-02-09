<?php

include "connection.php";

$queryBahan = "SELECT * FROM masterbahan ORDER BY nama";
$resultBahan = $conn->query($queryBahan);

$currentHargaBahan = "";
$newHargaBahan = "";

if (isset($_POST['selectedItem'])) {
    $selectedItem = $_POST['selectedItem'];
    $newHargaBahan = $_POST['price'];

    // Fetch the username from the POST data
    $pengguna = isset($_SESSION['username']) ? $_SESSION['username'] : '';


    // Fetch the stock harga_bahan from the database based on the selected item
    $query = "SELECT harga_bahan FROM masterbahan WHERE nama = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedItem);
    $stmt->execute();
    $stmt->bind_result($currentHargaBahan);
    $stmt->fetch();
    $stmt->close();

    if ($newHargaBahan == "") {
        echo json_encode(array('currentHargaBahan' => $currentHargaBahan, 'newHargaBahan' => $newHargaBahan));
        exit();
    } elseif ($newHargaBahan <= 0) {
        echo "Harga yang dimasukkan harus lebih besar dari 0";
        exit();
    }

    // Update the database with the new stock harga_bahan

    $updateQueryHarga = "UPDATE masterbahan SET harga_bahan = ? WHERE nama = ?";
    $updateStmtHarga = $conn->prepare($updateQueryHarga);
    $updateStmtHarga->bind_param("is", $newHargaBahan, $selectedItem);
    $updateStmtHarga->execute();
    $updateStmtHarga->close();

    $updateQueryHargaProduksi = "UPDATE bahan_produksi SET harga_bahan = ? WHERE nama_bahan = ?";
    $updateStmtHargaProduksi = $conn->prepare($updateQueryHargaProduksi);
    $updateStmtHargaProduksi->bind_param("is", $newHargaBahan, $selectedItem);
    $updateStmtHargaProduksi->execute();
    $updateStmtHargaProduksi->close();

    // Return the updated stock harga_bahan
    echo json_encode(array('currentHargaBahan' => $currentHargaBahan, 'newHargaBahan' => $newHargaBahan));
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Harga Bahan</title>

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
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="produksi/produksi.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Produksi Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="produksi/quality_control.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Quality Control</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="produksi/inventaris_device.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inventaris Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="produksi/pengiriman.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pengiriman Device</p>
                                    </a>
                                </li>
                            </ul>
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
                        <li class="nav-item">
                            <a href="harga_bahan.php" class="nav-link active">
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
                                    <a href="perusahaan/tambah_perusahaan.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tambah Perusahaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="perusahaan/list_perusahaan.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Perusahaan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-header">PELAPORAN</li>
                        <li class="nav-item">
                            <a href="stok_bahan.php" class="nav-link">
                                <i class="nav-icon ion ion-pie-graph"></i>
                                <p>
                                    Stok Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="stok_produk.php" class="nav-link">
                                <i class="nav-icon fas fa-microchip"></i>
                                <p>
                                    Stok Produk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item" style="margin-bottom: 40px;">
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
                            <h1>Harga Bahan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Harga Bahan</li>
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
                            <h3 class="card-title">Mengupdate Harga Bahan</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="priceForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pilihBahan">Pilih Bahan <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihBahan" name="selectedItem">
                                        <option value="">--- Pilih Bahan ---</option>
                                        <?php
                                        while ($row = $resultBahan->fetch_assoc()) {
                                            echo '<option value="' . $row['nama'] . '">' . $row['nama'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <p id="priceMessage">Harga Bahan Saat ini: <?php echo $currentHargaBahan; ?></p>
                                <p id="successMessage">Harga Bahan Terbaru: <?php echo $newHargaBahan; ?></p>
                                <div class="form-group">
                                    <label for="price">Harga Bahan Terbaru <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="price" name="price" min="0" value="" placeholder="Masukkan harga bahan terbaru">
                                    </div>
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

        $(function() {
            bsCustomFileInput.init();

            // Add an event listener to the select element
            $("#pilihBahan").change(function() {
                validatecurrentHargaBahan();
            });
        });

        function validateForm() {
            var selectedItem = document.getElementById("pilihBahan").value;
            var harga_bahan = document.getElementById("price").value;

            if (selectedItem === "" || harga_bahan === "" || harga_bahan <= 0) {
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

        function updatepriceMessage() {
            var priceMessage = document.getElementById("priceMessage");
            var selectedharga_bahan = parseInt(document.getElementById("harga_bahan").value, 10);

            // Update stock message dynamically based on the selected item's stock harga_bahan
            priceMessage.innerText = "Stok Bahan Tersisa: " + (<?php echo $currentHargaBahan; ?> + selectedharga_bahan);
        }

        function validatecurrentHargaBahan() {
            // Get the form data
            var formData = $("#priceForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock harga_bahan
            $.ajax({
                type: "POST",
                url: "harga_bahan.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    // Hide the stock message
                    document.getElementById("successMessage").style.display = "none";
                    // Update the stock message with the fetched harga_bahan
                    document.getElementById("priceMessage").innerText = "Harga Bahan Saat Ini: " + 
                    formatCurrency(response.currentHargaBahan);
                },
                error: function(error) {
                    alert("Error, refresh the page!");
                }
            });
        }

        function validateSuccess() {
            // Get the form data
            var formData = $("#priceForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock harga_bahan
            $.ajax({
                type: "POST",
                url: "harga_bahan.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    document.getElementById("priceMessage").innerText = "Harga Bahan Saat Ini: Rp. ";

                    Swal.fire({
                        icon: 'success',
                        title: 'Harga bahan berhasil diperbarui!',
                        text: 'Harga bahan terbaru adalah ' + formatCurrency(response.newHargaBahan) + ' per piece',
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
                    alert("Error fetching new material price.");
                }
            });
        }

        function resetForm() {
            document.getElementById("priceForm").reset();
            resetDropdown();
            disableharga_bahanInput();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihBahan");
            dropdown.selectedIndex = 0; // reset ke pilihan pertama

            // jika multiple selection
            dropdown.querySelectorAll("option:checked").forEach(option => {
                option.selected = false;
            });

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
        }

        // harga_bahan input disabled to prevent bugs
        document.addEventListener("DOMContentLoaded", function() {
            disableharga_bahanInput();
        });

        function disableharga_bahanInput() {
            const harga_bahanInput = document.getElementById("price");
            harga_bahanInput.placeholder = "Pilih bahan terlebih dahulu";
            harga_bahanInput.disabled = true;
        }

        $("#pilihBahan").change(function() {
            const harga_bahanInput = document.getElementById("price");
            harga_bahanInput.placeholder = "Masukkan jumlah stok bahan yang dibeli";
            harga_bahanInput.disabled = false;
        });

        // When user press enter on keyboard
        var harga_bahanInput = document.getElementById('price');
        harga_bahanInput.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                submitForm();
            }
        });

        function submitForm() {
            document.getElementById('submitButton').click();
        }

        function formatCurrency(angka) {
            return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>

</html>