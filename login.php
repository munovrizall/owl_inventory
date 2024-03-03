<?php
session_start();

$serverName = "localhost";
$userNameDb = "root";
$password = "";
$dbName = "databaseinventory";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($serverName, $userNameDb, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$queryAccount = "SELECT * FROM user_account";
$result = $conn->query($queryAccount);

// Cek apakah hasil query kosong
if (empty($result)) {
    $result = "Tidak ada data";
}

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array(
        'namaLengkap' => $row['nama_lengkap'],
        'username' => $row['username'],
        'password' => $row['password'],
        'role' => $row['role'],
    );
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $queryAccount = "SELECT * FROM user_account WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($queryAccount);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $resultAccount = $stmt->get_result();
    $stmt->close();

    if ($resultAccount->num_rows > 0) {
        $accountData = $resultAccount->fetch_assoc();

        // Verify the hashed password using password_verify
        if ($password === $accountData['password'] && $username === $accountData['username']) {
            if ($accountData['role'] == "admin") {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = "admin";
                header("location: homepage.php");
                exit();
            } elseif ($accountData['role'] == "user") {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = "user";
                header("location: stok_bahan.php");
                exit();
            } else {
                echo "Akun tidak terdaftar";
            }
        } else {
            echo "Password salah";
        }
    } else {
        echo "Akun tidak terdaftar";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="login.php" class="h1">
                    <img src="assets/adminlte/dist/img/OWLlogo.png" alt="OWL Inventory" class="brand-image img-circle elevation-3" style="background-color: black; padding: 8px; opacity: .8; height: 50px; width: 50px;">
                    <b>OWL</b> RnD
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Silahkan login terlebih dahulu</p>

                <form id="loginForm">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" id="usernameInput" name="username" onkeypress="checkEnter(event)">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" id="passwordInput" name="password" onkeypress="checkEnter(event)">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span id="togglePassword" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="buttonSubmit" class="btn btn-primary btn-block" onclick="performLogin()">Sign In</button>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/adminlte/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 Toast -->
    <script src="assets/adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script>
        var accountData = <?php echo json_encode($data); ?>;

        function checkEnter(event) {
            if (event.key === "Enter") {
                performLogin();
            }
        }

        function performLogin() {
            var inputUsername = document.getElementById('usernameInput').value;
            var inputPassword = document.getElementById('passwordInput').value;

            if (inputUsername.trim() !== "") {
                var isUsernameValid = false;
                var isPasswordValid = false;

                // Melakukan iterasi pada array accountData untuk mencocokkan username dan password
                for (var i = 0; i < accountData.length; i++) {
                    if (accountData[i].username === inputUsername) {
                        isUsernameValid = true;

                        // Jika username valid, lanjutkan untuk memeriksa password
                        if (accountData[i].password === inputPassword) {
                            isPasswordValid = true;
                            break; // Jika password valid, keluar dari loop
                        }
                    }
                }

                if (!isUsernameValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Username tidak ditemukan',
                        text: 'Masukkan username yang benar!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK (enter)'
                    });
                } else {
                    if (!isPasswordValid) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Salah',
                            text: 'Masukkan password yang benar!',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK (enter)'
                        });
                    } else {
                        localStorage.setItem("username", inputUsername);

                        // Perform AJAX request to send username to server
                        $.ajax({
                            type: "POST",
                            url: "process_username.php",
                            data: {
                                username: inputUsername
                            },
                            success: function(response) {
                                // Parse the response as JSON
                                var responseData = JSON.parse(response);

                                // Check the role and redirect accordingly
                                if (responseData.role === "admin") {
                                    window.location.href = "homepage.php";
                                } else {
                                    window.location.href = "user/homepage.php";
                                }
                            },
                            error: function(error) {
                                console.error("Error sending username to server: " + error);
                            }
                        });
                    }
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Username Kosong',
                    text: 'Mohon lengkapi form login!',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK (enter)'
                })
            }
        }


        // When user press enter on keyboard
        var passwordInput = document.getElementById('passwordInput');
        passwordInput.addEventListener('keyup', function(event) {
            if (event.keyCode === 13) {
                submitForm();
            }
        });

        function submitForm() {
            document.getElementById('submitButton').click();
        }

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('passwordInput');
            var togglePassword = document.getElementById('togglePassword');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }
    </script>
</body>

</html>