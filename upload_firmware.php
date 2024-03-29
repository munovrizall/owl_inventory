<?php

include "connection.php";
include "admin_privilege.php";

// Fetch data from produksi table
$queryProduk = "SELECT nama_produk FROM produk";
$resultProduk = $conn->query($queryProduk);

if (isset($_FILES['fileFirmware'])) {
    $produk = $_POST['selectedItem'];
    $firmware = $_POST['firmware'];
    $hardware = $_POST['hardware'];

    // Proses upload file firmware
    $uploadPath = "/var/www/html/bin/$produk/$hardware/$firmware/"; // Sesuaikan dengan direktori upload Anda

    $fileFirmware = $_FILES['fileFirmware'];
    $fileName = "firmware.bin";
    $uploadFile = $uploadPath . basename($fileName);

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    if (move_uploaded_file($fileFirmware['tmp_name'], $uploadFile)) {
        $queryFlag = "UPDATE firmware_setup SET flag_active = 0 WHERE produk = ?";
        $stmt = $conn->prepare($queryFlag);
        $stmt->bind_param("s", $produk);
        $stmt->execute();
        // Simpan data ke dalam database
        $sql = "INSERT INTO firmware_setup (produk, firmware, hardware, path, flag_active) VALUES ('$produk', '$firmware', '$hardware', '$uploadFile', 1)";

        if ($conn->query($sql) === TRUE) {
            echo json_encode("Data berhasil disimpan!");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo json_encode("Gagal mengupload file firmware.");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Firmware</title>

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
                            <h1>Upload Firmware</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Upload Firmware</li>
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
                            <h3 class="card-title">Upload Firmware Device</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="firmwareForm" enctype="multipart/form-data" action="upload_firmware.php" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pilihProduk">Pilih Produk <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihProduk" name="selectedItem">
                                        <option value="">--- Pilih Produk ---</option>
                                        <?php
                                        while ($row = $resultProduk->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_produk'] . '">' . $row['nama_produk'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="hardware">Versi Hardware <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control form-control-border border-width-2" id="hardware" name="hardware" placeholder="Masukkan versi hardware yang diupload">
                                </div>
                                <div class="form-group">
                                    <label for="firmware">Versi Firmware <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control form-control-border border-width-2" id="firmware" name="firmware" placeholder="Masukkan versi firmware yang diupload">
                                </div>
                                <div class="form-group">
                                    <label for="fileFirmware">Upload File Firmware <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="fileFirmware" name="fileFirmware">
                                            <label class="custom-file-label" for="fileFirmware">Pilih File</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer d-flex justify-content-end">
                                <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
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

            $('#fileFirmware').on('change', handleFileChange);

            $("#firmwareForm").submit(function(e) {
                event.preventDefault();
                if (validateForm()) {
                    e.preventDefault(); 
                    var formData = new FormData(this);

                    $.ajax({
                        url: "upload_firmware.php", 
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Firmware berhasil diupload!',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK (enter)'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);


                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Harap lengkapi semua formulir!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK (enter)'
                    });
                }
            });
        });

        function handleFileChange() {
            // Menampilkan nama file yang dipilih di label custom-file-label
            var fileName = $('#fileFirmware')[0].files[0].name;
            $('.custom-file-label').html(fileName);
        }

        function validateForm() {
            var selectedItem = document.getElementById("pilihProduk").value;
            var firmware = document.getElementById("firmware").value;
            var hardware = document.getElementById("hardware").value;
            var fileFirmware = document.getElementById("fileFirmware").value;

            if (selectedItem === "" || firmware === "" || hardware === "" || fileFirmware === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                });
                return false;
            }
            return true;
        }

        function validateSuccess() {
            console.log('success');
            // Get the form data
            var formData = $("#firmwareForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock quantity
            $.ajax({
                type: "POST",
                url: "upload_firmware.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Firmware berhasil diupload!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK (enter)'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });

                    resetForm();

                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>
</body>

</html>