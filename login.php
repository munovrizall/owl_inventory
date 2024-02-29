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

    $buatUsername = isset($_POST['buatUsername']) ? $_POST['buatUsername'] : null;
    $buatPassword = isset($_POST['buatPassword']) ? $_POST['buatPassword'] : null;
    $confirmBuatPassword = isset($_POST['confirmBuatPassword']) ? $_POST['confirmBuatPassword'] : null;
    $buatNama = isset($_POST['buatNama']) ? $_POST['buatNama'] : null;

    $cekUsername = isset($_POST["cekUsername"]) ? $_POST["cekUsername"] : null;
    $newPassword = isset($_POST["newPassword"]) ? $_POST["newPassword"] : null;
    $confirmNewPassword = isset($_POST["confirmNewPassword"]) ? $_POST["confirmNewPassword"] : null;

    $preAppend = "p4l1s4d3";
    $append = "h0nd4nsx90";            
    
    if ($cekUsername !== null) {
        $checkCountUsername = "SELECT COUNT(*) FROM user_account WHERE username = ?";
        $checkStmtUsername = $conn->prepare($checkCountUsername);
        $checkStmtUsername->bind_param("s", $cekUsername);
        $checkStmtUsername->execute();
        $checkStmtUsername->bind_result($countUsername);
        $checkStmtUsername->fetch();
        $checkStmtUsername->close();

        if ($countUsername == 1) {
            if ($newPassword !== null && $confirmNewPassword !== null) {
                if ($newPassword == $confirmNewPassword) {

                    $saltedNewPassword = $preAppend . $newPassword . $append;
                    $hashedNewPassword = hash('sha256', $saltedNewPassword);

                    $updatePassword = "UPDATE user_account SET password = ? WHERE username = ?";
                    $updatePasswordStmt = $conn->prepare($updatePassword);
                    $updatePasswordStmt->bind_param("ss", $hashedNewPassword, $cekUsername);
                    $updatePasswordStmt->execute();
                    $updatePasswordStmt->close();
                }
                else {
                    echo "Error: New Password and New Password Confirmation is different";
                    exit();
                }
            } else{
                echo "Error: New Password is not filled";
                exit();
            }
        } else{
            echo "Error: Username does not exists.";
            exit; // Stop further execution
        }

    } else if ($buatUsername !== null){
        $checkCountUsername = "SELECT COUNT(*) FROM user_account WHERE username = ?";
        $checkStmtUsername = $conn->prepare($checkCountUsername);
        $checkStmtUsername->bind_param("s", $buatUsername);
        $checkStmtUsername->execute();
        $checkStmtUsername->bind_result($countUsername);
        $checkStmtUsername->fetch();
        $checkStmtUsername->close();

        if ($countUsername == 0) {

            $saltedBuatPassword = $preAppend . $buatPassword . $append;
            $hashedBuatPassword = hash('sha256', $saltedBuatPassword);

            $insertQuery = "INSERT INTO user_account (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'user')";
            $insertStmtAkun = $conn->prepare($insertQuery);
            $insertStmtAkun->bind_param("sss", $buatUsername, $hashedBuatPassword, $buatNama);
            $insertStmtAkun->execute();
            $insertStmtAkun->close();
        }

    } else {
        // Retrieve the user's salted and hashed password from the database
        $queryAccount = "SELECT * FROM user_account WHERE username = ?";
        $stmt = $conn->prepare($queryAccount);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultAccount = $stmt->get_result();
        $stmt->close();

        if ($resultAccount->num_rows > 0) {
            $accountData = $resultAccount->fetch_assoc();
            $storedHashedPassword = $accountData['password'];

            // Add salt to the provided password
            $pre_salt = "p4l1s4d3";
            $post_salt = "h0nd4nsx90";
            $saltedPassword = $pre_salt . $password . $post_salt;

            // Hash the salted password using SHA-256
            $hashedPassword = hash('sha256', $saltedPassword);

            // Compare the hashed passwords
            if ($hashedPassword === $storedHashedPassword) {
                // Password is correct
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $accountData['role'];
                if ($accountData['role'] == "admin") {
                    header("location: homepage.php");
                    exit();
                } elseif ($accountData['role'] == "user") {
                    header("location: user/homepage.php");
                    exit();
                }
            } else {
                // Password is incorrect
                echo "Password salah";
            }
        } else {
            // Username not found
            echo "Akun tidak terdaftar";
        }
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
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
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
          <div class="form-group">
              <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#modalLupaPassword" style="margin-top: 10px; max-width: 140px;"><i style="margin-right: 8px;"></i>Lupa Password</button>
              <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#modalBuatAkun" style="margin-top: 10px; max-width: 140px;"><i style="margin-right: 8px;"></i>Buat Akun</button>
          </div>
          <button type="button" id="buttonSubmit" class="btn btn-primary btn-block" onclick="performLogin()">Sign In</button>
        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <form id="lupaPasswordForm">
        <div class="modal fade" id="modalLupaPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cekUsername">Username Akun <span style="color: red;">*</span></label>
                            <input type="text" class="form-control form-control-border border-width-2" id="cekUsername" name="cekUsername" placeholder="Masukkan username yang telah terdaftar">
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
    <form id="buatAkunForm">
        <div class="modal fade" id="modalBuatAkun" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="buatUsername">Username<span style="color: red;">*</span></label>
                            <input type="text" class="form-control form-control-border border-width-2" id="buatUsername" name="buatUsername" placeholder="Masukkan username Anda">
                            <button type="button" class="btn btn-outline-info btn-block" id="cekButton" name="cekButton" style="margin-top: 10px; max-width: 180px;"><i class="fas fa-sync-alt" style="margin-right: 8px;" onclick="cekProduksi()"></i>Cek</button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="buatPassword">Password<span style="color: red;">*</span></label>
                            <input type="password" class="form-control form-control-border border-width-2" id="buatPassword" name="buatPassword" placeholder="Masukkan password Anda">
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="confirmBuatPassword">Konfirmasi Password<span style="color: red;">*</span></label>
                            <input type="password" class="form-control form-control-border border-width-2" id="confirmBuatPassword" name="confirmBuatPassword" placeholder="Masukkan password Anda">
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="buatNama">Nama Lengkap<span style="color: red;">*</span></label>
                            <input type="text" class="form-control form-control-border border-width-2" id="buatNama" name="buatNama" placeholder="Masukkan Nama Lengkap">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="if(validateFormBA()) { validateSuccessBA(); resetForm(); }">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
          if (inputUsername.trim() !== "") {
              var isUsernameValid = false;

              // Melakukan iterasi pada array accountData untuk mencocokkan username dan password
              for (var i = 0; i < accountData.length; i++) {
                  if (accountData[i].username === inputUsername) {
                      isUsernameValid = true;
                      break; 
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
                      localStorage.setItem("username", inputUsername);

                      // Perform AJAX request to send username to server
                      $.ajax({
                          type: "POST",
                          url: "process_username.php",
                          data: {
                              username: inputUsername
                          },
                          success: function (response) {
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
                }else {
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

      function validateFormNP() {
        var username = document.getElementById("cekUsername").value;
        var newPassword = document.getElementById("newPassword").value;
        var CNPassword = document.getElementById("confirmNewPassword").value;

        if (username === "" || newPassword === "" || CNPassword === "") {
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
        var formDataKB = $("#lupaPasswordForm").serialize();

        $.ajax({
            type: "POST",
            url: "login.php",
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

    function validateFormBA() {
        var username = document.getElementById("buatUsername").value;
        var password = document.getElementById("buatPassword").value;
        var confirmPassword = document.getElementById("confirmBuatPassword").value;
        var namaLengkap = document.getElementById("buatNama").value;

        if (username === "" || password === "" || confirmPassword === "" || namaLengkap === "") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harap lengkapi semua formulir!',
            });
            return false;
        } else if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password baru berbeda dengan konfirmasi!',
            });
            return false;
        }

        return true;
    }
    
    function validateSuccessBA() {
        // Get the form data
        var formDataKB = $("#buatAkunForm").serialize();

        $.ajax({
            type: "POST",
            url: "login.php",
            data: formDataKB,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Akun berhasil dibuat!',
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
                alert("Error membuat akun.");
            }
        });
    }

    function resetForm() {
        document.getElementById("lupaPasswordForm").reset();
        document.getElementById("buatAkunForm").reset();
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