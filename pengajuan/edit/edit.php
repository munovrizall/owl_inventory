<?php
include("../../connection.php");
include "../../admin_privilege.php";

$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id'])) {
        $pengajuan_id = $_GET['id'];

        $queryCheck = "SELECT approved FROM pengajuan WHERE pengajuan_id = ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("i", $pengajuan_id);
            $stmtCheck->execute();
            $stmtCheck->bind_result($approved);
            $fetchSuccess = $stmtCheck->fetch();
            $stmtCheck->close();

            if (!$fetchSuccess) {
                die("p");
            }

            if ($approved != 1) {
                die("Pengajuan belum disetujui, tidak bisa update.");
            }

        

        // Ambil data pengajuan_detail
        $queryDetail = "SELECT pd.*, mb.link_foto, mb.link 
            FROM pengajuan_detail pd
            LEFT JOIN masterbahan mb ON pd.stok_id = mb.stok_id
            WHERE pd.pengajuan_id = ?
        ";
        $stmtDetail = $conn->prepare($queryDetail);
        $stmtDetail->bind_param("i", $pengajuan_id);
        $stmtDetail->execute();

        $resultDetail = $stmtDetail->get_result();
    } else {
        echo "ID not provided.";
    }
}

// Ambil no_surat dari pengajuan
$pengajuan_id = $_GET['id'];
$queryPengajuan = "SELECT no_surat, keperluan FROM pengajuan WHERE pengajuan_id = ?";
$stmtPengajuan = $conn->prepare($queryPengajuan);
$stmtPengajuan->bind_param("i", $pengajuan_id);
$stmtPengajuan->execute();
$resultPengajuan = $stmtPengajuan->get_result();
$rowPengajuan = $resultPengajuan->fetch_assoc();

$noSurat = $rowPengajuan['no_surat'];
$keperluan = $rowPengajuan['keperluan'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['submitForm'])) {
        // Nama pengguna
        $pengguna = isset($_SESSION['namaLengkap']) ? $_SESSION['namaLengkap'] : '';

        // Loop melalui setiap data yang diterima
        foreach ($_POST['status'] as $detail_id => $status) {
            // Bersihkan dan validasi input
            $detail_id = intval($detail_id);
            $status = htmlspecialchars($status);

            // Ambil qty real dari form
            $qtyr1 = !empty($_POST['kuantitas_real'][$detail_id][0]) ? htmlspecialchars($_POST['kuantitas_real'][$detail_id][0]) : 0;
            $qtyr2 = (!empty($_POST['kuantitas_real'][$detail_id][1]) && !empty($_POST['additional_links'][$detail_id][0])) ? htmlspecialchars($_POST['kuantitas_real'][$detail_id][1]) : 0;
            $qtyr3 = (!empty($_POST['kuantitas_real'][$detail_id][2]) && !empty($_POST['additional_links'][$detail_id][1])) ? htmlspecialchars($_POST['kuantitas_real'][$detail_id][2]) : 0;

            $total_qty = $qtyr1 + $qtyr2 + $qtyr3;
            
            // Ambil link tambahan dari form
            $link2 = !empty($_POST['additional_links'][$detail_id][0]) ? htmlspecialchars($_POST['additional_links'][$detail_id][0]) : null;
            $link3 = !empty($_POST['additional_links'][$detail_id][1]) ? htmlspecialchars($_POST['additional_links'][$detail_id][1]) : null;

            // Ambil status sebelumnya dari database
            $queryPreviousStatus = "SELECT status, stok_id, nama_barang FROM pengajuan_detail WHERE detail_id = ?";
            $stmtPrev = $conn->prepare($queryPreviousStatus);
            $stmtPrev->bind_param("i", $detail_id);
            $stmtPrev->execute();
            $resultPrev = $stmtPrev->get_result();
            $rowPrev = $resultPrev->fetch_assoc();


            // Jika status sebelumnya bukan "selesai" dan sekarang jadi "selesai"
            if ($rowPrev['status'] !== 'selesai' && $status === 'selesai') {
                $stok_id = $rowPrev['stok_id'];
                $namaBarang = $rowPrev['nama_barang'];

                // Update kuantitas di tabel masterbahan
                if ($keperluan === 'PRODUKSI') {
                    $updateMasterQuery = "UPDATE masterbahan SET quantity = quantity + ? WHERE stok_id = ?";
                    $stmtMaster = $conn->prepare($updateMasterQuery);
                    $stmtMaster->bind_param("ii", $total_qty, $stok_id);
                    $stmtMaster->execute();
                }
                
                // Masukkan data ke tabel historis
                $insertHistorisQuery = "INSERT INTO historis (pengguna, nama_barang, waktu, quantity, activity, deskripsi) VALUES (?, ?, NOW(), ?, 'Restock', ?)";
                $stmtHistoris = $conn->prepare($insertHistorisQuery);
                $stmtHistoris->bind_param("ssis", $pengguna, $namaBarang, $total_qty, $noSurat);
                if (!$stmtHistoris->execute()) {
                    throw new Exception("Gagal menyimpan ke tabel historis: " . $stmtHistoris->error);
                }
                
            }
            
            // Siapkan query untuk memperbarui status
            $updateQuery = "UPDATE pengajuan_detail SET status = ?, qtyr1 = ?, qtyr2 = ?, qtyr3 = ?, link2 = ?, link3 = ? WHERE detail_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("siiissi", $status, $qtyr1, $qtyr2, $qtyr3, $link2, $link3, $detail_id);

            // Eksekusi query
            $stmt->execute();

            if (!$stmt->execute()) {
                echo "Error updating: " . $stmt->error;
                die();
            }
        }

        // Redirect setelah data disimpan
        header("Location: ../monitoring_pengajuan.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Transaksi</title>

    <link rel="icon" href="../../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <style>
        .lebar-kolom1 {
            width: 30%;
        }

        .lebar-kolom2-1 {
            width: 7%;
        }

        .lebar-kolom2-2 {
            width: 8%;
        }

        .lebar-kolom3 {
            width: 9%;
        }

        .lebar-kolom4 {
            width: 6%;
        }

        .lebar-kolom5 {
            width: 13%;
        }

        .lebar-kolom6 {
            width: 27%;
        }

        th {
            text-align: center;
            vertical-align: middle;
            vertical-align: middle !important;
            padding-top: 5px !important;
            padding-bottom: 5px !important;
        }

        td {
            vertical-align: middle !important;
            /* line-height: 30px; */
        }

        .form-control {
            height: 2rem;
        }

        .sub-header {
            font-size: 12px;
            color: #888;
        }

        .harga-wrapper {
            display: flex;
            justify-content: space-between;
        }   
        
        .harga {
            text-align: right;
        }

        td i.fas.fa-image {
            font-size: 1.5em;
        }

        .link-row {
            margin-bottom: 5px;
            max-width: 240px;
            align-items: center;
        }

        .btn-sm {
            font-size: 15px;
            height: 25px;
            width: 25px;
            display: flex;
            justify-content: center;
        }

        .status-cols {
                padding-left: 0 !important;
                padding-right: 0 !important;
        }

        .radio-wrapper {
            display: inline-block;
            text-align: center;
            margin-left: 5px;
            margin-right: 5px;
        }

        .card-body.p-0 .table thead > tr > th:first-of-type,
        .card-body.p-0 .table tbody > tr > td:first-of-type,
        .card-body.p-0 .table tfoot > tr > td:first-of-type {
            padding-left: 0.75rem !important;
        }

        .card-body.p-0 .table thead > tr > th:last-of-type,
        .card-body.p-0 .table tfoot > tr > td:last-of-type {
            padding-right: 0.75rem !important;
        }

        .card-body.p-0 .table tbody > tr > td:last-of-type {
            padding-right: 0 !important;
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
                            <h1>Monitoring Pembelian</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Pengajuan</li>
                                <li class="breadcrumb-item active"><a href="../monitoring.php">Monitoring</a></li>
                                <li class="breadcrumb-item active">Edit</li>
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
                            <?php
                                echo "<h3 class='card-title'>No. Surat: {$noSurat}</h3>";
                            ?>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="monitoringForm" method="post">
                            <div class="card-body">
                                <div class="card">
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="lebar-kolom1" style="min-width:140px;" rowspan=2>Nama</th>
                                                        <th  colspan=2>
                                                            QTY
                                                        </th>
                                                        <th class="lebar-kolom3" rowspan=2 style="min-width:90px;">Harga</th>
                                                        <th class="lebar-kolom4" rowspan=2>Foto</th>
                                                        <th class="lebar-kolom5" rowspan=2>Link</th>
                                                        <th class="lebar-kolom6" rowspan=2 style="min-width:290px;">Status</th>

                                                    </tr>
                                                    <tr class="sub-header">
                                                        <th class="lebar-kolom2-1">Req</th> <!-- QTY yang diajukan -->
                                                        <th class="lebar-kolom2-2">Real</th> <!-- QTY yang direalisasikan -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($resultDetail->num_rows > 0): ?>
                                                        <?php while ($row = $resultDetail->fetch_assoc()): ?>
                                                            <?php 
                                                            $detailId = htmlspecialchars($row['detail_id']);
                                                            $namaBarang = htmlspecialchars($row['nama_barang']);
                                                            $kuantitas = htmlspecialchars($row['kuantitas']);
                                                            $kuantitasReal = htmlspecialchars($row['qtyr1']);
                                                            $harga = htmlspecialchars($row['harga']);
                                                            $hargaFormatted = number_format($harga, 0, ',', '.');
                                                            $linkFoto = htmlspecialchars($row['link_foto'] ?? '');
                                                            $link = htmlspecialchars($row['link'] ?? '');
                                                            $link2 = htmlspecialchars($row['link2'] ?? '');
                                                            $link3 = htmlspecialchars($row['link3'] ?? '');
                                                            $status = htmlspecialchars($row['status'] ?? '');
                                                            ?>
                                                            <tr data-detail-id="<?= $detailId ?>">
                                                                <td>
                                                                    <?= $namaBarang ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?= $kuantitas ?>
                                                                </td>
                                                                <td class="qty-column">
                                                                    <!-- QTY real default dari database -->
                                                                    <div class="text-center">
                                                                        <input type="text" name="kuantitas_real[<?= $detailId ?>][]" value="<?= htmlspecialchars($kuantitasReal) ?>" class="form-control text-center">
                                                                    </div>
                                                                    <!-- QTY real default dari database -->
                                                                    <div class="qty-extra">
                                                                        <?php if (!empty($row['link2'])): ?>
                                                                            <div class="qty-row">
                                                                                <input type="text" name="kuantitas_real[<?= $detailId ?>][]" value="<?= htmlspecialchars($row['qtyr2']) ?>" class="form-control text-center">
                                                                            </div>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($row['link3'])): ?>
                                                                            <div class="qty-row">
                                                                                <input type="text" name="kuantitas_real[<?= $detailId ?>][]" value="<?= htmlspecialchars($row['qtyr3']) ?>" class="form-control text-center">
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                                <td class="harga">
                                                                    <?= $hargaFormatted ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="<?= $linkFoto ?>" target="_blank">
                                                                        <!-- Lihat Foto -->
                                                                        <i class="fas fa-image"></i>
                                                                    </a>
                                                                </td>
                                                                <td class="url-column">
                                                                    <!-- Link default dari database -->
                                                                    <div class="link-row d-flex align-items-center">
                                                                        <a href="<?= $link ?>" target="_blank" class="flex-grow-1 text-truncate"><?= $link ?></a>
                                                                        <button type="button" class="btn btn-sm btn-secondary add-link-btn ml-2" title="Tambah Link">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </div>

                                                                    <!-- Container untuk link tambahan -->
                                                                    <div class="dynamic-links mt-2">
                                                                        <?php if (!empty($row['link2'])): ?>
                                                                            <div class="link-row d-flex align-items-center">
                                                                                <input type="url" name="additional_links[<?= $detailId ?>][]" value="<?= htmlspecialchars($row['link2']) ?>" class="form-control flex-grow-1">
                                                                                <button type="button" class="btn btn-sm btn-danger ml-2 remove-link-btn" title="Hapus Link">
                                                                                    <i class="fas fa-minus"></i>
                                                                                </button>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <?php if (!empty($row['link3'])): ?>
                                                                            <div class="link-row d-flex align-items-center">
                                                                                <input type="url" name="additional_links[<?= $detailId ?>][]" value="<?= htmlspecialchars($row['link3']) ?>" class="form-control flex-grow-1">
                                                                                <button type="button" class="btn btn-sm btn-danger ml-2 remove-link-btn" title="Hapus Link">
                                                                                    <i class="fas fa-minus"></i>
                                                                                </button>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center status-cols">
                                                                    <div class="radio-wrapper">
                                                                        <input type="hidden" name="status[<?= $detailId ?>]" value="pending">
                                                                        <input type="radio" name="status[<?= $detailId ?>]" value="dibeli" 
                                                                            <?= isset($status) && $status == 'dibeli' ? 'checked' : '' ?> 
                                                                            <?= isset($status) && in_array($status, ['dikirim', 'sampai', 'selesai', 'cancel']) ? 'disabled' : '' ?>>
                                                                        <div>Dibeli</div>
                                                                    </div>
                                                                    <div class="radio-wrapper">
                                                                        <input type="radio" name="status[<?= $detailId ?>]" value="dikirim" 
                                                                            <?= isset($status) && $status == 'dikirim' ? 'checked' : '' ?> 
                                                                            <?= isset($status) && in_array($status, ['sampai', 'selesai', 'cancel']) ? 'disabled' : '' ?>>
                                                                        <div>Dikirim</div>
                                                                    </div>
                                                                    <div class="radio-wrapper">
                                                                        <input type="radio" name="status[<?= $detailId ?>]" value="sampai" 
                                                                            <?= isset($status) && $status == 'sampai' ? 'checked' : '' ?> 
                                                                            <?= isset($status) && in_array($status, ['selesai', 'cancel']) ? 'disabled' : '' ?>>
                                                                        <div>Sampai</div>
                                                                    </div>
                                                                    <div class="radio-wrapper">
                                                                        <input type="radio" name="status[<?= $detailId ?>]" value="selesai" 
                                                                            <?= isset($status) && $status == 'selesai' ? 'checked' : '' ?>
                                                                            <?= isset($status) && $status == 'cancel' ? 'disabled' : '' ?>>
                                                                        <div>selesai</div>
                                                                    </div>
                                                                    <div class="radio-wrapper">
                                                                        <input type="radio" name="status[<?= $detailId ?>]" value="cancel" 
                                                                            <?= isset($status) && $status == 'cancel' ? 'checked' : '' ?>
                                                                            <?= isset($status) && $status == 'selesai' ? 'disabled' : '' ?>>
                                                                        <div>cancel</div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="4">Tidak ada data.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" name="submitForm">Submit</button>
                            </div>
                        </form>

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
    <script src="../../assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="../../assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../assets/adminlte/dist/js/adminlte.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(document).ready(function () {
            // Fungsi untuk menambah baris link
            function addLinkRow(button) {
                var row = $(button).closest('tr');
                var detailId = row.data('detail-id');

                var urlContainer = $(button).closest('td').find('.dynamic-links');
                var urlRow = urlContainer.find(".link-row");
                var qtyContainer = $(button).closest('tr').find('.qty-extra');
                var qtyRow = qtyContainer.find(".qty-row");

                var urlRowCount = urlContainer.find(".link-row").length;

                if (urlRowCount < 2) {
                    let urlRow = $(`
                        <div class="link-row d-flex align-items-center">
                            <input type="url" name="additional_links[${detailId}][]" class="form-control flex-grow-1" placeholder="Link Tambahan">
                            <button type="button" class="btn btn-sm btn-danger ml-2 remove-link-btn" title="Hapus Link">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    `);
                    urlContainer.append(urlRow);

                    let qtyRow = $(`
                        <div class="qty-row">
                            <input type="text" name="kuantitas_real[${detailId}][]" value="0" class="form-control text-center">
                        </div>
                    `);
                    qtyContainer.append(qtyRow);

                    // Disable tombol tambah jika sudah ada 2 link tambahan
                    if (urlRowCount >= 2) {
                        $(button).prop('disabled', true);
                    }
                }
            }

            // Event listener tombol tambah link
            $('.add-link-btn').on('click', function () {
                addLinkRow(this);
            });

            // Event listener tombol hapus link (buat link yang sudah ada dari database)
            $(document).on('click', '.remove-link-btn', function() {
                var index = $(this).closest('.link-row').index();
                var urlContainer = $(this).closest('.dynamic-links');
                var row = $(this).closest('tr');
                var qtyContainer = row.find('.qty-extra');
                
                $(this).closest('.link-row').remove();
                qtyContainer.find('.qty-row').eq(index).remove();

                if (urlContainer.find('.link-row').length < 2) {
                    urlContainer.closest('td').find('.add-link-btn').prop('disabled', false);
                }
            });

            // Cek jumlah link saat halaman dimuat
            $('.dynamic-links').each(function () {
                var urlContainer = $(this);
                var addButton = urlContainer.closest('td').find('.add-link-btn');

                if (urlContainer.find('.link-row').length >= 2) {
                    addButton.prop('disabled', true);
                }
            });

            $("table.table-striped").on("input", "input[name^='kuantitas_real'][name$='[]']", function () {
                var input = $(this);
                var cleanValue = input.val().replace(/[^0-9]/g, ""); // Ambil cuma angka 0-9
                input.val(cleanValue); // Update input biar cuma angka doang
            });


        });
    </script>

</body>
</html>