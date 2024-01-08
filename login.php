<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];

  // Check if the username is not empty
  if (!empty($username)) {
    // Save the username in a session variable
    $_SESSION['username'] = $username;


    exit();
  } else {
    echo "Username cannot be empty";
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
            <select class="form-control" placeholder="Username" id="usernameInput">
              <option value="Riki">Riki</option>
              <option value="Ahmad">Ahmad</option>
              <option value="Tatang">Tatang</option>
            </select>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" id="passwordInput">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Ingat Saya
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="button" class="btn btn-primary btn-block" onclick="saveUsername()">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
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
    function saveUsername() {
      var username = document.getElementById("usernameInput").value;
      if (username.trim() !== "") {
        // Save the username to local storage
        localStorage.setItem("username", username);

        // Perform AJAX request to send username to server
        $.ajax({
          type: "POST",
          url: "process_username.php",
          data: {
            username: username
          },
          success: function(response) {
            console.log(response); // Handle the server response if needed
        window.location.href = "homepage.php";
          },
          error: function(error) {
            console.error("Error sending username to server: " + error);
          }
        });
      } else {
        alert("Username cannot be empty");
      }
    }
  </script>
</body>

</html>