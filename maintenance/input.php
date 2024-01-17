<?php
session_start();

$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "databaseinventory";

$conn = new mysqli($serverName, $userName, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET["getDropdownOptions"])) {

    $queryMasterBahan = "SELECT nama FROM masterbahan ORDER BY nama";

    $resultMasterBahan = $conn->query($queryMasterBahan);

    $options = '<option value="" selected disabled>Pilih Bahan</option>';

    if ($resultMasterBahan && $resultMasterBahan->num_rows > 0) {
        while ($row = $resultMasterBahan->fetch_assoc()) {
            $options .= '<option value="' . $row['nama'] . '">' . $row['nama'] . '</option>';
        }
    }
    echo $options;
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle the POST request for submitting form data
    $namaDevice = $_POST["namaDevice"];

    // Check if 'bahan' and 'quantity' arrays are set in POST
    if (isset($_POST["pilihNamaBahan"]) && isset($_POST["quantity"])) {
        $bahanArray = $_POST["pilihNamaBahan"];
        $quantityArray = $_POST["quantity"];

        // Loop through the arrays and insert records
        foreach ($bahanArray as $key => $bahan) {
            $quantity = $quantityArray[$key];

            // Use prepared statements
            $checkQuery = "SELECT COUNT(*) FROM produksi WHERE produk = ? AND nama_bahan = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("ss", $namaDevice, $bahan);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                echo "Error: Device and Bahan combination already exists in the database.";
            } else {
                // Insert the new record
                $query = "INSERT INTO produksi (produk, nama_bahan, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssi", $namaDevice, $bahan, $quantity);

                if ($stmt->execute()) {
                    echo "Data berhasil ditambahkan ke tabel produksi.";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    } else {
        echo "Error: Bahan and Quantity arrays are not set.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Maintenance</title>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <style>
        .input-group-append label {
            margin-right: 24px;
        }

        .form-select {
            display: block;
            width: 100%;
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            -moz-padding-start: calc(0.75rem - 3px);
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .lebar-kolom1 {
            width: 30%;
        }

        .lebar-kolom2 {
            width: 15%;
        }

        .lebar-kolom3 {
            width: 15%;
        }

        .lebar-kolom4 {
            width: 30%;
        }

        .lebar-kolom5 {
            width: 10%;
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

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4 fixed">
            <!-- Brand Logo -->
            <a href="../homepage.php" class="brand-link">
                <img src="../assets/adminlte/dist/img/OWLlogo.png" alt="OWL Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-heavy">OWL RnD</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="../homepage.php" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        </li>
                        <li class="nav-header">TRANSAKSI</li>
                        <li class="nav-item">
                            <a href="../produksi.php" class="nav-link">
                                <i class="nav-icon fas fa-toolbox"></i>
                                <p>
                                    Produksi
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../maintenance.php" class="nav-link active">
                                <i class="nav-icon fas fa-wrench"></i>
                                <p>
                                    Maintenance
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./input.php" class="nav-link active">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./monitoring.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./update.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Update</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="../prototype.php" class="nav-link">
                                <i class="nav-icon fas fa-screwdriver"></i>
                                <p>
                                    Prototype
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../restock.php" class="nav-link">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>
                                    Restock
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">TAMBAH DATA</li>
                        <li class="nav-item">
                            <a href="../master_bahan.php" class="nav-link">
                                <i class="nav-icon fa fa-pen"></i>
                                <p>
                                    Master Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../master_device.php" class="nav-link">
                                <i class="nav-icon fas fa-cube"></i>
                                <p>
                                    Master Device
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">PELAPORAN</li>
                        <li class="nav-item">
                            <a href="../laporan_stok.php" class="nav-link">
                                <i class="nav-icon ion ion-pie-graph"></i>
                                <p>
                                    Laporan Stok
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../histori_transaksi.php" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>
                                    Histori Transaksi
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Input Transaksi Maintenance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Maintenance</a></li>
                                <li class="breadcrumb-item active">Input</li>
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
                            <h3 class="card-title">Menambah Transaksi Maintenance</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="masterDeviceForm">
                            <div class="card-body">
                                <div class="col">
                                    <p id="idTransaksi">ID Transaksi: 1/1/FP301T/001</p>
                                    <div class="form-group">
                                        <label>Tanggal Transaksi <span style="color: red;">*</span></label>
                                        <div class="input-group date" id="datepicker" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#datepicker" placeholder="Masukkan tanggal transaksi" />
                                            <div class="input-group-append" data-target="#datepicker" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col">
                                        <label for="pilihNamaPT">Pilih PT <span style="color: red;">*</span></label>
                                        <select class="form-select" id="pilihNamaPT" name="kelompok">
                                            <option value="">Pilih PT</option>
                                            <option value="2">PT 1</option>
                                            <option value="3">PT 2</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#modalBuatPT" style="margin-top: 10px; max-width: 180px;"><i class="fas fa-plus" style="margin-right: 8px;" onclick=""></i>Tambah PT</button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="tableDetail" class=" table order-list table-striped">
                                            <thead>
                                                <tr>
                                                    <td class="text-center lebar-kolom1"><b>Nama Produk <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom2"><b>Nomor SN <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom3"><b>Garansi? <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom4"><b>Kerusakan <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom5"><b>Aksi</b></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>

                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" style="text-align: left;">
                                                        <input type="button" class="btn btn-outline-info btn-block" id="addrow" value="+  Tambah Detail" />
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- /.card-body -->
                        </form>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="if(validateForm()) { validateSuccess(); resetForm(); }">Submit</button>
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

        <form id="tambahPTForm">
            <div class="modal fade" id="modalBuatPT" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Buat PT Baru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="namaKelompokBaru">Nama PT <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="namaPTBaru" name="namaPTBaru" placeholder="Masukkan nama PT yang ingin dibuat">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="if(validateFormKB()) { validateSuccessKB(); resetForm(); }">Submit</button>
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
    <!-- bootstrap searchable dropdown -->
    <script src="../assets/bootstrap-5/bootstrap.bundle.min.js"></script>
    <script src="../assets/dselect.js"></script>
    <!-- InputMask -->
    <script src="../assets/adminlte/plugins/moment/moment.min.js"></script>
    <script src="../assets/adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../assets/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script>
        $(function() {
            $('#datepicker').datetimepicker({
                format: 'DD-MM-YYYY',
                locale: 'id',
            });
        });

        $(document).ready(function() {
            var counter = 0;

            $("#addrow").on("click", function() {
                addRow();
            });

            $("table.order-list").on("click", ".ibtnDel", function(event) {
                $(this).closest("tr").remove();
                calculateGrandTotal();
            });

            function addRow() {

                // Make an AJAX request to fetch dropdown options
                $.ajax({
                    url: '../master_device.php?getDropdownOptions',
                    type: 'GET',
                    success: function(dropdownOptions) {
                        var dropdownGaransi = '<option value="" selected disabled>Garansi</option>' +
                            '<option value="1">Ya</option>' +
                            '<option value="0">Tidak</option>';
                        var newRow = $("<tr>");
                        var cols = "";

                        cols += '<td><select class="form-select pilihNamaProduk" name="pilihNamaProduk[]">' + dropdownOptions + '</select></td>';
                        cols += '<td><input type="text" class="form-control" name="numberSN[]" value="" placeholder="Nomor SN"/></td>';
                        cols += '<td><select class="form-select" id="pilihGaransi ' + counter + '" name="pilihGaransi' + counter + '">' + dropdownGaransi + '</select></td>';
                        cols += '<td><input type="text" class="form-control" name="inputKerusakan[]" value="" placeholder="Kerusakan Device"/></td>';
                        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger"  value="Delete"></td>';

                        newRow.append(cols);
                        $("table.order-list").append(newRow);
                        counter++;
                    },
                    error: function(error) {
                        console.log("Error fetching dropdown options: " + error);
                    }
                });
            }
        });


        function calculateRow(row) {
            var price = +row.find('input[name^="price"]').val();
        }

        function calculateGrandTotal() {
            var grandTotal = 0;
            $("table.order-list").find('input[name^="price"]').each(function() {
                grandTotal += +$(this).val();
            });
            $("#grandtotal").text(grandTotal.toFixed(2));
        }

        function validateForm() {
            var selectedItem = document.getElementById("namaDevice").value;

            // Use classes for dynamic elements
            var namaElements = document.querySelectorAll(".pilihNamaBahan");
            var quantityElements = document.querySelectorAll(".quantity");

            // Check each row
            for (var i = 0; i < namaElements.length; i++) {
                var nama = namaElements[i].value;
                var quantity = quantityElements[i].value;

                if (selectedItem === "" || nama === "" || quantity === "" || quantity <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Harap lengkapi semua formulir!',
                    });
                    return false;
                }
            }

            return true;
        }


        function validateSuccess() {
            var formData = new FormData(document.getElementById("masterDeviceForm"));

            $.ajax({
                type: "POST",
                url: "master_device.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Device berhasil didaftarkan!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(error) {
                    // ... existing code ...
                }
            });
        }

        // Searchable dropdown
        var select_box_element = document.querySelector('#pilihNamaPT');
        dselect(select_box_element, {
            search: true,
        });


        function resetForm() {
            document.getElementById("masterDeviceForm").reset();
            resetDropdown();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihNamaBahan");
            dropdown.selectedIndex = 0; // reset ke pilihan pertama

            // jika multiple selection
            dropdown.querySelectorAll("option:checked").forEach(option => {
                option.selected = false;
            });

            // memicu event change 
            dropdown.dispatchEvent(new Event('change'));
        }
    </script>
</body>

</html>