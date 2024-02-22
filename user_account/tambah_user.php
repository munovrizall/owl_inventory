<?php

include "../connection.php";
include "../admin_privilege.php";

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which fields are provided
    $namaLengkap = $_POST["namaLengkap"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];
    $tandaTangan = isset($_POST["tandaTangan"]) ? $_POST["tandaTangan"] : null;
    
    $checkUsername = "SELECT COUNT(*) FROM user_account WHERE username = ?";
    $stmtUsername = $conn->prepare($checkUsername);
    $stmtUsername->bind_param("s", $username);
    $stmtUsername->execute();
    $stmtUsername->bind_result($countUsername);
    $stmtUsername->fetch();
    $stmtUsername->close();

    if ($countUsername > 0){
        $response['status'] = 'error';
        $response['message'] = "'$username' sudah ada di database!";
    } else {
        $insertQuery = "INSERT INTO user_account (nama_lengkap, username, password, role, tanda_tangan) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sssss", $namaLengkap, $username, $password, $role, $tandaTangan);
        $insertStmt->execute();
        $insertStmt->close();

        $response['status'] = 'success';
        $response['message'] = 'Akun ' . $role . ' berhasil dibuat!';
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
    <title>Tambah Akun</title>

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
                            <h1>Tambah Akun</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">User Account</li>
                                <li class="breadcrumb-item active">Tambah Akun</li>
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
                            <h3 class="card-title">Menambah Akun User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="userForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="namaLengkap">Nama Lengkap <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="namaLengkap" name="namaLengkap" placeholder="Masukkan nama lengkap user">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tandaTangan">Gambar TTD <span class="gray-italic-text"> (opsional)</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button onclick="window.open('https://img.doerig.dev/', '_blank')" class="btn btn-primary">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                        <input type="text" class="form-control" id="tandaTangan" name="tandaTangan" placeholder="Copy dan paste disini link imgur yang telah dibuat">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="role">Role <span style="color: red;">*</span></label>
                                        <select class="form-control select2" id="role" name="role">
                                            <option value="">--- Pilih Role ---</option>
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="username">Username Login <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="username" name="username" placeholder="Masukkan username user">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="password">Password Login <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="password" name="password" placeholder="Masukkan password user">
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
                    title: 'Akun berhasil dibuat!',
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
            var namaLengkap = document.getElementById("namaLengkap").value;
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var role = document.getElementById("role").value;

            if (namaLengkap === "" || username === "" || password === "" || role === "") {
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
            var formData = $("#userForm").serialize();

            $.ajax({
                type: "POST",
                url: "tambah_user.php",
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
            document.getElementById("userForm").reset();
        }

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