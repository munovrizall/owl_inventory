<?php

include "../connection.php";

$queryClient = "SELECT * FROM client ORDER BY nama_client";
$resultClient = $conn->query($queryClient);

if (!$resultClient) {
    die("Error fetching kelompok data: " . $conn->error);
}
$queryProduk = "SELECT nama_produk FROM produk ORDER BY nama_produk";
$resultProduk = $conn->query($queryProduk);

if (isset($_GET["getDropdownOptions"])) {

    $queryProduk = "SELECT produk, no_sn, nama_client FROM inventaris_produk ORDER BY no_sn DESC";

    $resultProduk = $conn->query($queryProduk);

    $options = '<option value="" selected disabled>Pilih Produk</option>';

    if ($resultProduk && $resultProduk->num_rows > 0) {
        while ($row = $resultProduk->fetch_assoc()) {
            $options .= '<option value="' . $row['produk'] . '">'  . $row['no_sn'] . ' - ' .  $row['produk'] . '</option>';
        }
    }
    echo $options;
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penggantian Device</title>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .lebar-kolom1 {
            width: 45%;
        }

        .lebar-kolom2 {
            width: 45%;
        }

        .lebar-kolom3 {
            width: 10%;
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
                            <h1>Penggantian Device</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Pengelolaan Device</li>
                                <li class="breadcrumb-item active">Penggantian</li>
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
                            <h3 class="card-title">Menukar Device dari OWL ke Client</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="PenggantianForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pilihClient">Pilih PT <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihClient" name="client">
                                        <option value="">--- Pilih PT ---</option>
                                        <?php
                                        while ($row = $resultClient->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_client'] . '">' . $row['nama_client'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style="margin-bottom: 16px;">
                                    <button type="button" class="btn btn-outline-info btn-block" id="cekButton" name="cekButton" style="margin-top: 10px; max-width: 180px;"><i class="fas fa-sync-alt" style="margin-right: 8px;" onclick="cekProduksi()"></i>Cek</button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="myTable" class=" table order-list table-striped">
                                            <thead>
                                                <tr>
                                                    <td class="text-center lebar-kolom1"><b>Produk Client <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom2"><b>Produk OWL <span style="color: red;">*</span></b></td>
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
                                                        <input type="button" class="btn btn-outline-info btn-block" id="addrow" value="+  Tambah Produk" />
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="deksripsi">Deskripsi<span class="gray-italic-text"> (opsional)</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan keterangan Penggantian produk ..."></textarea>
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

            $("#pilihClient").change(function() {
                var selectedPT = $("#pilihClient").val();
                console.log(selectedPT);
                // Extract the value you want from the selectedTransaksi
                var extractedValue = ""; // Update this based on your logic

                // Update the deskripsi field
                $("#deskripsi").val("Penggantian untuk " + selectedPT);
            });
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
                url: 'Penggantian.php?getDropdownOptions',
                type: 'GET',
                success: function(dropdownOptions) {

                    var newRow = $("<tr>");
                    var cols = "";

                    cols += '<td><select class="form-control select2 pilihNamaProduk" name="pilihNamaProduk[]" style="min-width:140px;">' + dropdownOptions + '</select></td>';
                    cols += '<td><select class="form-control select2 pilihNamaProduk" name="pilihNamaProduk[]" style="min-width:140px;">' + dropdownOptions + '</select></td>';
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

        function validateForm() {
            var selectedItem = document.getElementById("pilihProdukPenggantian").value;
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

        function resetForm() {
            document.getElementById("PenggantianForm").reset();
            resetDropdown();
            disableQuantityInput();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihProdukPenggantian");
            dropdown.selectedIndex = 0;
            // reset ke pilihan pertama
            dropdown.dispatchEvent(new Event('change'));
        }

        $("#pilihProdukPenggantian").change(function() {
            const quantityInput = document.getElementById("quantity");
            quantityInput.placeholder = "Masukkan jumlah stok produk yang ingin dikirim";
            quantityInput.disabled = false;
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