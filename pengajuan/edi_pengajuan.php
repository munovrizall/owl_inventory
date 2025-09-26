<?php
include("../connection.php");
include "../admin_privilege.php";


if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $pengajuan_id = $_GET['id'];

        // Cek ID
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

        if ($approved != 0) {
            die("Pengajuan sudah disetujui, tidak bisa diedit.");
        }

        // Ambil data dari pengajuan
        $queryPengajuan = "SELECT no_surat, judul_pengajuan, unit, keperluan, project, batch, keterangan, pengaju, penyetuju, grand_total FROM pengajuan WHERE pengajuan_id = ?";
        $stmtPengajuan = $conn->prepare($queryPengajuan);
        $stmtPengajuan->bind_param("i", $pengajuan_id);
        $stmtPengajuan->execute();
        $resultPengajuan = $stmtPengajuan->get_result();
        $rowPengajuan = $resultPengajuan->fetch_assoc();

        // Ambil data pengajuan_detail
        $queryDetail = "SELECT stok_id, kuantitas, harga, keterangan FROM pengajuan_detail WHERE pengajuan_id = ?";
        $stmtDetail = $conn->prepare($queryDetail);
        $stmtDetail->bind_param("i", $pengajuan_id);
        $stmtDetail->execute();
        $resultDetail = $stmtDetail->get_result();

        // Nama
        $queryNama = "SELECT nama_lengkap FROM user_account";
    } else {
        die("ID tidak diberikan di URL.");
    }
}



// Update db
if ($_SERVER['REQUEST_METHOD']=== 'POST') {
    // === Ambil data input dari frontend ===
    $pengajuan_id = $_POST['pengajuan_id'] ?? null;
    // Data tabel Pengajuan
    $noSurat = $_POST['input_nomor_surat'] ?? null;
    $judulPengajuan = trim(strtoupper($_POST['judul_pengajuan'] ?? null));
    $unit = $_POST['unit']  ?? null;
    $keperluanSurat = $_POST['keperluan_surat']  ?? null;
    $project = $_POST['project']  ?? null;
    $batch = $_POST['batch']  ?? null;
    $keterangan = $_POST['keterangan']  ?? null;
    $diajukanOleh = $_POST['diajukan_oleh']  ?? null;
    $disetujuiOleh = $_POST['disetujui_oleh']  ?? null;

    // No surat baru
    // $no_surat_lama = $rowPengajuan['no_surat'];
    // $no_surat_baru = preg_replace('/^(\d+\/)([^\/]+)(\/RND\/\d{2}-\d{4})$/', "$1$project$3", $no_surat_lama);

    // Data tabel pengajuan_detail
    $barang = $_POST['id_barang']  ?? null; // Array ID barang
    $namaBarang = $_POST['nama_barang']  ?? null; // Array nama barang
    $kuantitas = $_POST['kuantitas']  ?? null; // Array kuantitas barang
    $harga = $_POST['harga']  ?? null; // Array harga barang
    $keteranganDetail = $_POST['keterangan_detail']  ?? null; // Array keterangan per barang
    
    try {
        // === VALIDASI DATA BACKEND ===
        if (empty($judulPengajuan) || empty($unit) || empty($keperluanSurat) || empty($project) || empty($diajukanOleh) || empty($disetujuiOleh)) {
            throw new Exception("Harap lengkapi semua data!");

        }

        foreach ($namaBarang as $key => $stokId) {
            $qty = $kuantitas[$key] ?? null;
            $price = $harga[$key] ?? null;

            if (empty($namaBarang) || !is_numeric($qty) || $qty <= 0 || !is_numeric($price) || $price <= 0) {
                throw new Exception("Harap lengkapi semua data!");
            }
        }

        // === TRANSAKSI DATABASE ===
        $conn->begin_transaction();

        // Simpan update pengajuan
        $queryUpdate = "UPDATE pengajuan SET no_surat = ?, judul_pengajuan = ?, unit = ?, keperluan = ?, project = ?, batch = ?, keterangan = ?, pengaju = ?, penyetuju = ? WHERE pengajuan_id = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bind_param("sssssssssi", $noSurat, $judulPengajuan, $unit, $keperluanSurat, $project, $batch, $keterangan, $diajukanOleh, $disetujuiOleh, $pengajuan_id);
        $stmtUpdate->execute();
        /* validasi di matikan dulu
        if (!$stmtUpdate->execute()) {
            throw new Exception("Gagal update: " . $stmtUpdate->error);
        } else if ($stmtUpdate->affected_rows === 0) {
            http_response_code(400);
            throw new Exception("Error: pengajuan_id tidak valid!");
        }
        */

        // Hapus semua detail lama
        $queryDelete = "DELETE FROM pengajuan_detail WHERE pengajuan_id = ?";
        $stmtDelete = $conn->prepare($queryDelete);
        $stmtDelete->bind_param("i", $pengajuan_id);
        if (!$stmtDelete->execute()) {
            throw new Exception("Gagal hapus: " . $stmtDelete->error);
        }

        // Simpan detail pengajuan
        // Simpan detail pengajuan
        $queryInsertDetail = "INSERT INTO pengajuan_detail (pengajuan_id, stok_id, nama_barang, kuantitas, qtyr1, harga, total_harga, keterangan)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtDetail = $conn->prepare($queryInsertDetail);

        $grandTotal = 0;
        foreach ($barang as $key => $stokId) {
            
            $nama = $namaBarang[$key];
            $qty = $kuantitas[$key];
            $price = $harga[$key];
            $totalHarga = $qty * $price;
            $grandTotal += $totalHarga;
            $keteranganBarang = isset($keteranganDetail[$key]) ? $keteranganDetail[$key] : '';
            $stmtDetail->bind_param("iisiiiis", $pengajuanId, $stokId, $nama, $qty, $qty, $price, $totalHarga, $keteranganBarang);
            if (!$stmtDetail->execute()) {
                throw new Exception("Gagal menyimpan detail pengajuan pada baris ke-" . ($key + 1) . ": " . $stmtDetail->error);
            }
        }

        // Update grand total di tabel pengajuan
        $queryUpdateGrandTotal = "UPDATE pengajuan SET grand_total = ? WHERE pengajuan_id = ?";
        $stmtUpdateGrandTotal = $conn->prepare($queryUpdateGrandTotal);
        $stmtUpdateGrandTotal->bind_param("ii", $grandTotal, $pengajuan_id);
        $stmtUpdateGrandTotal->execute();

        $conn->commit();
            
        echo "Pengajuan berhasil diperbarui!";
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code($e->getCode());
        // http_response_code(500);
        echo $e->getMessage();
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pengajuan</title>

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

        textarea.table-input {
            overflow: hidden;
        }

        .total-harga-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 7px;
            /* align-items: center; */
        }   
        
        .total-harga {
            text-align: right; /* Rata kanan */
            max-width: 120px;
        }

        .lebar-kolom0 {
            width: 3%;
        }

        .lebar-kolom1 {
            width: 30%;
        }

        .lebar-kolom2 {
            width: 10%;
        }

        .lebar-kolom3 {
            width: 10%;
        }

        .lebar-kolom4 {
            width: 15%;
        }

        .lebar-kolom5 {
            width: 22%;
        }

        .lebar-kolom6 {
            width: 10%;
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
                            <h1>Edit Pengajuan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Pengajuan</a></li>
                                <li class="breadcrumb-item active">Buat Pengajuan</li>
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
                            <h3 class="card-title">List Pengajuan</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="inputPengajuanForm">
                            <div class="card-body">
                                <!--  -->
                                <div class="col">
                                    <div class="form-group">
                                    <input type="hidden" name="pengajuan_id" value="<?= htmlspecialchars($pengajuan_id) ?>">
                                        <p style="font-weight: bold; font-size: 18px;">
                                            No. Surat: <span id="nomor_surat"><?php echo($rowPengajuan['no_surat']) ?></span>
                                        </p>
                                        <input type="hidden" name="input_nomor_surat" id="input_nomor_surat" value="<?php echo($rowPengajuan['no_surat']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="judul_pengajuan">Judul Pengajuan <span style="color: red;">*</span></label>
                                        <input 
                                            type="text" class="form-control" id="judul_pengajuan" name="judul_pengajuan" value="<?php echo htmlspecialchars($rowPengajuan['judul_pengajuan']); ?>" placeholder="Masukkan judul pengajuan" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="unit">Unit <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="unit" name="unit" value="<?php echo htmlspecialchars($rowPengajuan['unit']) ?>" placeholder="Masukkan unit" required />
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="keperluan_surat">Keperluan Surat <span style="color: red;">*</span></label>
                                            <select class="form-control select2" data-minimum-results-for-search="Infinity" id="keperluan_surat" name="keperluan_surat" required>
                                                <option value="" disabled selected>Pilih Keperluan</option>
                                                <option value="RISET">Riset</option>
                                                <option value="PRODUKSI">Produksi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="project">Project <span style="color: red;">*</span></label>
                                            <select class="form-control select2" data-minimum-results-for-search="Infinity" id="project" name="project" data-selected="<?php $rowPengajuan['project'] ?>" required>
                                                <!-- <option value="" disabled selected>Pilih Keperluan Terlebih Dahulu</option> -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="batch">Batch</label>
                                        <input type="text" class="form-control" id="batch" name="batch" value="<?php echo htmlspecialchars($rowPengajuan['batch']) ?>" placeholder="Masukkan batch" />
                                    </div>

                                    <div class="form-group">
                                        <label for="keterangan">Keterangan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?php echo htmlspecialchars($rowPengajuan['keterangan']) ?>" placeholder="Masukkan keterangan" required>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="diajukan_oleh">Diajukan Oleh <span style="color: red;">*</span></label>
                                            <select class="form-control select2" data-minimum-results-for-search="Infinity" id="diajukan_oleh" name="diajukan_oleh" required>
                                                <option value="" disabled selected>Pilih Pengaju</option>
                                                <?php                                                
                                                $result = mysqli_query($conn, $queryNama);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo '<option value="' . htmlspecialchars($row['nama_lengkap']) . '">' . htmlspecialchars($row['nama_lengkap']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="disetujui_oleh">Disetujui Oleh <span style="color: red;">*</span></label>
                                            <select class="form-control select2" data-minimum-results-for-search="Infinity" id="disetujui_oleh" name="disetujui_oleh" required>
                                                <option value="" disabled selected>Pilih Pemberi Persetujuan</option>
                                                <?php
                                                $result = mysqli_query($conn, $queryNama); // Pake query yang sama buat dropdown kedua
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo '<option value="' . htmlspecialchars($row['nama_lengkap']) . '">' . htmlspecialchars($row['nama_lengkap']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <!--  -->
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="tableDetail" class=" table order-list table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center lebar-kolom0"></td>
                                                    <th class="text-center lebar-kolom1" style="min-width:160px;">Nama Barang <span style="color: red;">*</span></td>
                                                    <th class="text-center lebar-kolom2" style="min-width:75px;">Qty <span style="color: red;">*</span></td>
                                                    <th class="text-center lebar-kolom3" style="min-width:120px;">Harga <span style="color: red;">*</span></td>
                                                    <th class="text-center lebar-kolom4" style="min-width:140px;">Total Harga </td>
                                                    <th class="text-center lebar-kolom5" style="min-width:140px;">Keterangan</td>
                                                    <th class="text-center lebar-kolom6"><b>Aksi</b></td>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($row = $resultDetail->fetch_assoc()) : ?>
                                                    <tr>
                                                        <td class="row-number text-center"></td>
                                                        <td>
                                                            <select class="form-control select2 id-barang" name="id_barang[]" data-default="<?= $row['stok_id']; ?>" data-harga-db="<?= $row['harga']; ?>">
                                                                <option selected disabled>Loading...</option>
                                                            </select>
                                                            <input type="hidden" name="nama_barang[]" value="">
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control table-input" name="kuantitas[]" rows="1" style="resize: none;" placeholder="0" required><?= $row['kuantitas']; ?></textarea>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control table-input" name="harga[]" rows="1" style="resize: none;" placeholder="0" required><?= $row['harga']; ?></textarea>
                                                        </td>
                                                        <td>
                                                            <div class="total-harga-wrapper">
                                                                <span class="prefix">Rp.</span>
                                                                <span class="total-harga"><?= number_format($row['kuantitas'] * $row['harga'], 0, ',', '.'); ?></span>
                                                                <input type="hidden" name="total_harga[]" value="<?= $row['kuantitas'] * $row['harga']; ?>">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control table-input" name="keterangan_detail[]" rows="1" style="resize: none;" placeholder="Masukkan Keterangan"><?= htmlspecialchars($row['keterangan']); ?></textarea>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm delete-row">Hapus</button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>

                                            <tfoot>
                                            <tr>
                                                <td colspan="4" style="text-align: right; font-weight: bold;">Grand Total:</td>
                                                <td colspan="1">
                                                    <div class="grand-total-wrapper">
                                                        <span class="prefix" style="font-weight: bold;">Rp.</span>
                                                        <span class="total-harga" id="grandtotal" style="font-weight: bold; float: right;">0</span>
                                                        <input type="hidden" name="grand_total" id="grand_total_input" value="0">
                                                    </div>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: left;">
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
                            <button type="button" class="btn btn-primary" onclick="if(validateForm()) { validateSuccess();}">Submit</button>
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

            calculateGrandTotal();
            updateDropdownDB();
            updateRowNumbers();
            generateNomorSurat();

            capitalizeText("#unit");
            capitalizeText("#batch");
            capitalizeText("#keterangan");
            capitalizeText("textarea[name='keterangan_detail[]']");

            
            $("#judul_pengajuan").on("input", function () {
                let posisi = this.selectionStart; // Simpan posisi kursor awal
                let oldLength = $(this).val().length; // Panjang sebelum edit

                let valueBaru = $(this).val()
                    .trimStart() // Hapus spasi di awal
                    .replace(/\s+/g, ' ') // Ganti spasi ganda jadi 1 spasi aja
                    .toUpperCase(); // Ubah ke uppercase
                
                $(this).val(valueBaru); // Set value baru

                let newLength = valueBaru.length; // Panjang setelah edit
                let perubahanPanjang = oldLength - newLength; // Selisih panjang teks

                let posisiBaru = Math.max(posisi - perubahanPanjang, 0); // Hitung posisi kursor baru
                this.setSelectionRange(posisiBaru, posisiBaru); // Set ulang posisi kursor
            });

            $('#judul_pengajuan').on('blur', function() {
                $(this).val($.trim($(this).val()));
            });

            $('#project, #keperluan_surat').on('change', generateNomorSurat);
            
            $('#keperluan_surat').val("<?= $rowPengajuan['keperluan'] ?>").trigger('change');
            
            $('#keperluan_surat').on('input', function () {
                let projectDropdown = document.getElementById("project");
                projectDropdown.disabled = false; // Aktifkan dropdown kedua
                projectDropdown.innerHTML = ""; // Hapus opsi lama

                // Tambahkan opsi default
                let defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.disabled = true;
                defaultOption.selected = true;
                
                projectDropdown.appendChild(defaultOption);

                // Tentukan opsi berdasarkan pilihan pertama
                let options = [];
                if (this.value === "RISET") {
                    defaultOption.textContent = "Pilih Detail Riset";
                    options = [
                        { value: "1", text: "1" },
                        { value: "2", text: "2" },
                        { value: "3", text: "3" }
                    ];
                } else if (this.value === "PRODUKSI") {
                    defaultOption.textContent = "Pilih Detail Produksi";
                    options = [
                        { value: "PRODUKSIFINGERPRINT", text: "Finger Print" },
                        { value: "PRODUKSIODSO", text: "ODSO" },
                        { value: "PRODUKSIOMBRO", text: "OMBRO" }
                    ];
                }

                // Tambahkan opsi ke dropdown kedua
                options.forEach(optionData => {
                    let option = document.createElement("option");
                    option.value = optionData.value;
                    option.textContent = optionData.text;
                    projectDropdown.appendChild(option);
                });
            });

            $('#keperluan_surat').trigger('input');
            $('#project').val("<?= $rowPengajuan['project'] ?>").trigger('change');

            $('#diajukan_oleh').val("<?= $rowPengajuan['pengaju'] ?>").trigger('change');
            $('#disetujui_oleh').val("<?= $rowPengajuan['penyetuju'] ?>").trigger('change');

            $("#addrow").on("click", function() {
                addRow();
                updateDropdownOptions();
            });

            $("table.order-list").on("click", ".delete-row", function(event) {
                $(this).closest("tr").remove();
                if ($("table.order-list tbody tr").length === 0) {
                    addRow(); // Jika tidak ada baris, tambahkan satu baris lagi
                }
                calculateGrandTotal();
                updateDropdownOptions();
                updateRowNumbers();
            });

            $("table.order-list").on("change", "select[name='id_barang[]']", function () {
                var selectedOption = $(this).find("option:selected"); // Ambil opsi terpilih
                var harga = selectedOption.data("harga"); // Ambil data-harga dari opsi
                var namaBarang = selectedOption.data("namabarang"); // Ambil data-namaBarang dari opsi


                var row = $(this).closest("tr"); // Baris saat ini


                // Isi kolom nama_barang dengan namaBarang
                row.find("input[name='nama_barang[]']").val(namaBarang);
                // Isi kolom harga dengan nilai dari database
                row.find("textarea[name='harga[]']").val(harga).trigger("input");

                // (Opsional) Gunakan URL jika diperlukan
                var link = selectedOption.data("link");

                // Update total harga dan grand total
                calculateRow(row);
                calculateGrandTotal();

                // Update dropdown di semua baris
                updateDropdownOptions();
                updateDropdownBarang();
            });

            $("table.order-list").on("input", "textarea", function () {
                this.style.height = "auto"; // Reset tinggi dulu agar perhitungan ulang akurat

                // Periksa apakah konten hanya 1 baris
                if (this.scrollHeight <= this.offsetHeight) {
                    this.style.height = "initial"; // Kembalikan ke tinggi default jika hanya 1 baris
                } else {
                    this.style.height = this.scrollHeight + "px"; // Sesuaikan tinggi berdasarkan isi
                }
            });

            $("table.order-list").on("input", "textarea[name='kuantitas[]'], textarea[name='harga[]']", function () {
                var input = $(this);
                var cursorPos = input.prop("selectionStart"); // Simpan posisi kursor
                var originalLength = input.val().length; // Panjang teks asli sebelum format

                var cleanValue = input.val().replace(/[^0-9]/g, ""); // Hilangkan semua titik

                if (cleanValue === "" || cleanValue === "0" && originalLength === 1) {
                    input.val(""); // Tetap kosong jika tidak ada angka
                    var row = input.closest("tr");
                    calculateRow(row);
                    calculateGrandTotal();
                    return; // Tidak perlu memproses lebih lanjut
                }
                
                var formattedValue = parseInt(cleanValue || "0", 10).toLocaleString("id-ID"); // Format angka
                input.val(formattedValue); // Tampilkan angka dengan format ribuan

                var newLength = input.val().length; // Panjang teks setelah format
                var adjustment = newLength - originalLength; // Hitung perbedaan panjang

                // Atur kembali posisi kursor
                input[0].setSelectionRange(cursorPos + adjustment, cursorPos + adjustment);

                // Hitung ulang total di baris terkait
                var row = input.closest("tr");
                calculateRow(row);
                calculateGrandTotal();
            });

            $("table.order-list").on("focus", "textarea[name='kuantitas[]'], textarea[name='harga[]']", function () {
                // if ($(this).val() === "0") {
                //     $(this).val("");
                // } 
            });

            $("table.order-list").on("blur", "textarea[name='kuantitas[]'], textarea[name='harga[]']", function () {
                // if ($(this).val() === "") {
                //     $(this).val("0");
                // }
            });

            
        });

       

        function capitalizeText(input) {
            const lowercaseWords = ['dan', 'atau', 'di', 'ke', 'dari', 'dengan', 'untuk', 'pada', 'dalam', 'yang', 'sebagai'];

            $(input)
                .on("input", function () {
                    let posisi = this.selectionStart; // Simpan posisi kursor awal
                    let oldLength = $(this).val().length; // Panjang sebelum edit

                    let words = $(this).val()
                        .trimStart() // Hapus spasi di awal
                        .replace(/\s+/g, ' ') // Ganti spasi ganda jadi 1 spasi aja
                        .split(' '); // Pisah kata per kata

                    let valueBaru = words.map(word => {
                        // Cek apakah word ada di lowercaseWords (case-insensitive)
                        let lowercaseMatch = lowercaseWords.find(lw => lw.toLowerCase() === word.toLowerCase());

                        return lowercaseMatch 
                            ? lowercaseMatch // Kalau ada di list, pakai versi dari list (lowercase)
                            : word.charAt(0).toUpperCase() + word.slice(1); // Kalau bukan, capitalize
                    }).join(' ');
                    
                    $(this).val(valueBaru); // Set value baru

                    let newLength = valueBaru.length; // Panjang setelah edit
                    let perubahanPanjang = oldLength - newLength; // Selisih panjang teks

                    let posisiBaru = Math.max(posisi - perubahanPanjang, 0); // Hitung posisi kursor baru
                    this.setSelectionRange(posisiBaru, posisiBaru); // Set ulang posisi kursor
                })
                .on("blur", function () {
                    $(this).val($.trim($(this).val())); // Trim spasi awal & akhir pas blur
                });
        }

        var dropdownBarang = '';

        function updateDropdownBarang() {
            $.ajax({
                url: "get_barang.php",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // Reset dropdownBarang
                    dropdownBarang = '<option value="" selected disabled>Pilih Barang</option>';

                    // Loop hasil JSON & update dropdownBarang
                    response.forEach(function(item) {
                        dropdownBarang += `<option value="${item.stok_id}" 
                            data-harga="${item.harga_bahan}" 
                            data-link="${item.link}" 
                            data-namabarang="${item.kelompok} - ${item.nama}">
                            ${item.kelompok} - ${item.nama}
                        </option>`;
                    });

                    updateDropdownOptions();

                },
                error: function(xhr, status, error) {
                    console.error("Gagal update dropdown barang:", error);
                }
            });
        }

        function updateDropdownDB() {
            $.ajax({
                url: "get_barang.php",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // Reset dropdownBarang
                    dropdownBarang = '<option value="" selected disabled>Pilih Barang</option>';

                    // Loop hasil JSON & update dropdownBarang
                    response.forEach(function(item) {
                        dropdownBarang += `<option value="${item.stok_id}" 
                            data-harga="${item.harga_bahan}" 
                            data-link="${item.link}" 
                            data-namabarang="${item.kelompok} - ${item.nama}">
                            ${item.kelompok} - ${item.nama}
                        </option>`;
                    });

                    $(".id-barang").each(function () {
                        let row = $(this).closest("tr");
                        let stokId = $(this).attr("data-default"); // Ambil stok_id dari data-default
                        let hargaAwal = $(this).attr("data-harga-db"); // Ambil harga asli dari database

                        $(this).html(dropdownBarang); // Masukin dropdown yang baru

                        if (stokId) {
                            $(this).val(stokId).trigger("change"); // Set default value
                            row.find("textarea[name='harga[]']").val(hargaAwal).trigger("input");
                            
                        }
                    });
                    

                },
                error: function(xhr, status, error) {
                    console.error("Gagal update dropdown barang:", error);
                }
            }); 
        }

        function generateNomorSurat() {
            const keperluanSurat = $('#keperluan_surat').val();
            let project = 'KEPERLUAN'; // Default tetap "KEPERLUAN"

            if (keperluanSurat === "RISET") {
                const selectedProject = $('#project').val();
                if (selectedProject) {
                    project = 'KEBUTUHANRISET'; // Baru ganti kalau ada nilai dari dropdown project
                }
            } else {
                project = $('#project').val() || 'KEPERLUAN'; // Kalau bukan "RISET", langsung pakai nilai project
            }

            const nomorSurat = $('#nomor_surat').text().replace(/\/[^/]+\/RND\//, `/${project}/RND/`);
            $('#nomor_surat').text(nomorSurat);
            $('#input_nomor_surat').val(nomorSurat);
        }

        function addRow() {  
            // Tambahkan baris baru
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td class="row-number text-center"></td>';
            cols += '<td><select class="form-control select2" name="id_barang[]"></select><input type="hidden" name="nama_barang[]" value=""></td>';
            cols += '<td><textarea class="form-control table-input" name="kuantitas[]" rows="1" style="resize: none;" placeholder="0" required></textarea></td>';
            cols += '<td><textarea class="form-control table-input" name="harga[]" rows="1" style="resize: none;" placeholder="0" required></textarea></td>';
            cols += '<td><div class="total-harga-wrapper"><span class="prefix">Rp.</span><span class="total-harga">0</span><input type="hidden" name="total_harga[]" value="0"></div></td>';
            cols += '<td><textarea class="form-control table-input" name="keterangan_detail[]" rows="1" style="resize: none;" placeholder="Masukkan Keterangan"></textarea></td>';
            cols += '<td class="text-center"><button type="button" class="btn btn-danger btn-sm delete-row">Hapus</button></td>';
            
            newRow.append(cols);
            $("table.order-list").append(newRow);
            
            // Inisialisasi Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                containerCssClass: 'height-40px',
            });

            updateRowNumbers();
            updateDropdownBarang();
            // updateDropdownOptions();
        }

        function updateRowNumbers() {
            $("table.order-list tbody tr").each(function(index) {
                $(this).find(".row-number").text(index + 1); // Update nomor sesuai urutan
            });
        }

        // Fungsi untuk mengupdate opsi dropdown
        function updateDropdownOptions() {
            let selectedValues = [];
            $("select[name='id_barang[]']").each(function () {
                const value = $(this).val();
                if (value) selectedValues.push(value);
            });

            $("select[name='id_barang[]']").each(function () {
                const currentSelect = $(this);
                const currentValue = currentSelect.val();

                // Filter opsi dropdown
                let filteredDropdown = '';
                $(dropdownBarang).each(function () {
                    const option = $(this);
                    const value = option.attr("value");

                    // Tambahkan opsi jika belum dipilih atau sesuai dengan nilai saat ini
                    if (!selectedValues.includes(value) || value === currentValue) {
                        filteredDropdown += option.prop("outerHTML");
                    }
                });

                // Update opsi pada dropdown
                currentSelect.html(filteredDropdown);
                currentSelect.val(currentValue); // Tetap pilih nilai yang sama
            });
        }


        function calculateRow(row) {
            var qty = parseFloat(row.find("textarea[name='kuantitas[]']").val().replace(/\./g, "")) || 0;
            var price = parseFloat(row.find("textarea[name='harga[]']").val().replace(/\./g, "")) || 0;

            // Jika salah satu nilai kosong atau 0, total menjadi 0
            var total = (qty && price) ? qty * price : 0;

            row.find(".total-harga").text(total.toLocaleString("id-ID"));

            row.find("input[name='total_harga[]']").val(total);
        }

        function calculateGrandTotal() {
            var grandTotal = 0;
            $("table.order-list").find("input[name='total_harga[]']").each(function () {
                var total = parseFloat($(this).val().replace(/\./g, "")) || 0;
                grandTotal += total;
            });
            $("#grandtotal").text(grandTotal.toLocaleString("id-ID"));
            $("#grand_total_input").val(grandTotal);
        }

        function prepareFormData() {
            $("textarea[name='kuantitas[]'], textarea[name='harga[]']").each(function () {
                // Hilangkan pemisah ribuan dari semua input sebelum pengiriman
                var cleanValue = $(this).val().replace(/\./g, "");
                $(this).val(cleanValue);
            });
        }

        function validateForm() {
            var isValid = true;

            // Validasi input required
            $("#inputPengajuanForm [required]").each(function () {
                var value = $(this).val();
                if (!value || value.trim() === "") { // Periksa jika null atau kosong
                    var invalidField = $(this); // Simpan elemen yang tidak valid
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Harap lengkapi semua data yang wajib diisi',
                    }).then(() => {
                        setTimeout(() => {
                            $('html, body').animate({
                                scrollTop: invalidField.offset().top - 300 // Scroll ke posisi elemen dengan offset
                            }, 500); // 500ms untuk efek smooth scroll
                            invalidField.focus(); // Fokuskan ke elemen setelah SweetAlert ditutup
                        }, 700); // Beri jeda waktu agar tidak bentrok dengan scroll
                    });
                    isValid = false;
                    return false; // Hentikan loop
                }
            });

            if (!isValid) return false;

            // Validasi setiap baris di tabel
            $("table.order-list tbody tr").each(function () {
                var namaBarang = $(this).find("select[name='id_barang[]']").val();
                var qty = $(this).find("textarea[name='kuantitas[]']").val();
                var harga = $(this).find("textarea[name='harga[]']").val();

                if (!namaBarang || qty === "" || qty === "0" || harga === "" || harga === "0") {
                    var invalidField = $(this).find("select[name='id_barang[]'], textarea[name='kuantitas[]'], textarea[name='harga[]']").filter(function () {
                        return !this.value || this.value.trim() === "" || this.value === "0";
                    }).first(); // Ambil elemen pertama yang tidak valid


                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Harap lengkapi semua data yang wajib diisi',
                    }).then(() => {
                        setTimeout(() => {
                            $('html, body').animate({
                                scrollTop: invalidField.offset().top - 300 // Scroll ke posisi elemen dengan offset
                            }, 500); // 500ms untuk efek smooth scroll
                            invalidField.focus(); // Fokuskan ke elemen setelah SweetAlert ditutup
                        }, 700); // Beri jeda waktu agar tidak bentrok dengan scroll
                    });

                    isValid = false;
                    return false; // Hentikan loop setelah menemukan baris tidak valid
                }
            });

            return isValid;
        }

        function validateSuccess() {
            prepareFormData() 

            var formData = new FormData(document.getElementById("inputPengajuanForm"));

            $.ajax({
                type: "POST",
                url: "edit_pengajuan.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response,
                    }).then((result) => {
                            // resetForm();
                    });
                },
                error: function(xhr) { // 3
                    // let response = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '' + xhr.responseText,
                    });
                    // console.error("Error:", xhr);
                }
            });
        }

        function resetForm() {
            document.getElementById("inputPengajuanForm").reset(); // Reset semua input
            $("table.order-list tbody").empty(); // Kosongkan tabel (jika ada)
            addRow(); // Tambahkan baris baru jika perlu
            updateDropdownOptions(); // Update opsi dropdown
            calculateGrandTotal(); // Reset total jika ada

            // Scroll ke paling atas
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 700);
            
        }
    </script>
</body>

</html>