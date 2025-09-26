<?php

include "../connection.php";
include "../admin_privilege.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve_id"])) {
    $pengajuan_id = $_POST["approve_id"];

    $query = "UPDATE pengajuan SET approved = 1 WHERE pengajuan_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pengajuan_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    exit;
}

$query = "SELECT * FROM pengajuan";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Pengajuan</title>

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

    <style>
        .lebar-kolom1 {
            width: 9%;
        }

        .lebar-kolom2 {
            width: 22%;
        }

        .lebar-kolom3 {
            width: 34%;
        }

        .lebar-kolom4 {
            width: 9%;
        }

        .lebar-kolom5 {
            width: 10%;
        }

        .lebar-kolom6 {
            width: 16%;
        }

        #tablePengajuan, #tablePengajuan_wrapper {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        #loading {
            position: fixed;
            top: 58%;
            left: 59%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
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

        .btn {
            padding: .375rem 0rem;
        }

        .approve-btn {
            position: relative;
            text-align: center;
        }

        .approve-btn i {
            position: relative;
            top: -4px;
        }

        .approve-text {
            font-size: 8px;
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .card-padding {
            padding: 10px 20px;
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
                            <h1>Monitoring Pengajuan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Pengajuan</li>
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
                                <h3 class="card-title"><b>List Pengajuan</b></h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive card-padding">
                                    <div id="loading">
                                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                                    </div>
                                    <table id="tablePengajuan" class="table table order-list table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center lebar-kolom1">Waktu</th>
                                                <th class="text-center lebar-kolom2">No Surat</th>
                                                <th class="text-center lebar-kolom3">Judul Pengajuan</th>
                                                <th class="text-center lebar-kolom4">Total</th>
                                                <th class="text-center lebar-kolom5">Status</th>
                                                <th class="text-center lebar-kolom6 aksi-column" style="min-width: 171.2px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Format tanggal
                                                $tanggal = date('d-m-Y', strtotime($row["waktu"]));
                                                
                                                $pengajuanId = $row["pengajuan_id"];
                                                // Query untuk memeriksa status
                                                $queryDetailStatus = "SELECT COUNT(*) AS total_row, 
                                                        SUM(CASE WHEN status IN ('selesai', 'cancel') THEN 1 ELSE 0 END) AS selesai 
                                                    FROM pengajuan_detail 
                                                    WHERE pengajuan_id = ?
                                                ";

                                                $stmtStatus = $conn->prepare($queryDetailStatus);
                                                $stmtStatus->bind_param("i", $pengajuanId);
                                                $stmtStatus->execute();
                                                $resultStatus = $stmtStatus->get_result();
                                                $statusRow = $resultStatus->fetch_assoc();

                                                $grandTotal = isset($row["grand_total"]) ? (float) $row["grand_total"] : 0;
                                                $grandTotalFormatted = number_format($grandTotal, 0, ',', '.');

                                                if ($row['approved']== 0) {
                                                    $statusClass = 'bg-dark';
                                                    $statusText = 'Pending';
                                                } else {
                                                    if ($statusRow['total_row'] > 0 && $statusRow['total_row'] == $statusRow['selesai']) {
                                                        $statusClass = 'bg-success';
                                                        $statusText = 'Selesai';
                                                    } else {
                                                        $statusClass = 'bg-danger';
                                                        $statusText = 'Belum';
                                                    }
                                                }
                                                    

                                            ?>
                                                <tr>
                                                    <td class="text-center"data-order="<?php echo strtotime($row["waktu"]); ?>">
                                                        <?php echo $tanggal; ?>
                                                    </td>
                                                    <td><?php echo $row["no_surat"]; ?></td>
                                                    <td><?php echo $row["judul_pengajuan"]; ?></td>                                                        
                                                    <td style="text-align: right;"><?php echo $grandTotalFormatted; ?></td>
                                                    <td class="text-center status-cell">
                                                        <span class="badge <?php echo $statusClass; ?>" id="status-<?php echo $pengajuanId; ?>">
                                                            <?php echo $statusText; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="row">
                                                            <?php if ($row['approved'] == 0) { ?>
                                                                <div class="col">
                                                                    <a href='edit_pengajuan.php?id=<?php echo $row["pengajuan_id"]; ?>' class="btn btn-info btn-block">Edit</a>
                                                                </div>
                                                                <div class="col">
                                                                    <a href='generate_pdf/pdf.php?id=<?php echo $row["pengajuan_id"]; ?>' class="btn btn-block btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                                                                </div>
                                                                <div class="col">
                                                                    <button class="btn btn-success btn-block approve-btn position-relative" data-id="<?= $row['pengajuan_id']; ?>">
                                                                        <i class="fas fa-user-check"></i>
                                                                        <span class="approve-text">Approve</span>
                                                                    </button>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="col">
                                                                    <a href='edit/edit.php?id=<?php echo $row["pengajuan_id"]; ?>' class="btn btn-info btn-block">Update</a>
                                                                </div>
                                                                <div class="col">
                                                                    <a href='generate_pdf/pdf.php?id=<?php echo $row["pengajuan_id"]; ?>' class="btn btn-block btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <!-- Page specific script -->
    <script>
        $(document).ready(function() {
            var table = $('#tablePengajuan').DataTable({
                responsive: true,
                language: {
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    zeroRecords: 'Data tidak ditemukan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                },
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                buttons: [
                    'pageLength',
                    {
                        extend: 'copy',
                        title: 'Monitoring Pengajuan Maintenance',
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
                        title: 'Monitoring Pengajuan Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Monitoring Pengajuan Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'Monitoring Pengajuan Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Monitoring Pengajuan Maintenance',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                ],
                order: [0, 'desc'],
                initComplete: function() {
                    $('#loading').hide();
                    $('#tablePengajuan, #tablePengajuan_wrapper').css('opacity', '1');
                }
            });

            table.buttons().container()
                .appendTo('wrapper .col-md-6:eq(0)');

            $("table.order-list").on("click", ".ibtnEdit", function(event) {
                var idToEdit = 123;

                // Lakukan redirect dengan menyertakan ID sebagai parameter
                window.location.href = 'edit/edit.php?id=' + idToEdit;
            });

            $(document).on("click", ".approve-btn", function () {
                var pengajuanId = $(this).data("id");
                var button = $(this);

                Swal.fire({
                    icon: "warning",
                    title: "Yakin Approve?",
                    text: "Setelah disetujui, pengajuan tidak bisa diedit lagi!",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Approve!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "monitoring_pengajuan.php",
                            type: "POST",
                            data: { approve_id: pengajuanId },
                            success: function (response) {
                                if (response === "success") {
                                    Swal.fire("Approved!", "Pengajuan telah disetujui.", "success");

                                    // Ubah button
                                    button.closest(".row").html(`
                                        <div class="col">
                                            <a href='edit/edit.php?id=${pengajuanId}' class="btn btn-info btn-block">Update</a>
                                        </div>
                                        <div class="col">
                                            <a href='generate_pdf/pdf.php?id=${pengajuanId}' class="btn btn-block btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                                        </div>
                                    `);

                                    // Ubah status
                                    $("#status-" + pengajuanId).removeClass("bg-dark bg-success").addClass("bg-danger").text("Belum");
                                } else {
                                    Swal.fire("Error!", "Terjadi kesalahan, coba lagi.", "error");
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>

</body>

</html>