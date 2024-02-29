<?php

include "../connection.php";
include "../admin_privilege.php";

$query = "SELECT * FROM user_account";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List User</title>

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
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../assets/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <style>
        .lebar-kolom1 {
            width: 5%;
        }

        .lebar-kolom2 {
            width: 40%;
        }

        .lebar-kolom3 {
            width: 10%;
        }

        .lebar-kolom5 {
            width: 10%;
        }

        .lebar-kolom6 {
            width: 10%;
        }

        .lebar-kolom7 {
            width: 15%;
        }

        .card-padding {
            padding: 10px 20px;
        }

        .align-center {
            display: flex;
            align-items: center;
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
                            <h1>List User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">User</li>
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
                            <div class="card-header align-center">
                                <a href="tambah.php">
                                    <button type="button" class="btn btn-primary btn-block" style="max-width: 200px;">
                                        <i class="fas fa-plus" style="margin-right: 8px;"></i>Tambah User
                                    </button>
                                </a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive card-padding">
                                    <table id="tableUser" class="table table order-list table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center lebar-kolom1">ID</th>
                                                <th class="text-center lebar-kolom2">Nama Lengkap</th>
                                                <th class="text-center lebar-kolom3">Username</th>
                                                <th class="text-center lebar-kolom5">Role</th>
                                                <th class="text-center lebar-kolom6 aksi-column">TTD</th>
                                                <th class="text-center lebar-kolom7 aksi-column">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($result)) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $row["account_id"]; ?></td>
                                                    <td><?php echo $row["nama_lengkap"]; ?></td>
                                                    <td><?php echo $row["username"]; ?></td>
                                                    <td><?php echo $row["role"]; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($row["tanda_tangan"] !== "") {
                                                            // Tampilkan gambar jika tanda_tangan tidak null
                                                            echo '<img src="' . $row["tanda_tangan"] . '" height="40">';
                                                        } else {
                                                            // Tampilkan "-" jika tanda_tangan null
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div style="display: inline-block;">
                                                            <a href='edit.php?id=<?php echo $row["account_id"]; ?>' class="btn btn-info"><i class="fas fa-pencil"></i></a>
                                                        </div>
                                                        <a href='#' class='btn btn-danger delete-btn' data-id='<?php echo $row["account_id"]; ?>'><i class="fas fa-trash"></i></a>
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
    <!-- SweetAlert2 Toast -->
    <script src="../assets/adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>

    <!-- Page specific script -->
    <script>
        $(document).ready(function() {

            $('#tableUser').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');

                // Tampilkan konfirmasi SweetAlert
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data pengguna ini akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika dikonfirmasi, lakukan penghapusan
                        window.location.href = 'delete.php?id=' + userId;
                    }
                });
            });

            var table = $('#tableUser').DataTable({
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
                        title: 'List User',
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
                        title: 'List User',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'List User',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'List User',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'List User',
                        exportOptions: {
                            columns: ':visible:not(.aksi-column)'
                        }
                    },
                ],
                order: [0, 'asc'],
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