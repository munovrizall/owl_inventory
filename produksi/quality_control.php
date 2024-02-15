<?php

include "../connection.php";

$query = "SELECT * FROM inventaris_produk ORDER BY id";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quality Control</title>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/fontawesome-free/css/all.min.css">
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
            width: 10%;
        }

        .lebar-kolom2 {
            width: 10%;
        }

        .lebar-kolom3 {
            width: 10%;
        }

        .lebar-kolom4 {
            width: 30%;
        }

        .lebar-kolom5 {
            width: 20%;
        }

        .lebar-kolom6 {
            width: 10%;
        }

        .lebar-kolom7 {
            width: 10%;
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
                            <h1>Quality Control</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Produksi</li>
                                <li class="breadcrumb-item active">Quality Control</li>
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
                                <h3 class="card-title"><b>List Device</b></h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive card-padding">
                                    <table id="tableInventaris" class="table table order-list table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center lebar-kolom1">ID</th>
                                                <th class="text-center lebar-kolom2">Chip ID</th>
                                                <th class="text-center lebar-kolom3">No SN</th>
                                                <th class="text-center lebar-kolom4">Nama Produk</th>
                                                <th class="text-center lebar-kolom5">Terakhir Online</th>
                                                <th class="text-center lebar-kolom6">Status</th>
                                                <th class="text-center lebar-kolom7 aksi-column">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            date_default_timezone_set('Asia/Jakarta');
                                            
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $lastOnline = $row['last_online'];
    
                                                if ($lastOnline !== null) {
                                                    $currentDateTime = new DateTime();
                                                    $lastOnlineDateTime = new DateTime($lastOnline);
                                                    $timeDifference = date_diff($currentDateTime, $lastOnlineDateTime);
                                                    $minutesAgo = $timeDifference->days * 24 * 60 + $timeDifference->h * 60 + $timeDifference->i;
                                                    $resultLastOnline = "";
    
                                                    if ($minutesAgo < 60) {
                                                        $resultLastOnline = $minutesAgo . " menit yang lalu";
                                                    } elseif ($minutesAgo < 1440) {
                                                        $resultLastOnline = floor($minutesAgo / 60) . " jam yang lalu";
                                                    } else {
                                                        $resultLastOnline = floor($minutesAgo / 1440) . " hari yang lalu";
                                                    }
                                                } else {
                                                    $resultLastOnline = "-";
                                                }

                                                $statusClass = 'badge bg-danger'; // Default class
                                                $statusText = 'QC Pending'; // Default text

                                                // Check conditions for bg-success
                                                $allConditionsMet = true;
                                                if (
                                                    is_null($row["type_produk"]) ||
                                                    is_null($row["produk"]) ||
                                                    is_null($row["chip_id"]) ||
                                                    is_null($row["no_sn"]) ||
                                                    is_null($row["nama_client"]) ||
                                                    is_null($row["garansi_awal"]) ||
                                                    is_null($row["garansi_akhir"]) ||
                                                    is_null($row["garansi_void"]) ||
                                                    is_null($row["keterangan_void"]) ||
                                                    is_null($row["ip_address"]) ||
                                                    is_null($row["mac_wifi"]) ||
                                                    is_null($row["mac_bluetooth"]) ||
                                                    is_null($row["firmware_version"]) ||
                                                    is_null($row["hardware_version"]) ||
                                                    is_null($row["free_ram"]) ||
                                                    is_null($row["min_ram"]) ||
                                                    is_null($row["batt_low"]) ||
                                                    is_null($row["batt_high"]) ||
                                                    is_null($row["temperature"]) ||
                                                    is_null($row["status_error"]) ||
                                                    is_null($row["gps_latitude"]) ||
                                                    is_null($row["gps_longitude"]) ||
                                                    is_null($row["status_qc_sensor_1"]) ||
                                                    is_null($row["status_qc_sensor_2"]) ||
                                                    is_null($row["status_qc_sensor_3"]) ||
                                                    is_null($row["status_qc_sensor_4"]) ||
                                                    is_null($row["status_qc_sensor_5"]) ||
                                                    is_null($row["status_qc_sensor_6"])
                                                ) {
                                                    $allConditionsMet = false;
                                                }

                                                if ($allConditionsMet) {
                                                    $statusClass = 'badge bg-success';
                                                    $statusText = 'QC Passed';
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo $row["id"]; ?></td>
                                                    <td><?php echo $row["chip_id"]; ?></td>
                                                    <td><?php echo $row["no_sn"]; ?></td>
                                                    <td><?php echo $row["produk"] ?></td>
                                                    <td><?php echo $resultLastOnline ?></td>
                                                    <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                                    <td class="text-center">
                                                        <div class="row">
                                                            <div class="col">
                                                                <a href='edit/edit.php?id=<?php echo $row["id"]; ?>' class="btn btn-info btn-block text-center">Edit</a>
                                                            </div>
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
            var table = $('#tableInventaris').DataTable({
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
                        title: 'Quality Control Produk',
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
                        title: 'Quality Control Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Quality Control Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'Quality Control Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Quality Control Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                ],
                order: [0, 'desc'],
            });

            table.buttons().container()
                .appendTo('wrapper .col-md-6:eq(0)');

            $("table.order-list").on("click", ".ibtnEdit", function(event) {
                var idToEdit = 123;

                // Lakukan redirect dengan menyertakan ID sebagai parameter
                window.location.href = 'edit/edit.php?id=' + idToEdit;
            });

        });
    </script>

</body>

</html>