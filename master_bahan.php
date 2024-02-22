<?php

include "connection.php";
include "admin_privilege.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which fields are provided
    $kelompok = isset($_POST["kelompok"]) ? $_POST["kelompok"] : null;
    $nama = isset($_POST["namaBahan"]) ? $_POST["namaBahan"] : null;
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : null;
    $hargaBahan = isset($_POST["price"]) ? $_POST["price"] : null;
    $lokasiPenyimpanan = isset($_POST["lokasiPenyimpanan"]) ? $_POST["lokasiPenyimpanan"] : null;
    $deskripsi = isset($_POST["deskripsi"]) ? $_POST["deskripsi"] : null;

    $namaKelompokBaru = isset($_POST["namaKelompokBaru"]) ? $_POST["namaKelompokBaru"] : null;

    // Check if the material name already exists
    if ($nama !== null) {
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
            exit; // Stop further execution
        }
    }

    // Check if the group name already exists
    if ($namaKelompokBaru !== null) {
        $checkQueryKB = "SELECT COUNT(*) FROM masterkelompok WHERE nama_kelompok = ?";
        $checkStmtKB = $conn->prepare($checkQueryKB);
        $checkStmtKB->bind_param("s", $namaKelompokBaru);
        $checkStmtKB->execute();
        $checkStmtKB->bind_result($countKB);
        $checkStmtKB->fetch();
        $checkStmtKB->close();

        // If the group name already exists, display an error
        if ($countKB > 0) {
            echo "Error: Group name '$namaKelompokBaru' already exists in the database.";
            exit; // Stop further execution
        }
        // Insert the new record for the group
        $insertQueryKB = "INSERT INTO masterkelompok (nama_kelompok) VALUES (?)";
        $insertStmtKB = $conn->prepare($insertQueryKB);
        $insertStmtKB->bind_param("s", $namaKelompokBaru);

        if ($insertStmtKB->execute()) {
            echo "Data berhasil ditambahkan ke tabel masterkelompok.";
        } else {
            echo "Error: " . $insertStmtKB->error;
        }

        $insertStmtKB->close();
    } elseif ($kelompok !== null) {
        // Insert the new record for material
        $insertQuery = "INSERT INTO masterbahan (kelompok, nama, quantity, harga_bahan, lokasi_penyimpanan, deskripsi) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssiiss", $kelompok, $nama, $quantity, $hargaBahan, $lokasiPenyimpanan, $deskripsi);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                                    <select class="form-control select2" id="pilihNamaKelompok" name="kelompok">
                                        <option value="">--- Pilih Kelompok ---</option>
                                        <?php
                                        while ($row = $resultKelompok->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_kelompok'] . '">' . $row['nama_kelompok'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#modalBuatKelompok" style="margin-top: 10px; max-width: 180px;"><i class="fas fa-plus" style="margin-right: 8px;"></i>Kelompok Baru</button>
                                </div>
                                <div class="form-group">
                                    <label for="namaBahan">Nama Bahan <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control form-control-border border-width-2" id="namaBahan" name="namaBahan" placeholder="Contoh : R060310k (jangan memasukkan tanda '' di nama bahan untuk menghindari bug)">
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Kuantitas <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="" placeholder="Masukkan jumlah stok bahan">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="price">Harga Bahan <span class="gray-italic-text"> (opsional)</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="price" name="price" min="0" value="" placeholder="Masukkan harga bahan">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="lokasiPenyimpanan">Lokasi Penyimpanan <span class="gray-italic-text"> (opsional)</span></label>
                                    <input type="text" class="form-control form-control-border border-width-2" id="lokasiPenyimpanan" name="lokasiPenyimpanan" placeholder="Masukkan lokasi penyimpanan">
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi <span class="gray-italic-text"> (opsional)</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan keterangan bahan ..."></textarea>
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

        <!-- Modal menambahkan kelompok baru -->
        <form id="tambahKelompokForm">
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
                                <label for="namaKelompokBaru">Nama Kelompok <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="namaKelompokBaru" name="namaKelompokBaru" placeholder="Masukkan nama kelompok yang ingin dibuat">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="if(validateFormKB()) { validateSuccessKB(); resetForm(); }">Submit</button>
        </form>
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

        function validateForm() {
            var selectedItem = document.getElementById("pilihNamaKelompok").value;
            var nama = document.getElementById("namaBahan").value;
            var quantity = document.getElementById("quantity").value;

            if (selectedItem === "" || nama === "" || quantity === "" || quantity <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                })
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
                    alert("Error mendaftarkan barang.");
                }
            });
        }

        function validateFormKB() {
            var namaKB = document.getElementById("namaKelompokBaru").value;

            if (namaKB === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                });
                return false;
            }

            return true;
        }

        function validateSuccessKB() {
            // Get the form data
            var formDataKB = $("#tambahKelompokForm").serialize();

            $.ajax({
                type: "POST",
                url: "master_bahan.php",
                data: formDataKB,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kelompok berhasil didaftarkan!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });


                },
                error: function(error) {
                    alert("Error mendaftarkan kelompok.");
                }
            });
        }

        function resetForm() {
            document.getElementById("masterBahanForm").reset();
            document.getElementById("tambahKelompokForm").reset();
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

        var kelompokBaruInput = document.getElementById('namaKelompokBaru');
        kelompokBaruInput.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                if (validateFormKB()) {
                    validateSuccessKB();
                    resetForm();
                }
            }
        });

        function submitForm() {
            document.getElementById('submitButton').click();
        }
    </script>
</body>

</html>