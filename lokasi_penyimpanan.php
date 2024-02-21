<?php

include "connection.php";
include "admin_privilege.php";

$queryBahan = "SELECT * FROM masterbahan ORDER BY nama";
$resultBahan = $conn->query($queryBahan);

$currentLokasiPenyimpanan = "";
$newLokasiPenyimpanan = "";

if (isset($_POST['selectedItem'])) {
    $selectedItem = $_POST['selectedItem'];
    $newLokasiPenyimpanan = $_POST['lokasiPenyimpanan'];

    // Fetch the username from the POST data
    $pengguna = isset($_SESSION['username']) ? $_SESSION['username'] : '';


    // Fetch the stock lokasi_penyimpanan from the database based on the selected item
    $query = "SELECT lokasi_penyimpanan FROM masterbahan WHERE nama = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedItem);
    $stmt->execute();
    $stmt->bind_result($currentLokasiPenyimpanan);
    $stmt->fetch();
    $stmt->close();

    if ($newLokasiPenyimpanan == "") {
        echo json_encode(array('currentLokasiPenyimpanan' => $currentLokasiPenyimpanan, 'newLokasiPenyimpanan' => $newLokasiPenyimpanan));
        exit();
    }

    // Update the database with the new stock lokasiPenyimpanan

    $updateQueryHarga = "UPDATE masterbahan SET lokasi_penyimpanan = ? WHERE nama = ?";
    $updateStmtHarga = $conn->prepare($updateQueryHarga);
    $updateStmtHarga->bind_param("ss", $newLokasiPenyimpanan, $selectedItem);
    $updateStmtHarga->execute();
    $updateStmtHarga->close();

    // Return the updated stock lokasiPenyimpanan
    echo json_encode(array('currentLokasiPenyimpanan' => $currentLokasiPenyimpanan, 'newLokasiPenyimpanan' => $newLokasiPenyimpanan));
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lokasi Penyimpanan</title>

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
                            <h1>Lokasi Penyimpanan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Lokasi Penyimpanan</li>
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
                            <h3 class="card-title">Mengupdate Lokasi Penyimpanan</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="lokasiPenyimpananForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pilihBahan">Pilih Bahan <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihBahan" name="selectedItem">
                                        <option value="">--- Pilih Bahan ---</option>
                                        <?php
                                        while ($row = $resultBahan->fetch_assoc()) {
                                            echo '<option value="' . $row['nama'] . '">'  . $row['kelompok'] . ' - ' .  $row['nama'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <p id="lokasiPenyimpananMessage">Lokasi Penyimpanan Saat ini: <?php echo $currentLokasiPenyimpanan; ?></p>
                                <p id="successMessage">Lokasi Penyimpanan Terbaru: <?php echo $newLokasiPenyimpanan; ?></p>
                                <div class="form-group">
                                    <label for="lokasiPenyimpanan">Lokasi Penyimpanan Terbaru <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="lokasiPenyimpanan" name="lokasiPenyimpanan" min="0" value="" placeholder="Masukkan Lokasi Penyimpanan terbaru">
                                    </div>
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

        $(function() {
            bsCustomFileInput.init();

            // Add an event listener to the select element
            $("#pilihBahan").change(function() {
                validatecurrentLokasiPenyimpanan();
            });
        });

        function validateForm() {
            var selectedItem = document.getElementById("pilihBahan").value;
            var lokasiPenyimpanan = document.getElementById("lokasiPenyimpanan").value;

            if (selectedItem === "" || lokasiPenyimpanan === "") {
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

        function updatelokasiPenyimpananMessage() {
            var lokasiPenyimpananMessage = document.getElementById("lokasiPenyimpananMessage");
            var selectedlokasiPenyimpanan = parseInt(document.getElementById("lokasiPenyimpanan").value, 10);

            // Update stock message dynamically based on the selected item's stock lokasiPenyimpanan
            lokasiPenyimpananMessage.innerText = "Lokasi Penyimpaan Saat Ini: " + (<?php echo $currentLokasiPenyimpanan; ?> + selectedlokasiPenyimpanan);
        }

        function validatecurrentLokasiPenyimpanan() {
            // Get the form data
            var formData = $("#lokasiPenyimpananForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock lokasiPenyimpanan
            $.ajax({
                type: "POST",
                url: "lokasi_penyimpanan.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    // Hide the stock message
                    document.getElementById("successMessage").style.display = "none";
                    // Update the stock message with the fetched lokasiPenyimpanan
                    document.getElementById("lokasiPenyimpananMessage").innerText = "Lokasi Penyimpanan Saat Ini: " +
                        response.currentLokasiPenyimpanan;
                },
                error: function(error) {
                    alert("Error, refresh the page!");
                }
            });
        }

        function validateSuccess() {
            // Get the form data
            var formData = $("#lokasiPenyimpananForm").serialize();

            // Use AJAX to submit the form data and fetch the updated stock lokasiPenyimpanan
            $.ajax({
                type: "POST",
                url: "lokasi_penyimpanan.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    document.getElementById("lokasiPenyimpananMessage").innerText = "Lokasi Penyimpanan Saat Ini: ";

                    Swal.fire({
                        icon: 'success',
                        title: 'Lokasi Penyimpanan berhasil diperbarui!',
                        text: 'Lokasi Penyimpanan terbaru adalah ' + response.newLokasiPenyimpanan,
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
                    alert("Error fetching new material lokasiPenyimpanan.");
                }
            });
        }

        function resetForm() {
            document.getElementById("lokasiPenyimpananForm").reset();
            resetDropdown();
            disablelokasiPenyimpananInput();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihBahan");
            dropdown.selectedIndex = 0; // reset ke pilihan pertama

            // jika multiple selection
            dropdown.querySelectorAll("option:checked").forEach(option => {
                option.selected = false;
            });

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
        }

        // lokasiPenyimpanan input disabled to prevent bugs
        document.addEventListener("DOMContentLoaded", function() {
            disablelokasiPenyimpananInput();
        });

        function disablelokasiPenyimpananInput() {
            const lokasiPenyimpananInput = document.getElementById("lokasiPenyimpanan");
            lokasiPenyimpananInput.placeholder = "Pilih bahan terlebih dahulu";
            lokasiPenyimpananInput.disabled = true;
        }

        $("#pilihBahan").change(function() {
            const lokasiPenyimpananInput = document.getElementById("lokasiPenyimpanan");
            lokasiPenyimpananInput.placeholder = "Masukkan jumlah stok bahan yang dibeli";
            lokasiPenyimpananInput.disabled = false;
        });

        // When user press enter on keyboard
        var lokasiPenyimpananInput = document.getElementById('lokasiPenyimpanan');
        lokasiPenyimpananInput.addEventListener('keydown', function(event) {
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