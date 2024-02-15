<?php

include "connection.php";

if (isset($_GET["getDropdownOptions"])) {

    $queryMasterBahan = "SELECT nama FROM masterbahan ORDER BY nama";

    $resultMasterBahan = $conn->query($queryMasterBahan);

    $options = '<option value="" selected disabled>Pilih Bahan</option>';

    if ($resultMasterBahan && $resultMasterBahan->num_rows > 0) {
        while ($row = $resultMasterBahan->fetch_assoc()) {
            $options .= '<option value="' . $row['nama'] . '">' . $row['nama'] . '</option>';
        }
    }
    echo $options;
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle the POST request for submitting form data
    $namaDevice = $_POST["namaDevice"];

    $queryProduk = "INSERT INTO produk (nama_produk, quantity) VALUES (?, '0')";
    $stmtProduk = $conn->prepare($queryProduk);
    $stmtProduk->bind_param("s", $namaDevice);
    $stmtProduk->execute();
    $stmtProduk->close();

    // Check if 'bahan' and 'quantity' arrays are set in POST
    if (isset($_POST["pilihNamaBahan"]) && isset($_POST["quantity"])) {
        $bahanArray = $_POST["pilihNamaBahan"];
        $quantityArray = $_POST["quantity"];


        // Loop through the arrays and insert records
        foreach ($bahanArray as $key => $bahan) {
            $quantity = $quantityArray[$key];

            // Use prepared statements
            $checkQuery = "SELECT COUNT(*) FROM bahan_produksi WHERE produk = ? AND nama_bahan = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("ss", $namaDevice, $bahan);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                echo "Error: Device and Bahan combination already exists in the database.";
            } else {
                $queryHarga = "SELECT harga_bahan FROM masterbahan WHERE nama = ?";
                $stmtHarga = $conn->prepare($queryHarga);
                $stmtHarga->bind_param("s", $bahan);
                $stmtHarga->execute();
                $stmtHarga->bind_result($hargaBahan);
                $stmtHarga->fetch();
                $stmtHarga->close();

                $query = "INSERT INTO bahan_produksi (produk, nama_bahan, quantity, harga_bahan) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssii", $namaDevice, $bahan, $quantity, $hargaBahan);

                if ($stmt->execute()) {
                    echo "Data berhasil ditambahkan ke tabel bahan_produksi dan tabel masterbahan.";
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }

        // Insert the new record into masterbahan table
        $queryMasterDevice = "INSERT INTO masterbahan (kelompok, nama, quantity) VALUES ('Barang Jadi', ?, 0)";
        $stmtMasterBahan = $conn->prepare($queryMasterDevice);
        $stmtMasterBahan->bind_param("s", $namaDevice);
        $stmtMasterBahan->execute();
        $stmtMasterBahan->close();
    } else {
        echo "Error: Bahan and Quantity arrays are not set.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Device</title>

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
    <!-- Select2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        .input-group-append label {
            margin-right: 24px;
        }

        .lebar-kolom1 {
            width: 78%;
        }

        .lebar-kolom2 {
            width: 12%;
        }

        .lebar-kolom3 {
            width: 10%;
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
                                            <label for="namaDevice">Nama Device <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2" id="namaDevice" name="namaDevice" placeholder="Masukkan nama device baru">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="myTable" class=" table order-list table-striped">
                                            <thead>
                                                <tr>
                                                    <td class="text-center lebar-kolom1"><b>Nama Bahan <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom2"><b>Kuantitas <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom3"><b>Aksi</b></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>

                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" style="text-align: left;">
                                                        <input type="button" class="btn btn-outline-info btn-block" id="addrow" value="+  Tambah Bahan" />
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
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
    <!-- Select2 -->
    <script src="assets/adminlte/plugins/select2/js/select2.full.min.js"></script>

    <!-- Page specific script -->
    <script>
        $(document).ready(function() {
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            var counter = 0;

            $("#addrow").on("click", function() {
                addRow();
            });

            $("table.order-list").on("click", ".ibtnDel", function(event) {
                $(this).closest("tr").remove();
                calculateGrandTotal();
            });

            function addRow() {

                // Make an AJAX request to fetch dropdown options
                $.ajax({
                    url: 'master_device.php?getDropdownOptions',
                    type: 'GET',
                    success: function(dropdownOptions) {

                        var newRow = $("<tr>");
                        var cols = "";

                        cols += '<td><select class="form-control select2 pilihNamaBahan" name="pilihNamaBahan[]" style="min-width:140px;">' + dropdownOptions + '</select></td>';
                        cols += '<td><input type="number" class="form-control quantity" name="quantity[]" min="0" value="" placeholder="0"/></td>';
                        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger"  value="Delete"></td>';

                        newRow.append(cols);
                        $("table.order-list").append(newRow);
                        counter++;

                        $('.select2').select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            containerCssClass: 'height-40px',
                        });
                    },
                    error: function(error) {
                        console.log("Error fetching dropdown options: " + error);
                    }
                });
            }

        });


        function calculateRow(row) {
            var price = +row.find('input[name^="price"]').val();
        }

        function calculateGrandTotal() {
            var grandTotal = 0;
            $("table.order-list").find('input[name^="price"]').each(function() {
                grandTotal += +$(this).val();
            });
            $("#grandtotal").text(grandTotal.toFixed(2));
        }

        function validateForm() {
            var selectedItem = document.getElementById("namaDevice").value;

            // Use classes for dynamic elements
            var namaElements = document.querySelectorAll(".pilihNamaBahan");
            var quantityElements = document.querySelectorAll(".quantity");

            // Check each row
            for (var i = 0; i < namaElements.length; i++) {
                var nama = namaElements[i].value;
                var quantity = quantityElements[i].value;

                if (selectedItem === "" || nama === "" || quantity === "" || quantity <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Harap lengkapi semua formulir!',
                    });
                    return false;
                }
            }

            return true;
        }


        function validateSuccess() {
            var formData = new FormData(document.getElementById("masterDeviceForm"));

            $.ajax({
                type: "POST",
                url: "master_device.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Device berhasil didaftarkan!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(error) {
                    // ... existing code ...
                }
            });
        }


        function resetForm() {
            document.getElementById("masterDeviceForm").reset();
            resetDropdown();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihNamaBahan");
            dropdown.selectedIndex = 0; // reset ke pilihan pertama

            // jika multiple selection
            dropdown.querySelectorAll("option:checked").forEach(option => {
                option.selected = false;
            });

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
        }
    </script>
</body>

</html>