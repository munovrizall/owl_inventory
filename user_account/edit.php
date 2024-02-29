<?php

include "../connection.php";
include "../admin_privilege.php";

if (isset($_GET['id'])) {
    $account_id = $_GET['id'];

    // Selecting data from client table based on account_id
    $query = "SELECT * FROM user_account WHERE account_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $account_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
} else {
    echo "ID not provided.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $account_id = isset($_POST["account_id"]) ? $_POST["account_id"] : null;
    $namaLengkap = $_POST["namaLengkap"];
    $tandaTangan = isset($_POST["tandaTangan"]) ? $_POST["tandaTangan"] : null;
    $role = $_POST["role"];
    $username = $_POST["username"];

    $currentPassword = isset($_POST["currentPassword"]) ? $_POST["currentPassword"] : null;
    $newPassword = isset($_POST["newPassword"]) ? $_POST["newPassword"] : null;
    $confirmNewPassword = isset($_POST["confirmNewPassword"]) ? $_POST["confirmNewPassword"] : null;

    $preAppend = "p4l1s4d3";
    $append = "h0nd4nsx90";

    if ($currentPassword !== null) {
        //Cek currentPassword benar atau tidak
        $saltedCurrentPassword = $preAppend . $currentPassword . $append;
        $hashedPassword = hash('sha256', $saltedCurrentPassword);

        if ($hashedPassword == $row['password']){
            if ($newPassword == $confirmNewPassword) {
                //Salt dan hash password baru
                $saltedNewPassword = $preAppend . $newPassword . $append;
                $hashedNewPassword = hash('sha256', $saltedNewPassword);

                $updatePassword = "UPDATE user_account SET password = ? WHERE account_id = ?";
                $updateStmt = $conn->prepare($updatePassword);
                $updateStmt->bind_param("si", $hashedNewPassword, $account_id);
                $updateStmt->execute();
                $updateStmt->close();
                exit();
            } else {
                echo json_encode("Error: Konfirmasi Password Baru Salah");
                exit();
            }
        } else {
            echo json_encode("Error: Password salah");
            exit();
        }

    } else {
        $updateQuery = "UPDATE user_account SET nama_lengkap = ?, tanda_tangan = ?, username = ?, role = ? WHERE account_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssi", $namaLengkap, $tandaTangan, $username, $role, $account_id);
        $updateStmt->execute();
        $updateStmt->close();
    
        header("Location: list.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>

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
                            <h1>Edit User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="list.php">List</a></li>
                                <li class="breadcrumb-item active">Edit</li>
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
                            <h3 class="card-title">Edit User </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="userForm" method="post"> <!-- Added method="post" -->
                            <input type="hidden" name="account_id" value="<?php echo $account_id; ?>"> <!-- Hidden input to pass account_id -->
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="namaLengkap">Nama Lengkap <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="namaLengkap" name="namaLengkap" placeholder="Masukkan nama lengkap user" value="<?php echo $row['nama_lengkap']; ?>">
                                    </div>
                                </div>
                                <label for="user">Role User <span style="color: red;">*</span></label>
                                <div class="form-group">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary <?php echo ($row['role'] == 'user') ? 'active' : ''; ?>">
                                            <input type="radio" name="role" id="user" value="user" <?php echo ($row['role'] == 'user') ? 'checked' : ''; ?>> User
                                        </label>
                                        <label class="btn btn-outline-primary <?php echo ($row['role'] == 'admin') ? 'active' : ''; ?>">
                                            <input type="radio" name="role" id="admin" value="admin" <?php echo ($row['role'] == 'admin') ? 'checked' : ''; ?>> Admin
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="username">Username Login <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="username" name="username" placeholder="Masukkan username user" value="<?php echo $row['username']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="password">Password</label>
                                    </div>
                                    <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#modalUbahPassword" style="margin-top: 10px; max-width: 140px;"><i style="margin-right: 8px;"></i>Ubah Password</button>
                                </div>
                                <div class="form-group">
                                    <label for="tandaTangan">Gambar TTD <span class="gray-italic-text"> (opsional)</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button type="button" onclick="openUploadPage()" class="btn btn-primary">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                        <input type="text" class="form-control" id="tandaTangan" name="tandaTangan" placeholder="Copy dan paste disini link imgur yang telah dibuat">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" id="submitButton" class="btn btn-primary" onclick="submitForm()">Submit</button>
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
        <form id="ubahPasswordForm">
            <div class="modal fade" id="modalUbahPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Ubah Password</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="currentPassword">Password Saat Ini <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="currentPassword" name="currentPassword" placeholder="Masukkan password saat ini">
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="newPassword">Password Baru <span style="color: red;">*</span></label>
                                <input type="password" class="form-control form-control-border border-width-2" id="newPassword" name="newPassword" placeholder="Masukkan password baru">
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="confirmNewPassword">Konfirmasi Password Baru <span style="color: red;">*</span></label>
                                <input type="password" class="form-control form-control-border border-width-2" id="confirmNewPassword" name="confirmNewPassword" placeholder="Masukkan password baru">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="if(validateFormNP()) { validateSuccessNP(); resetForm(); }">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
            event.preventDefault();
            if (validateForm()) {
                validateSuccess();
                Swal.fire({
                    icon: 'success',
                    title: 'Data akun berhasil diupdate!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "../list_perusahaan.php";
                    }
                });
            }
        }

        function validateForm() {
            var namaLengkap = document.getElementById("namaLengkap").value;
            var role = document.querySelector('input[name="role"]:checked').value;
            var username = document.getElementById("username").value;

            if (namaLengkap === "" || role === "" || username === "") {
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
                url: "edit.php",
                data: formData,
                success: function(response) {
                    // Handle success response
                    Swal.fire({
                        icon: 'success',
                        title: 'Data akun berhasil diupdate!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "list.php";
                        }
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akun telah terdaftar!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function validateFormNP() {
            var currentPassword = document.getElementById("currentPassword").value;
            var newPassword = document.getElementById("newPassword").value;
            var CNPassword = document.getElementById("confirmNewPassword").value;

            if (currentPassword === "" || newPassword === "" || CNPassword === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                });
                return false;
            } else if (newPassword !== CNPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Password baru berbeda dengan konfirmasi!',
                });
                return false;
            }

            return true;
        }
        
        function validateSuccessNP() {
            // Get the form data
            var formDataKB = $("#ubahPasswordForm").serialize();

            $.ajax({
                type: "POST",
                url: "edit.php",
                data: formDataKB,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password berhasil diubah!',
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
                    alert("Error mengubah password.");
                }
            });
        }

        function resetForm() {
            document.getElementById("userForm").reset();
            document.getElementById("ubahPasswordForm").reset();
        }

        // var password = document.getElementById('password');
        // password.addEventListener('keydown', function(event) {
        //     if (event.keyCode === 13) {
        //         event.preventDefault();
        //         submitForm();
        //     }
        // });

        function openUploadPage() {
            var newTab = window.open('https://img.doerig.dev/', '_blank');
            newTab.blur();
            window.focus();
        }
    </script>
</body>

</html>