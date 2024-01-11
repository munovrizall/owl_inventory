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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaDevice = $_POST["namaDevice"];

    // Check if the material name already exists
    $checkQuery = "SELECT COUNT(*) FROM masterkelompok WHERE nama = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $namaDevice);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "Error: Material name '$nama' already exists in the database.";
    } else {
        // Insert the new record
        $query = "INSERT INTO masterkelompok (nama_kelompok) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $namaDevice);

        if ($stmt->execute()) {
            echo "Data berhasil ditambahkan ke tabel masterkelompok.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
// Fetch data from masterkelompok table
$queryMasterBahan = "SELECT nama FROM masterbahan";
$resultMasterBahan = $conn->query($queryMasterBahan);

if (!$resultMasterBahan) {
    die("Error fetching kelompok data: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Bahan</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

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
            width: 78%;
        }

        .lebar-kolom2 {
            width: 12%;
        }

        .lebar-kolom3 {
            width: 10%;
        }
    </style>


</head>

<body class="hold-transition sidebar-mini">
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
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="homepage.php" class="brand-link">
                <img src="assets/adminlte/dist/img/OWLlogo.png" alt="OWL Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
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
                            <a href="./homepage.php" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        </li>
                        <li class="nav-header">TRANSAKSI</li>
                        <li class="nav-item">
                            <a href="produksi.php" class="nav-link">
                                <i class="nav-icon fas fa-toolbox"></i>
                                <p>
                                    Produksi
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="maintenance.php" class="nav-link">
                                <i class="nav-icon fas fa-wrench"></i>
                                <p>
                                    Maintenance
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="restock.php" class="nav-link">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>
                                    Restock
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">TAMBAH DATA</li>
                        <li class="nav-item">
                            <a href="master_bahan.php" class="nav-link">
                                <i class="nav-icon fa fa-pen"></i>
                                <p>
                                    Master Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="master_device.php" class="nav-link active">
                                <i class="nav-icon fas fa-cube"></i>
                                <p>
                                    Master Device
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">PELAPORAN</li>
                        <li class="nav-item">
                            <a href="laporan_stok.php" class="nav-link">
                                <i class="nav-icon ion ion-pie-graph"></i>
                                <p>
                                    Laporan Stok
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
                            <h1>Master Device</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Master Device</li>
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
                            <h3 class="card-title">Menambah Jenis Device untuk Diproduksi</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="masterDeviceForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col">
                                            <label for="namaDevice">Masukkan Nama Device <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2" id="namaDevice" name="namaDevice" placeholder="Masukkan nama device baru">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="myTable" class=" table order-list table-striped">
                                            <thead>
                                                <tr>
                                                    <td class="text-center lebar-kolom1"><b>Nama Bahan <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom2"><b>Kuantitas <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom3"><b>Aksi</b></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <!-- <td class="col-sm-8">
                                                        <select class="form-select" id="pilihNamaBahan" name="bahan">
                                                            <option value="">Pilih Kelompok</option>
                                                            <option value="1">Resistor</option>
                                                            <option value="2">Transistor</option>
                                                            <option value="3">Kapasitor</option>
                                                            <option value="4">Dioda</option>
                                                            <option value="5">Mosfet</option>
                                                            <option value="6">Sensor</option>
                                                        </select>
                                                    </td>
                                                    <td class="col-sm-4">
                                                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="" placeholder="Jumlah bahan">
                                                    </td>
                                                    <td class="col-sm-2"><a class="deleteRow"></a>
                                                    </td> -->
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" style="text-align: left;">
                                                        <input type="button" class="btn btn-outline-info btn-block" id="addrow" value="+  Bahan lain" />
                                                    </td>
                                                </tr>
                                                <tr>
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
    <!-- bootstrap searchable dropdown -->
    <script src="assets/bootstrap-5/bootstrap.bundle.min.js"></script>
    <script src="assets/dselect.js"></script>
    <!-- Page specific script -->
    <script>
        $(document).ready(function() {
            var counter = 0;

            $("#addrow").on("click", function() {
                var dropdownOptions = '<option value="" selected disabled>Pilih Bahan</option>' +
                    '<option value="1">Resistor</option>' +
                    '<option value="2">Transistor</option>' +
                    '<option value="3">Kapasitor</option>' +
                    '<option value="4">Dioda</option>' +
                    '<option value="5">Mosfet</option>' +
                    '<option value="6">Sensor</option>';
                var newRow = $("<tr>");
                var cols = "";

                cols += '<td><select class="form-select" id="pilihNamaBahan ' + counter + '" name="pilihNamaBahan' + counter + '">' + dropdownOptions + '</select></td>';
                cols += '<td><input type="number" class="form-control" id="quantity ' + counter + '" name="quantity' + counter + '" min="0" value="" placeholder="0"/></td>';

                cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger"  value="Delete"></td>';
                newRow.append(cols);
                $("table.order-list").append(newRow);
                counter++;
            });

            $("table.order-list").on("click", ".ibtnDel", function(event) {
                $(this).closest("tr").remove();
                counter -= 1
            });


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
            var nama = document.getElementById("namaBahan").value;
            var quantity = document.getElementById("quantity").value;

            if (selectedItem === "" || nama === "" || quantity === "" || quantity <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                });
                return false;
            }

            return true;
        }


        function validateSuccess() {
            // Get the form data
            var formData = $("#masterDeviceForm").serialize();

            $.ajax({
                type: "POST",
                url: "master_device.php",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Device berhasil didaftarkan!',
                        showConfirmButton: false,
                        timer: 3000
                    });

                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Barang sudah terdaftar!',
                        text: 'Masukkan nama baru!',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetForm();
                        }
                    });
                }
            });
        }

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

        // Searchable dropdown
        var select_box_element = document.querySelector('#pilihNamaBahan');
        dselect(select_box_element, {
            search: false,
        });
    </script>
</body>

</html>