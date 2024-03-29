<?php

include "../connection.php";
include "../admin_privilege.php";

$queryTransaksiId = "SELECT MAX(transaksi_id) AS last_transaksi_id FROM transaksi_maintenance";
$resultTransaksiId = $conn->query($queryTransaksiId);

if (isset($_POST['tanggal'])) {
    // Handle the POST request for submitting form data
    $tanggal = $_POST["tanggal"];

    // Initialize the array to store fetched nama_client values
    $namaClientArray = array();

    // Loop through the arrays and insert records
    foreach ($_POST["numberSN"] as $key => $no_sn) {
        // Fetch nama_client based on no_sn from inventaris_produk
        $queryFetchClient = "SELECT nama_client FROM inventaris_produk WHERE no_sn = ?";
        $stmtFetchClient = $conn->prepare($queryFetchClient);
        $stmtFetchClient->bind_param("i", $no_sn);
        $stmtFetchClient->execute();
        $stmtFetchClient->bind_result($nama_client_fetched);

        // Fetch the result and ensure all fetched nama_client values are the same
        $stmtFetchClient->fetch();
        $stmtFetchClient->close();

        if ($key === 0) {
            // Store the first fetched nama_client value
            $namaClientArray[] = $nama_client_fetched;
        } else {
            // Check if the current fetched nama_client matches the stored value
            if ($nama_client_fetched !== $namaClientArray[0]) {
                echo "Error: Fetched nama_client values are not the same.";
                exit; // Exit the script if validation fails
            }
        }
    }

    // Continue with the transaction maintenance
    $query = "INSERT INTO transaksi_maintenance (tanggal_terima) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tanggal);

    if ($stmt->execute()) {
        // Get the auto-generated transaksi_id
        $transaksi_id = $conn->insert_id;

        // Insert successful, continue with the detail maintenance
        if (isset($_POST["numberSN"]) && isset($_POST["inputKerusakan"])) {
            $numberSNArray = $_POST["numberSN"];
            $keteranganArray = $_POST["inputKerusakan"];
            $pilihGaransiArray = $_POST["pilihGaransi"];
            $keteranganVoidArray = $_POST["keteranganVoid"];

            foreach ($numberSNArray as $key => $no_sn) {
                $keterangan = $keteranganArray[$key];
                $pilihGaransi = $pilihGaransiArray[$key];
                $keteranganVoid = !empty($keteranganVoidArray[$key]) ? $keteranganVoidArray[$key] : "-";

                // Insert the new record into detail_maintenance table
                $queryDetail = "INSERT INTO detail_maintenance (transaksi_id, no_sn, 
                keterangan, kedatangan, cek_barang, berita_as, administrasi, pengiriman, no_resi) VALUES (?, ?, ?, '1', '0','0','0','0', '0')";
                $stmtDetail = $conn->prepare($queryDetail);
                $stmtDetail->bind_param("iis", $transaksi_id, $no_sn, $keterangan);

                if ($stmtDetail->execute()) {
                    $stmtDetail->close();

                    // Update the garansi_void column in inventaris_produk table
                    $queryUpdateInventaris = "UPDATE inventaris_produk SET garansi_void = ?, keterangan_void = ? WHERE no_sn = ?";
                    $stmtUpdateInventaris = $conn->prepare($queryUpdateInventaris);
                    $stmtUpdateInventaris->bind_param("isi", $pilihGaransi, $keteranganVoid, $no_sn);

                    if ($stmtUpdateInventaris->execute()) {
                        $stmtUpdateInventaris->close();
                    } else {
                        echo "Error updating inventaris_produk: " . $stmtUpdateInventaris->error;
                    }
                } else {
                    echo "Error inserting record into detail_maintenance: " . $stmtDetail->error;
                }
            }
            // Validate and update transaksi_maintenance table
            if (count($namaClientArray) > 0) {
                $nama_client = $namaClientArray[0];

                $queryUpdateClient = "UPDATE transaksi_maintenance SET nama_client = ? WHERE transaksi_id = ?";
                $stmtUpdateClient = $conn->prepare($queryUpdateClient);
                $stmtUpdateClient->bind_param("si", $nama_client, $transaksi_id);

                if (!$stmtUpdateClient->execute()) {
                    echo "Error updating transaksi_maintenance: " . $stmtUpdateClient->error;
                }

                $stmtUpdateClient->close();
            } else {
                echo "Error: No nama_client values fetched.";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            width: 20%;
        }

        .lebar-kolom2 {
            width: 20%;
        }

        .lebar-kolom3 {
            width: 15%;
        }

        .lebar-kolom4 {
            width: 25%;
        }

        .lebar-kolom5 {
            width: 10%;
        }

        .larger-checkbox {
            width: 20px;
            height: 20px;
        }

        .select2-container--default .select2-results__option[data-selected="1"]:hover {
            background-color: red;
            color: white;
        }

        .column-name {
            font-weight: bold;
        }

        .column-description {
            font-size: 10px;
            color: #888;
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
                            <h1>Input Transaksi Maintenance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
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
                                    <p id="idTransaksi"><?php $row = $resultTransaksiId->fetch_assoc();
                                                        $maxTransaksiId = $row['last_transaksi_id'] + 1;
                                                        echo "ID Transaksi: Maintenance/" . $maxTransaksiId;
                                                        ?>
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal Transaksi <span style="color: red;">*</span></label>
                                        <div class="input-group date" id="datepicker" data-target-input="nearest">
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" placeholder="Masukkan tanggal transaksi" />
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="tableDetail" class=" table order-list table-striped">
                                            <thead>
                                                <tr>
                                                    <td class="text-center lebar-kolom1" style="min-width:160px;"><b>Nomor SN <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom2" style="min-width:120px;"><b>Kerusakan <span style="color: red;">*</span></b></td>
                                                    <td class="text-center lebar-kolom3" style="min-width:140px;"><b>Garansi Void <span style="color: red;">*</span></b></td>
                                                    <th class="text-center lebar-kolom4" style="min-width:180px;">
                                                        <div class="column-name">Keterangan Void</div>
                                                        <div class="column-description">Kosongkan jika tidak void</div>
                                                    </th>
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
                    type: 'POST',
                    url: 'input.php',
                    success: function(response) {
                        var dropdownGaransi = '<option value="" selected disabled>Pilih Void</option>' +
                            '<option value="1">Void</option>' +
                            '<option value="0">Tidak Void</option>';
                        var newRow = $("<tr>");
                        var cols = "";

                        cols += '<td><input type="number" class="form-control" name="numberSN[]" value="" placeholder="Nomor SN"/></td>';
                        cols += '<td><input type="text" class="form-control" name="inputKerusakan[]" value="" placeholder="Kerusakan Device"/></td>';
                        cols += '<td><select class="form-control select2" id="pilihGaransi ' + counter + '" name="pilihGaransi[]' + counter + '">' + dropdownGaransi + '</select></td>';
                        cols += '<td><input type="text" class="form-control" name="keteranganVoid[]" value="" placeholder="Keterangan Void"/></td>';
                        cols += '<td class="text-center"><input type="button" class="ibtnDel btn btn-md btn-danger"  value="Delete"></td>';

                        newRow.append(cols);
                        $("table.order-list").append(newRow);
                        counter++;

                        $('.select2').select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            containerCssClass: 'height-40px',
                        });
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
            var namaClientArray = [];
            var tanggal = document.getElementById("tanggal").value;

            if (tanggal === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap isi tanggal!',
                });
                return false;
            }

            // Use classes for dynamic elements
            var numberSNElements = document.querySelectorAll("[name^='numberSN']");
            var keteranganElements = document.querySelectorAll("[name^='inputKerusakan']");
            var garansiElements = document.querySelectorAll("[name^='pilihGaransi']");

            // Check each row
            for (var i = 0; i < numberSNElements.length; i++) {
                var numberSN = numberSNElements[i].value;
                var keterangan = keteranganElements[i].value;
                var garansi = garansiElements[i].value;


                if (numberSN === "" || keterangan === "" || garansi === "") {
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
    </script>
</body>

</html>