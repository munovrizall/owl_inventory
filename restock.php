<?php

include "connection.php";

$queryBahan = "SELECT * FROM masterbahan ORDER BY nama";
$resultBahan = $conn->query($queryBahan);

$stockQuantity = ""; // Default value, replace it with the actual stock quantity based on the selected item from the database
$newStockQuantity = "";

if (isset($_POST['quantity'])) {
    $selectedItemId = $_POST['selectedItem'];
    $submittedQuantity = $_POST['quantity'];

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

    $queryNamaBahan = "SELECT nama FROM masterbahan WHERE stok_id = ?";
    $stmtNamaBahan = $conn->prepare($queryNamaBahan);
    $stmtNamaBahan->bind_param("i", $selectedItemId);
    $stmtNamaBahan->execute();
    $stmtNamaBahan->bind_result($namaBahan);
    $stmtNamaBahan->fetch();
    $stmtNamaBahan->close();

    // Insert a new record into the 'historis' table
    $insertQueryHistoris = "INSERT INTO historis (pengguna, nama_barang, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Restock', ?)";
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
    <title>Restock</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        <?php
        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        include $rootPath . "/owl_inventory/includes/navbar.php";
        include $rootPath . "/owl_inventory/includes/sidebar.php";
        ?>

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
                                    <label for="pilihBahanRestock">Pilih Bahan <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihBahanRestock" name="selectedItem">
                                        <option value="">--- Pilih Bahan ---</option>
                                        <?php
                                        while ($row = $resultBahan->fetch_assoc()) {
                                            echo '<option value="' . $row['stok_id'] . '">' . $row['kelompok'] . ' - ' . $row['nama'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Kuantitas <span style="color: red;">*</span></label>
                                    <div class="input-group">
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
            quantityInput.placeholder = "Pilih bahan terlebih dahulu";
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