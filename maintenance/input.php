<?php

include "../connection.php";

$queryClient = "SELECT * FROM client ORDER BY nama_client";
$resultClient = $conn->query($queryClient);

if (!$resultClient) {
    die("Error fetching kelompok data: " . $conn->error);
}

if (isset($_GET["getDropdownOptions"])) {

    $queryProduk = "SELECT DISTINCT produk FROM produksi ORDER BY produk";
    $resultProduk = $conn->query($queryProduk);
    $options = '<option value="" selected disabled>Pilih Produk</option>';

    if ($resultProduk && $resultProduk->num_rows > 0) {
        while ($row = $resultProduk->fetch_assoc()) {
            $options .= '<option value="' . $row['produk'] . '">' . $row['produk'] . '</option>';
        }
    }
    echo $options;
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the POST request for submitting form data
    $tanggal = $_POST["tanggal"];
    $nama_client = $_POST["client"];

    // Check if the client needs to be added
    if (isset($_POST["namaPTBaru"]) && !empty($_POST["namaPTBaru"])) {
        $newClientName = $_POST["namaPTBaru"];

        // Check if the client already exists in the database
        $checkClientQuery = "SELECT nama_client FROM client WHERE nama_client = ?";
        $stmtCheck = $conn->prepare($checkClientQuery);
        $stmtCheck->bind_param("s", $newClientName);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        // If the client already exists, show an error
        if ($stmtCheck->num_rows > 0) {
            echo "Client with the name '$newClientName' already exists in the database.";
            $stmtCheck->close();
            exit();
        }

        $stmtCheck->close();

        // Perform insertion of a new client into the database
        $insertClientQuery = "INSERT INTO client (nama_client) VALUES (?)";
        $stmt = $conn->prepare($insertClientQuery);
        $stmt->bind_param("s", $newClientName);

        if ($stmt->execute()) {
            // Insert successful, continue with the transaction maintenance
            $stmt->close();
        } else {
            echo "Error adding a new client: " . $stmt->error;
            exit();
        }
    }

    // Continue with the transaction maintenance
    $query = "INSERT INTO transaksi_maintenance (tanggal_terima, nama_client) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $tanggal, $nama_client);

    if ($stmt->execute()) {
        // Get the auto-generated transaksi_id
        $transaksi_id = $conn->insert_id;

        // Insert successful, continue with the detail maintenance
        if (isset($_POST["pilihNamaProduk"]) && isset($_POST["numberSN"]) && isset($_POST["pilihGaransi"]) && isset($_POST["inputKerusakan"])) {
            $produkArray = $_POST["pilihNamaProduk"];
            $numberSNArray = $_POST["numberSN"];
            $garansiArray = $_POST["pilihGaransi"];
            $keteranganArray = $_POST["inputKerusakan"];

            // Loop through the arrays and insert records
            foreach ($produkArray as $key => $nama_produk) {
                $no_sn = $numberSNArray[$key];
                $garansi = $garansiArray[$key];
                $keterangan = $keteranganArray[$key];

                // Insert the new record into detail_maintenance table
                echo "Produk: $nama_produk, SN: $no_sn, Garansi: $garansi, Keterangan: $keterangan";
                $queryDetail = "INSERT INTO detail_maintenance (transaksi_id, produk_mt, no_sn, garansi, 
                keterangan, kedatangan, cek_barang, berita_as, administrasi, pengiriman, no_resi) VALUES (?, ?, ?, ?, ?, '1', '0','0','0','0', '0')";
                $stmtDetail = $conn->prepare($queryDetail);
                $stmtDetail->bind_param("isiis", $transaksi_id, $nama_produk, $no_sn, $garansi, $keterangan);

                if ($stmtDetail->execute()) {
                    echo "Data berhasil ditambahkan ke tabel detail.";

                    // Retrieve the generated detail_id
                    $detail_id = $stmtDetail->insert_id;

                    // Insert the corresponding record into prg_maintenance table
                    $queryPrgMaintenance = "INSERT INTO prg_maintenance (detail_id) VALUES (?)";
                    $stmtPrgMaintenance = $conn->prepare($queryPrgMaintenance);
                    $stmtPrgMaintenance->bind_param("i", $detail_id);

                    if ($stmtPrgMaintenance->execute()) {
                        echo "Data berhasil ditambahkan ke tabel prg_maintenance.";
                    } else {
                        echo "Error: " . $stmtPrgMaintenance->error;
                    }

                    $stmtPrgMaintenance->close();

                } else {
                    echo "Error: " . $stmtDetail->error;
                }

                $stmtDetail->close();
            }
        } else {
            echo "Error: Products and other arrays are not set.";
        }
    } else {
        echo "Error adding transaction maintenance: " . $stmt->error;
    }

    $stmt->close();
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
    <!-- Select2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        .input-group-append label {
            margin-right: 24px;
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
                        <form id="inputMaintenanceForm">
                            <div class="card-body">
                                <div class="col">
                                    <p id="idTransaksi">ID Transaksi: 1/1/FP301T/001</p>
                                    <div class="form-group">
                                        <label>Tanggal Transaksi <span style="color: red;">*</span></label>
                                        <div class="input-group date" id="datepicker" data-target-input="nearest">
                                            <input type="date" class="form-control datetimepicker-input" id="tanggal" name="tanggal" placeholder="Masukkan tanggal transaksi" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col">
                                        <label for="pilihClient">Pilih PT <span style="color: red;">*</span></label>
                                        <select class="form-control select2" id="pilihClient" name="client">
                                            <option value="">--- Pilih PT ---</option>
                                            <?php
                                            while ($row = $resultClient->fetch_assoc()) {
                                                echo '<option value="' . $row['nama_client'] . '">' . $row['nama_client'] . '</option>';
                                            }
                                            ?>
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
                                <label for="namaClientBaru">Nama PT <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="namaPTBaru" name="namaPTBaru" placeholder="Masukkan nama PT yang ingin dibuat">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="if(validateFormPT()) { validateSuccessPT(); resetForm(); }">Submit</button>
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
    <!-- Select2 -->
    <script src="../assets/adminlte/plugins/select2/js/select2.full.min.js"></script>

    <script>
        // Select2 Dropdown
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $(function() {
            $('#datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'id',
            });
        });

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                containerCssClass: 'height-40px',
            });

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
                    url: 'input.php?getDropdownOptions',
                    type: 'GET',
                    success: function(dropdownOptions) {
                        var dropdownGaransi = '<option value="" selected disabled>Garansi</option>' +
                            '<option value="1">Ya</option>' +
                            '<option value="0">Tidak</option>';
                        var newRow = $("<tr>");
                        var cols = "";

                        cols += '<td><select class="form-control select2 pilihNamaProduk" name="pilihNamaProduk[]">' + dropdownOptions + '</select></td>';
                        cols += '<td><input type="text" class="form-control" name="numberSN[]" value="" placeholder="Nomor SN"/></td>';
                        cols += '<td><select class="form-control select2" id="pilihGaransi ' + counter + '" name="pilihGaransi[]' + counter + '">' + dropdownGaransi + '</select></td>';
                        cols += '<td><input type="text" class="form-control" name="inputKerusakan[]" value="" placeholder="Kerusakan Device"/></td>';
                        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger"  value="Delete"></td>';

                        newRow.append(cols);
                        $("table.order-list").append(newRow);
                        counter++;
                    },
                    error: function(xhr, status, error) {
                        console.log("Error fetching dropdown options:");
                        console.log("Status: " + status);
                        console.log("Error: " + error);
                        console.log("Response Text: " + xhr.responseText);
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
            var tanggal = document.getElementById("tanggal").value;

            // Use classes for dynamic elements
            var namaElements = document.querySelectorAll(".pilihNamaProduk");
            var numberSNElements = document.querySelectorAll("[name^='numberSN']");
            var garansiElements = document.querySelectorAll("[name^='pilihGaransi']");
            var keteranganElements = document.querySelectorAll("[name^='inputKerusakan']");

            // Check each row
            for (var i = 0; i < namaElements.length; i++) {
                var nama = namaElements[i].value;
                var numberSN = numberSNElements[i].value;
                var garansi = garansiElements[i].value;
                var keterangan = keteranganElements[i].value;

                if (nama === "" || numberSN === "" || garansi === "" || keterangan === "") {
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
            var formData = new FormData(document.getElementById("inputMaintenanceForm"));
            formData.append('getDropdownOptions', '1');

            $.ajax({
                type: "POST",
                url: "input.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Maintenance berhasil didaftarkan!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(error) {
                    console.log("Error:", error);
                }
            });
        }

        function validateFormPT() {
            var namaKB = document.getElementById("namaPTBaru").value;

            if (namaKB === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap lengkapi semua formulir!',
                });
                return false;
            }

            return true;
        }

        function validateSuccessPT() {
            // Get the form data
            var formDataKB = $("#tambahPTForm").serialize();

            $.ajax({
                type: "POST",
                url: "input.php",
                data: formDataKB,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Client berhasil didaftarkan!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });


                },
                error: function(error) {
                    alert("Error mendaftarkan Client.");
                }
            });
        }


        function resetForm() {
            document.getElementById("inputMaintenanceForm").reset();
            resetDropdown();
        }

        function resetDropdown() {
            const dropdown = document.getElementById("pilihNamaProduk");

            // Check if the dropdown element exists
            if (dropdown) {
                // Reset to the first option if it's a single-selection dropdown
                if (dropdown.selectedIndex !== undefined) {
                    dropdown.selectedIndex = 0;
                }

                // If it's a multiple selection dropdown
                if (dropdown.options) {
                    dropdown.options.forEach(option => {
                        option.selected = false;
                    });
                }

                // Trigger the change event
                dropdown.dispatchEvent(new Event('change'));
            } else {
                console.error("Dropdown element with ID 'pilihNamaProduk' not found.");
            }
        }
    </script>
</body>

</html>