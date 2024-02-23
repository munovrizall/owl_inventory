<?php

include "connection.php";

$query = "SELECT * FROM historis ORDER BY historis.ID DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Error in query: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Histori Transaksi</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Table with search -->
    <link href="https://cdn.datatables.net/v/bs4/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-print-2.4.2/fh-3.4.0/r-2.5.0/rg-1.4.1/sb-1.6.0/sp-2.2.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        .lebar-kolom1 {
            width: 4%;
        }

        .lebar-kolom2 {
            width: 16%;
        }

        .lebar-kolom3 {
            width: 25%;
        }

        .lebar-kolom4 {
            width: 5%;
        }

        .lebar-kolom5 {
            width: 5%;
        }

        .lebar-kolom6 {
            width: 30%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .lebar-kolom7 {
            width: 15%;
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
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Histori Transaksi</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Histori Transaksi</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

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
                                    <table id="tableTransaksi" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center lebar-kolom1">ID</th>
                                                <th class="text-center lebar-kolom2">User</th>
                                                <th class="text-center lebar-kolom3" style="min-width: 120px">Nama Barang</th>
                                                <th class="text-center lebar-kolom4">Kuantitas</th>
                                                <th class="text-center lebar-kolom5">Aktivitas</th>
                                                <th class="text-center lebar-kolom6">Deskripsi</th>
                                                <th class="text-center lebar-kolom7" style="min-width: 120px">Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $tanggal = date('H:i d-m-Y', strtotime($row["waktu"]));
                                                    // Tentukan kelas badge berdasarkan nilai activity
                                                    $badgeClass = "";
                                                    switch ($row["activity"]) {
                                                        case "Produksi":
                                                            $badgeClass = "badge bg-info";
                                                            if (str_contains($row["quantity"], "-")) {
                                                                $badgeClass = "badge bg-danger";
                                                            }
                                                            break;
                                                        case "Restock":
                                                            $badgeClass = "badge bg-success";
                                                            break;
                                                        case "Maintenance":
                                                            $badgeClass = "badge bg-danger";
                                                            $row["quantity"] = "-" . $row["quantity"];
                                                            break;
                                                        case "Prototype":
                                                            $badgeClass = "badge bg-warning";
                                                            $row["quantity"] = "-" . $row["quantity"];
                                                            break;
                                                        case "Pengiriman":
                                                            $badgeClass = "badge bg-secondary";
                                                            $row["quantity"] = "-" . $row["quantity"];
                                                            break;
                                                        default:
                                                            $badgeClass = "badge bg-secondary";
                                                            break;
                                                    }
                                            ?>
                                                    <tr>
                                                        <td><?php echo $row["ID"]; ?></td>
                                                        <td><?php echo $row["pengguna"]; ?></td>
                                                        <td><?php echo $row["nama_barang"]; ?></td>
                                                        <td><?php echo $row["quantity"]; ?></td>
                                                        <td class="text-center"><span class="<?php echo $badgeClass; ?>"><?php echo $row["activity"]; ?></span></td>
                                                        <td><?php echo $row["deskripsi"]; ?></td>
                                                        <td><?php echo $tanggal; ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo "No rows found in the result set.";
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
    <script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="assets/adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/adminlte/dist/js/adminlte.js"></script>
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


    <script>
        $(document).ready(function() {
            var table = $('#tableTransaksi').DataTable({
                fixedHeader: true,
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
                    'pageLength', 'copy',
                    {
                        extend: 'spacer',
                        style: 'bar',
                        text: 'Export files:'
                    },
                    'csv', 'excel', 'pdf', 'print',
                ],
                order: [],

            });

        });
    </script>



</body>

</html>