<?php

include "../connection.php";

if (isset($_GET['id'])) {
    $client_id = $_GET['id'];

    // Selecting data from client table based on client_id
    $query = "SELECT * FROM client WHERE client_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();

    $result = $stmt->get_result();
} else {
    echo "ID not provided.";
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client_id = isset($_POST["client_id"]) ? $_POST["client_id"] : null;
    $namaClient = isset($_POST["namaPerusahaan"]) ? $_POST["namaPerusahaan"] : null;
    $namaKorespondensi = isset($_POST["namaKorespondensi"]) ? $_POST["namaKorespondensi"] : null;
    $alamatPerusahaan = isset($_POST["alamatPerusahaan"]) ? $_POST["alamatPerusahaan"] : null;
    $username = isset($_POST["username"]) ? $_POST["username"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;

    $updateQuery = "UPDATE client SET nama_client = ?, nama_korespondensi = ?, alamat_perusahaan = ?, username = ?, password = ? WHERE client_id=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssi", $namaClient, $namaKorespondensi, $alamatPerusahaan, $username, $password, $client_id);
    $updateStmt->execute();
    $updateStmt->close();

    header("Location: list.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Perusahaan</title>

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
                            <h1>Edit Perusahaan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="list.php">Perusahaan</a></li>
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
                            <h3 class="card-title">Edit Perusahaan </h3>
                        </div>
                        <!-- /.card-header -->
                        <?php
                        $row = mysqli_fetch_assoc($result);
                        ?>
                        <!-- form start -->
                        <form id="perusahaanForm" method="post"> <!-- Added method="post" -->
                            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>"> <!-- Hidden input to pass client_id -->
                            <div class="card-body">
                                <div class="form-group">
                                    <div>
                                        <label for="namaPerusahaan">Nama Perusahaan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="namaPerusahaan" name="namaPerusahaan" placeholder="Masukkan nama perusahaan" value="<?php echo $row['nama_client']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="namaKorespondensi">Nama Korespondensi <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="namaKorespondensi" name="namaKorespondensi" placeholder="Masukkan nama korespondensi perusahaan" value="<?php echo $row['nama_korespondensi']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="alamatPerusahaan">Alamat Perusahaan <span style="color: red;">*</span></label>
                                    <textarea class="form-control" id="alamatPerusahaan" name="alamatPerusahaan" rows="3"><?php echo $row['alamat_perusahaan']; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="username">Username Login</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="username" name="username" placeholder="Masukkan username untuk perusahaan tersebut" value="<?php echo $row['username']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="password">Password Login</label>
                                        <input type="text" class="form-control form-control-border border-width-2" id="password" name="password" placeholder="Masukkan password untuk perusahaan tersebut" value="<?php echo $row['password']; ?>">
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
                    title: 'Keterangan perusahaan berhasil diupdate!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "list.php";
                    }
                });
            }
        }

        function validateForm() {
            var namaPerusahaan = document.getElementById("namaPerusahaan").value;
            var namaKorespondensi = document.getElementById("namaKorespondensi").value;
            var alamatPerusahaan = document.getElementById("alamatPerusahaan").value;
            var password = document.getElementById("password").value;

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
                url: "edit.php",
                data: formData,
                success: function(response) {
                    // Handle success response
                    Swal.fire({
                        icon: 'success',
                        title: 'Keterangan perusahaan berhasil diupdate!',
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
                        title: 'Error di suatu tempat!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
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

        var password = document.getElementById('password');
        password.addEventListener('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                submitForm();
            }
        });
    </script>
</body>

</html>