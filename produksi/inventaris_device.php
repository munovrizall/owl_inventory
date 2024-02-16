<?php

include "../connection.php";

$query = "SELECT * FROM inventaris_produk WHERE 
           id IS NOT NULL AND 
           type_produk IS NOT NULL AND 
           produk IS NOT NULL AND 
           chip_id IS NOT NULL AND 
           no_sn IS NOT NULL AND 
           nama_client IS NOT NULL AND 
           garansi_awal IS NOT NULL AND 
           garansi_akhir IS NOT NULL AND 
           garansi_void IS NOT NULL AND 
           keterangan_void IS NOT NULL AND 
           ip_address IS NOT NULL AND 
           mac_wifi IS NOT NULL AND 
           mac_bluetooth IS NOT NULL AND 
           firmware_version IS NOT NULL AND 
           hardware_version IS NOT NULL AND 
           free_ram IS NOT NULL AND 
           min_ram IS NOT NULL AND 
           batt_low IS NOT NULL AND 
           batt_high IS NOT NULL AND 
           temperature IS NOT NULL AND 
           status_error IS NOT NULL AND 
           gps_latitude IS NOT NULL AND 
           gps_longitude IS NOT NULL AND 
           status_qc_sensor_1 IS NOT NULL AND 
           status_qc_sensor_2 IS NOT NULL AND 
           status_qc_sensor_3 IS NOT NULL AND 
           status_qc_sensor_4 IS NOT NULL AND 
           status_qc_sensor_5 IS NOT NULL AND 
           status_qc_sensor_6 IS NOT NULL
           ORDER BY id";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventaris Device</title>

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
            width: 5%;
        }

        .lebar-kolom2 {
            width: 10%;
        }

        .lebar-kolom3 {
            width: 20%;
        }

        .lebar-kolom4 {
            width: 10%;
        }


        .lebar-kolom5 {
            width: 20%;
        }

        .lebar-kolom6 {
            width: 10%;
        }

        .lebar-kolom7 {
            width: 25%;
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
                            <h1>Inventaris Device</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Produksi</li>
                                <li class="breadcrumb-item active">Inventaris Device</li>
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
                                                <th class="text-center lebar-kolom2">Nomor SN</th>
                                                <th class="text-center lebar-kolom3" style="min-width: 100px">Produk</th>
                                                <th class="text-center lebar-kolom4">Perusahaan</th>
                                                <th class="text-center lebar-kolom5">Terakhir Online</th>
                                                <th class="text-center lebar-kolom6">Garansi</th>
                                                <th class="text-center lebar-kolom7 aksi-column" style="min-width:140px;">Aksi</th>
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

                                                $statusClass = 'bg-danger'; // Default class
                                                $statusText = 'Tidak'; // Default text

                                                // Check if any row is missing and set default values
                                                if (
                                                    empty($row["garansi_akhir"]) // Check if warranty expiry date is missing
                                                    // Add similar checks for other rows as needed
                                                ) {
                                                    $statusClass = '-';
                                                    $statusText = '-';
                                                } else {
                                                    // Check conditions for bg-success
                                                    $allConditionsMet = true;
                                                    if (strtotime($row["garansi_akhir"]) < strtotime('today') || $row["garansi_void"] == 1) { // Check if warranty expiry date is before today
                                                        $allConditionsMet = false;
                                                    }

                                                    if ($allConditionsMet) {
                                                        $statusClass = 'bg-success';
                                                        $statusText = 'Ya';
                                                    }
                                                }

                                                // Output table row with appropriate status class and text
                                            ?>
                                                <tr>
                                                    <td><?php echo $row["id"]; ?></td>
                                                    <td><?php echo $row["no_sn"]; ?></td>
                                                    <td><?php echo $row["produk"] ?></td>
                                                    <td><?php echo !empty($row["nama_client"]) ? $row["nama_client"] : '-' ?></td>
                                                    <td><?php echo $resultLastOnline ?></td>
                                                    <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                                    <td class="text-center">
                                                        <div class="row">
                                                            <div class="col">
                                                                <a href='detail/detail.php?id=<?php echo $row["id"]; ?>' class="btn btn-info btn-block" ">Detail</a>
                                                            </div>
                                                            <div class="col">
                                                                <button class="btn btn-block btn-outline-info" data-id="<?php echo $row['id']; ?>" id="downloadBarcode" >
                                                                    <i class="fas fa-barcode"></i>
                                                                </button>
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
                        title: 'Monitoring Inventaris Produk',
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
                        title: 'Monitoring Inventaris Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Monitoring Inventaris Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'Monitoring Inventaris Produk',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Monitoring Inventaris Produk',
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
            
            $('#tableInventaris').on('click', '#downloadBarcode', function() {
                // Get the data-id attribute value from the clicked button
                var rowId = $(this).data('id');

                var xhr = new XMLHttpRequest();
                xhr.open("GET", "generate_barcode/barcode.php?id=" + rowId);
                xhr.responseType = "blob";

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var url = window.URL.createObjectURL(xhr.response);
                        var link = document.createElement("a");
                        link.href = url;
                        link.download = "combined_barcodes.png";
                        document.body.appendChild(link);
                        link.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(link);
                    }
                };

                xhr.send();
            });
        });
    </script>

</body>

</html>