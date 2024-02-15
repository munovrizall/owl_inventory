<?php

include "../connection.php";

$resultProduksi = "";
$stokDibutuhkan = "";
$currentStock = "";
$totalNewHargaBahan = 0;
$totalNewHargaTotal = 0;

// Fetch data from produksi table
$queryProdukPilihan = "SELECT nama_produk FROM produk ORDER BY nama_produk";
$resultProdukPilihan = $conn->query($queryProdukPilihan);

if (isset($_POST['selectedDevice'])) {
    $selectedDeviceName = $_POST['selectedDevice'];
    $pengguna = isset($_SESSION['username']) ? $_SESSION['username'] : '';

    // Milih bahan untuk produksi
    $query = "SELECT nama_bahan, quantity, harga_bahan FROM bahan_produksi WHERE produk = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedDeviceName);
    $stmt->execute();
    $stmt->bind_result($namaBahan, $stokDibutuhkan, $hargaBahan);
    $resultProduksi = array();
    while ($stmt->fetch()) {
        $resultProduksi[] = array('namaBahan' => $namaBahan, 'stokDibutuhkan' => $stokDibutuhkan, 'hargaBahan' => $hargaBahan);
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
            $hargaBahan = $row['hargaBahan'];

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
            $newHargaBahan = $hargaBahan * $stokDibutuhkan;
            $newHargaTotal = $hargaBahan * $submittedQuantity * $stokDibutuhkan;

            $totalNewHargaBahan += $newHargaBahan;
            $totalNewHargaTotal += $newHargaTotal;

            // Store the updated stock quantities in the array
            $updatedStockQuantities[] = array(
                'namaBahan' => $namaBahan, 'stokDibutuhkan' => $stokDibutuhkan, 'hargaBahan' => $hargaBahan,
                'currentStock' => $currentStock, 'newStock' => $newStock, 'newHargaBahan' => $newHargaBahan, 'newHargaTotal' => $newHargaTotal,
                'totalNewHargaBahan' => $totalNewHargaBahan, 'totalNewHargaTotal' => $totalNewHargaTotal
            );
        }

        // Update hpp_produk column in produk table
        $totalNewHargaBahan = end($updatedStockQuantities)['totalNewHargaBahan'];
        $updateQueryHPP = "UPDATE produk SET hpp_produk = ? WHERE nama_produk = ?";
        $stmtUpdateHPP = $conn->prepare($updateQueryHPP);
        $stmtUpdateHPP->bind_param("is", $totalNewHargaBahan, $selectedDeviceName);
        $stmtUpdateHPP->execute();
        $stmtUpdateHPP->close();

        $queryCurrentProductStock = "SELECT quantity FROM produk WHERE nama_produk = ?";
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

            $insertQueryHistorisMinus = "INSERT INTO historis (pengguna, nama_barang, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Produksi', ?)";
            $insertStmtMinus = $conn->prepare($insertQueryHistorisMinus);

            foreach ($resultProduksi as $row) {
                $namaBahan = $row['namaBahan'];
                $stokDibutuhkan = $row['stokDibutuhkan'];
                $minusBahan = -1 * ($submittedQuantity * $stokDibutuhkan);

                $insertStmtMinus->bind_param("ssis", $pengguna, $namaBahan, $minusBahan, $_POST['deskripsi']);
                if (!$insertStmtMinus->execute()) {
                    // Log or display the error
                    echo "Error in insertStmtMinus execution: " . $insertStmtMinus->error;
                }
            }

            $insertStmtMinus->close();

            // Insert into historis
            $insertQueryHistoris = "INSERT INTO historis (pengguna, nama_barang, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Produksi', ?)";
            $insertStmt = $conn->prepare($insertQueryHistoris);
            $insertStmt->bind_param("ssis", $pengguna, $selectedDeviceName, $submittedQuantity, $_POST['deskripsi']);
            $insertStmt->execute();
            $insertStmt->close();

            // Update Produk quantity pada masterbahan
            $updateQueryProduk = "UPDATE produk SET quantity = ? WHERE nama_produk = ?";
            $updateStmtProduk = $conn->prepare($updateQueryProduk);
            $updateStmtProduk->bind_param("is", $newProductStock, $selectedDeviceName);
            $updateStmtProduk->execute();
            $updateStmtProduk->close();

            $insertStmtQualityControl = "INSERT INTO inventaris_produk (produk) VALUES (?)";
            $insertStmtQualityControl = $conn->prepare($insertStmtQualityControl);

            for ($i = 0; $i < $submittedQuantity; $i++) {
                $insertStmtQualityControl->bind_param("s", $selectedDeviceName);
                $insertStmtQualityControl->execute();
            }

            $insertStmtQualityControl->close();


            $updateStmt->close();
        }
        // Return the updated stock quantities
        $responseArray = array(
            'resultProduksi' => $resultProduksi, 'updatedStockQuantities' => $updatedStockQuantities,
            'totalNewHargaBahan' => $totalNewHargaBahan, 'totalNewHargaTotal' => $totalNewHargaTotal
        );

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
    <title>Produksi Device</title>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        .gray-italic-text {
            color: #808080;
            font-style: italic;
        }

        .lebar-kolom1 {
            width: 5%;
        }

        .lebar-kolom2 {
            width: 30%;
        }

        .lebar-kolom3 {
            width: 15%;
        }

        .lebar-kolom4 {
            width: 15%;
        }

        .lebar-kolom5 {
            width: 10%;
        }

        .lebar-kolom6 {
            width: 10%;
        }

        .lebar-kolom7 {
            width: 15%;
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
                            <h1>Produksi Device</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Produksi</li>
                                <li class="breadcrumb-item active">Produksi Device</li>
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
                                    <select class="form-control select2" id="pilihProduksiDevice" name="selectedDevice">
                                        <option value="">--- Pilih Produk ---</option>
                                        <?php
                                        while ($row = $resultProdukPilihan->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_produk'] . '">' . $row['nama_produk'] . '</option>';
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
                                                        <th class="text-center lebar-kolom6">Harga Bahan</th>
                                                        <th class="text-center lebar-kolom7" style="min-width: 120px;">Total Harga Bahan</th>
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
                                    <textarea class="form-control" rows="3" id="deskripsi" name="deskripsi" placeholder="Masukkan keterangan produksi device ..."></textarea>
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
        });

        $(function() {
            bsCustomFileInput.init();

            // Event listener for "Cek" button click
            document.getElementById("cekButton").addEventListener("click", function() {
                cekProduksi();
            });
        });

        function cekProduksi() {
            var selectedDevice = document.getElementById("pilihProduksiDevice").value;
            var quantity = document.getElementById("quantity").value;

            // Check if a device is selected
            if (selectedDevice === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Pilih produk terlebih dahulu',
                    confirmButtonText: 'OK (enter)'
                });
                return;
            }
            // Check if quantity is provided
            if (quantity === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Masukkan jumlah produksi device!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                });
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
                    if (response.resultProduksi.length === 0) {
                        alert("Tidak ada data produksi untuk produk yang dipilih.");
                        return;
                    }
                    // Update table rows dynamically
                    var tableBody = document.getElementById("produksiTable");
                    tableBody.innerHTML = ""; // Clear existing rows
                    var totalNewHargaBahan = response.totalNewHargaBahan;
                    var totalNewHargaTotal = response.totalNewHargaTotal;

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
                            "<td style='text-align: center;'>" + formatCurrency((response.resultProduksi[i].hargaBahan !== null ? response.resultProduksi[i].hargaBahan : 0)) + "</td>" +
                            "<td style='text-align: center;'>" + formatCurrency(response.resultProduksi[i].hargaBahan * (response.resultProduksi[i].stokDibutuhkan * quantity)) + "</td>" +
                            "</tr>";
                        tableBody.innerHTML += newRow;
                    }

                    // Append total row to the table
                    var totalRowBahan = "<tr><td colspan='7' style='text-align: left;'>Biaya pembuatan satu produk&nbsp: " + formatCurrency(totalNewHargaBahan) + "</td></tr>";
                    tableBody.innerHTML += totalRowBahan;

                    // Append total row for totalNewHargaTotal
                    var totalRowTotal = "<tr><td colspan='7' style='text-align: left;'><b>Total keseluruhan biaya produksi: " + formatCurrency(totalNewHargaTotal) + "</b></td></tr>";
                    tableBody.innerHTML += totalRowTotal;

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
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi gagal',
                    text: 'Harap cek semua formulir!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                });
            }
        }

        function validateForm() {
            // Add your validation logic here
            // Return true if the form is valid, false otherwise
            var selectedDevice = document.getElementById("pilihProduksiDevice").value;
            var quantity = document.getElementById("quantity").value;

            // Example validation: Check if the selected device and quantity are not empty
            if (selectedDevice === "" || quantity === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                });
                return false;
            } else if (quantity == "0") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Kuantitas harus lebih besar dari 0!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                });
                return false;
            } else {
                var tableRows = document.getElementById("produksiTable").getElementsByTagName("tr");
                for (var i = 0; i < tableRows.length; i++) {
                    var badge = tableRows[i].getElementsByClassName("bg-danger");
                    if (badge.length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Bahan tidak mencukupi!',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK (enter)'
                        });
                        return false;

                    }
                }
            }
            // Add more validation as needed...
            alert('Produksi berhasil!\nKlik ok atau enter');

            return true; // If all validations pass
        }

        document.getElementById("quantity").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Prevent form submission
                cekProduksi(); // Trigger the Cek button click
            }
        });

        document.getElementById('deskripsi').addEventListener('keyup', function(event) {
            if (event.keyCode === 13) {
                document.querySelector('[name="submitForm"]').click();
            }
        });

        function formatCurrency(angka) {
            return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>

</body>

</html>