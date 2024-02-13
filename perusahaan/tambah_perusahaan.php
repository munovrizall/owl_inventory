<?php

include "../connection.php";

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which fields are provided
    $namaPerusahaan = isset($_POST["namaPerusahaan"]) ? $_POST["namaPerusahaan"] : null;
    $namaKorespondensi = isset($_POST["namaKorespondensi"]) ? $_POST["namaKorespondensi"] : null;
    $alamatPerusahaan = isset($_POST["alamatPerusahaan"]) ? $_POST["alamatPerusahaan"] : null;
    $username = isset($_POST["username"]) ? $_POST["username"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;

    if ($namaPerusahaan !== null) {
        $checkQuery = "SELECT COUNT(*) FROM client WHERE nama_client = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $namaPerusahaan);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        // If the material name already exists, display an error
        if ($count > 0) {
            $response['status'] = 'error';
            $response['message'] = "'$namaPerusahaan' sudah ada di database!";
        } else {
            $insertQuery = "INSERT INTO client (nama_client, nama_korespondensi, alamat_perusahaan, username, password) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("sssss", $namaPerusahaan, $namaKorespondensi, $alamatPerusahaan, $username, $password);
            $insertStmt->execute();
            $insertStmt->close();

            $response['status'] = 'success';
            $response['message'] = 'Perusahaan berhasil ditambahkan!';
        }
    } 

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Perusahaan</title>

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
        <?php include "../sidebar.php"; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Tambah Perusahaan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Perusahaan</li>
                                <li class="breadcrumb-item active">Tambah Perusahaan</li>
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
                            <h3 class="card-title">Menambah Data Perusahaan yang Bekerjasama dengan OWL</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="perusahaanForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="namaPerusahaan">Nama Perusahaan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="namaPerusahaan" name="namaPerusahaan" placeholder="Masukkan nama perusahaan">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="namaKorespondensi">Nama Korespondensi <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="namaKorespondensi" name="namaKorespondensi" placeholder="Masukkan nama korespondensi perusahaan">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="alamatPerusahaan">Alamat Perusahaan <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="alamatPerusahaan" name="alamatPerusahaan" rows="3" placeholder="Masukkan alamat perusahaan ..."></textarea>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="username">Username Login</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="username" name="username" placeholder="Masukkan username untuk perusahaan tersebut">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="password">Password Login</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="password" name="password" placeholder="Masukkan password untuk perusahaan tersebut">
                                    </div>
                                </div>
                            </div>

                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" id="submitButton" class="btn btn-primary" onclick="submitForm()">Submit</button>
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
    <!-- Page specific script -->
    <script>
        function submitForm() {
            if (validateForm()) {
                validateSuccess();
                Swal.fire({
                    icon: 'success',
                    title: 'Perusahaan Berhasil Ditambahkan!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetForm();
                    }
                });
            }
        }

        function validateForm() {
            var namaPerusahaan = document.getElementById("namaPerusahaan").value;
            var namaKorespondensi = document.getElementById("namaKorespondensi").value;
            var alamatPerusahaan = document.getElementById("alamatPerusahaan").value;

            if (namaPerusahaan === "" || namaKorespondensi === "" || alamatPerusahaan === "") {
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
            var formData = $("#perusahaanForm").serialize();

            $.ajax({
                type: "POST",
                url: "tambah_perusahaan.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        // Handle success response
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                resetForm();
                            }
                        });
                    } else if (response.status === 'error') {
                        // Handle error response
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                },
            });
        }

        function resetForm() {
            document.getElementById("perusahaanForm").reset();
        }

        var alamatInput = document.getElementById('alamatPerusahaan');
        alamatInput.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                submitForm();
            }
        });

        var passwordInput = document.getElementById('password');
        passwordInput.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                submitForm();
            }
        });
    </script>
</body>

</html>