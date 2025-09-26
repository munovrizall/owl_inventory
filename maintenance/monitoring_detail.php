<?php

include "../connection.php";
include "../admin_privilege.php";

$query = "SELECT dm. *, tm. tanggal_terima, nama_client 
    FROM detail_maintenance dm 
    JOIN transaksi_maintenance tm 
    ON dm.transaksi_id = tm.transaksi_id";
$result = mysqli_query($conn, $query);

file_put_contents("log.txt", json_encode($_POST) . PHP_EOL, FILE_APPEND);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    // Cek apakah parameter yang dibutuhkan ada
    if (!isset($_POST["status"], $_POST["detail_id"])) {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
        exit();
    }

    $status = $_POST["status"];
    $detailId = $_POST["detail_id"];

    // Update status di database
    $queryStatus = "UPDATE detail_maintenance SET status = ? WHERE detail_id = ?";
    $stmtStatus = $conn->prepare($queryStatus);
    $stmtStatus->bind_param("ii", $status, $detailId);

    if ($stmtStatus->execute()) {
        echo json_encode(["success" => true, "message" => "Status diperbarui"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal update"]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_text'])) {
    $detailId = $_POST['detail_id'];
    $field = $_POST['field']; // Bisa 'no_so' atau 'no_resi'
    $value = $_POST['value'];

    // Pastikan hanya field yang diperbolehkan
    if (in_array($field, ['no_so', 'no_resi'])) {
        $queryText = "UPDATE detail_maintenance SET $field = ? WHERE detail_id = ?";
        $stmtText = $conn->prepare($queryText);
        $stmtText->bind_param("si", $value, $detailId);
        
        if ($stmtText->execute()) {
            echo json_encode(["success" => true, "message" => "$field updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update $field"]);
        }
    } 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Maintenance</title>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Table with search -->
    <link href="https://cdn.datatables.net/v/bs4/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-print-2.4.2/fh-3.4.0/r-2.5.0/rg-1.4.1/sb-1.6.0/sp-2.2.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="style.css" />

    <style>
        .lebar-kolom1 {
            width: 60px;
        }

        .lebar-kolom2 {
            width: 120px;
        }

        .lebar-kolom3 {
            width: 120px;
        }

        .lebar-kolom4 {
            width: 10%;
        }

        .lebar-kolom5 {
            width: 200px;
        }

        .lebar-kolom6 {
            width: 20%;
        }

        .lebar-kolom7 {
            width: 20%;
        }

        .lebar-kolom8 {
            width: 15%;
        }

        .lebar-kolom9 {
            width: 15%;
        }

        .column-name {
            font-weight: bold;
        }

        #tableTransaksi th {
            text-align: center;
            vertical-align: middle;
        }

        .column-description {
            font-size: 10px;
            color: #888;
        }

        .card-padding {
            padding: 10px 20px;
        }

        .table-responsive {
            position: relative;
            padding-bottom: 50px;
        }

        #tableTransaksi_wrapper {
            width: 100%;  /* Pastikan wrapper tetap penuh */
            overflow-x: hidden; /* Biar yang scroll cuma tabelnya, bukan wrapper */
        }

        #tableTransaksi {
            width: 100%; /* Biar tetap proporsional */
            min-width: 1200px; /* Sesuaiin biar nggak terlalu kecil */
            display: none; /* Sembunyikan dulu, biar nggak keliatan saat loading */
        }

        .card-body.p-0 .table thead > tr > th:first-of-type,
        .card-body.p-0 .table tbody > tr > td:first-of-type,
        .card-body.p-0 .table tfoot > tr > td:first-of-type {
            padding-left: 0.75rem !important;
        }

        .card-body.p-0 .table thead > tr > th:last-of-type,
        .card-body.p-0 .table tbody > tr > td:last-of-type,
        .card-body.p-0 .table tfoot > tr > td:last-of-type {
            padding-right: 0.75rem !important;
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
                            <h1>Monitoring Transaksi Maintenance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Maintenance</li>
                                <li class="breadcrumb-item active">Monitoring</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <section class="content">

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><b>List Transaksi</b></h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive card-padding">
                                    <div id="loading" style="text-align:center; padding: 200px;">
                                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                                    </div>
                                    <table id="tableTransaksi" class="table table order-list table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="min-width:60px;">Tanggal</th>
                                                <th class="text-center" style="min-width:150px;">Nama Barang</th>
                                                <th class="text-center" style="min-width:120px;">Client</th>
                                                <th class="text-center" style="min-width:60px;">No. SN</th>
                                                <th class="text-center" style="min-width:60px;">Garansi</th>
                                                <th class="text-center" style="min-width:240px;">Kerusakan</th>
                                                <th class="text-center" style="min-width:240px;">Status</th>
                                                <th class="text-center" style="min-width:200px;">No. SO</th>
                                                <th class="text-center" style="min-width:200px;">No. Resi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // ID
                                                $detailId = $row['detail_id'];
                                                // Format date
                                                $tanggal = date('d-m-Y', strtotime($row["tanggal_terima"]));
                                                // Status
                                                $currentStep = $row['status'];
                                            ?>
                                                <!-- Output table row with appropriate status class and text -->
                                                <tr data-detail-id="<?= $detailId ?>">
                                                    <td data-order="<?php echo strtotime($row["tanggal_terima"]); ?>">
                                                            <?php echo $tanggal; ?>
                                                        </td>
                                                    <td><?php echo $row["produk_mt"]; ?></td>
                                                    <td><?php echo $row["nama_client"]; ?></td>
                                                    <td class="text-center"><?php echo $row["no_sn"]; ?></td>
                                                    <td class="text-center"><?php echo ($row["garansi"] == 1) ? "Ya" : "Tidak"; ?></td>
                                                    <td><?php echo $row["keterangan"]; ?></td>
                                                    <td>
                                                        <div class="container">
                                                            <div class="steps">
                                                                <span class="circle"><i class="fa-solid fa-truck-ramp-box"></i></span>
                                                                <span class="circle"><i class="fa-solid fa-file-invoice"></i></span>
                                                                <span class="circle"><i class="fa-solid fa-check"></i></span>
                                                                <span class="circle"><i class="fa-solid fa-tools"></i></span>
                                                                <span class="circle"><i class="fa-solid fa-truck-fast"></i></span>
                                                                <span class="circle"><i class="fa-solid fa-file-signature"></i></span>
                                                                <div class="progress-bar">
                                                                    <span class="indicator"></span>
                                                                </div>
                                                                <input type="hidden" name="status[<?= $detailId ?>]" value=<?php echo $currentStep; ?>>
                                                                <input type="hidden" name="so[<?= $detailId ?>]" value=<?php echo $row['administrasi']; ?>>
                                                            </div>
                                                            <div class="buttons">
                                                                <button id="prev">Prev</button>
                                                                <button id="next">Next</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" class="form-control no-so" name="no_so[<?= $detailId ?>]" value="<?php echo $row['no_so'] ?? ''; ?>"></td>
                                                    <td><input type="text" class="form-control no-resi" name="no_resi[<?= $detailId ?>]" value="<?php echo $row['no_resi'] ?? ''; ?>"></td>

                                                    <!-- <td class="text-center">
                                                        <div class="row">
                                                            <div class="col">
                                                                <a href='edit/edit.php?id= <?php //echo $row["transaksi_id"]; ?>' class="btn btn-info btn-block">Edit</a>
                                                            </div>
                                                            <div class="col">
                                                                <a href='generate_pdf/pdf.php?id=<?php //echo $row["transaksi_id"]; ?>' class="btn btn-block btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                                                            </div>
                                                        </div>
                                                    </td> -->
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- general form elements -->
                            <!-- /.card -->
                            <!-- Pagination links -->

                        </div><!-- /.container-fluid -->
                    </section>
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
    <!-- Datatables -->
    <script src="https://cdn.datatables.net/v/bs4/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-print-2.4.2/fh-3.4.0/r-2.5.0/rg-1.4.1/sb-1.6.0/sp-2.2.0/datatables.min.js"></script>
    <script src="https:////code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

    <script src="script.js" defer></script>

    <!-- Page specific script -->
    <script>
        $(document).ready(function() {
            var table = $('#tableTransaksi').DataTable({
                processing: true,
                responsive: true,
                scrollX: true,
                language: {
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    zeroRecords: 'Data tidak ditemukan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                },
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                buttons: [
                    'pageLength',
                    {
                        extend: 'copy',
                        title: 'Monitoring Transaksi Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'spacer',
                        style: 'bar',
                        text: 'Export files:'
                    },
                    {
                        extend: 'csv',
                        title: 'Monitoring Transaksi Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Monitoring Transaksi Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'Monitoring Transaksi Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Monitoring Transaksi Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                ],
                order: [0, 'desc'],
                initComplete: function() {
                    $('#loading').hide();
                    $('#tableTransaksi').fadeIn();
                }
            });

            table.buttons().container()
                .appendTo('wrapper .col-md-6:eq(0)');

        });
    </script>

</body>

</html>