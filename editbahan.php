<?php

include "./connection.php";
include "./admin_privilege.php";


// Cek apakah request GET memiliki ID
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $stok_id = $_GET['id'];

        // Ambil data dari database berdasarkan stok_id
        $query = "SELECT * FROM masterbahan WHERE stok_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $stok_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            die("ID tidak ditemukan di database.");
        }
    } else {
        die("ID tidak diberikan di URL.");
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Ambil data dari form
        $stok_id = $_POST["stok_id"] ?? null;
        $kelompok = $_POST["kelompok"] ?? null;
        $nama = $_POST["nama"] ?? null;
        $quantity = $_POST["quantity"] ?? null;
        $harga_bahan = $_POST["harga_bahan"] ?? null;
        $lokasi_penyimpanan = $_POST["lokasi_penyimpanan"] ?? null;
        $link_foto = $_POST["link_foto"] ?? null;
        $link = $_POST["link"] ?? null;
        $deskripsi = $_POST["deskripsi"] ?? null;

        // Validasi tidak boleh kosong
        if (
            empty($stok_id) || empty($kelompok) || empty($nama) || empty($quantity) ||
            empty($harga_bahan) || empty($lokasi_penyimpanan) || empty($link_foto) ||
            empty($link) || empty($deskripsi)
        ) {
            http_response_code(400); // Set HTTP response jadi 400 (Bad Request)
            echo "Semua kolom wajib diisi!";
            exit();
        }

        // Validasi quantity dan harga_bahan harus integer dan > 0
        if (!ctype_digit($quantity) || intval($quantity) <= 0) {
            http_response_code(400);
            echo "Quantity harus berupa angka positif!";
            exit();
        }

        if (!ctype_digit($harga_bahan) || intval($harga_bahan) <= 0) {
            http_response_code(400);
            echo "Harga bahan harus berupa angka positif!";
            exit();
        }

        // Validasi URL
        if (!filter_var($link, FILTER_VALIDATE_URL) || !filter_var($link_foto, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo "Link dan link foto harus berupa URL valid!";
            exit();
        }

        // Query untuk update data
        $updateQuery = "UPDATE masterbahan SET kelompok = ?, nama = ?, quantity = ?, harga_bahan = ?, lokasi_penyimpanan = ?, link_foto = ?, link = ?, deskripsi = ? WHERE stok_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssiissssi", $kelompok, $nama, $quantity, $harga_bahan, $lokasi_penyimpanan, $link_foto, $link, $deskripsi, $stok_id);

        if ($updateStmt->execute()) {
            echo "Data berhasil diperbarui!";
        } else {
            http_response_code(500); // Internal Server Error
            echo "Terjadi kesalahan saat memperbarui data!";
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Bahan</title>

    <link rel="icon" href="./assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
                            <?php
                                echo "<h1>Edit Stok Bahan {$stok_id}</h1>";
                            ?>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Bahan</h3>
                        </div>

                        <form id="editbahanForm" method="post">
                            <input type="hidden" name="stok_id" value="<?php echo htmlspecialchars($stok_id); ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="kelompok">Kelompok <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="kelompok" name="kelompok" placeholder="Masukkan kelompok" value="<?php echo $row['kelompok']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" value="<?php echo $row['nama']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Quantity <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Masukkan quantity" min="1" value="<?php echo $row['quantity']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="harga_bahan">Harga Bahan <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="harga_bahan" name="harga_bahan" placeholder="Masukkan harga bahan" min="1" value="<?php echo $row['harga_bahan']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lokasi_penyimpanan">Lokasi Penyimpanan</label>
                                    <input type="text" class="form-control" id="lokasi_penyimpanan" name="lokasi_penyimpanan" placeholder="Masukkan lokasi penyimpanan" value="<?php echo $row['lokasi_penyimpanan']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="link_foto">Link Foto <span style="color: red;">*</span></label>
                                    <input type="url" class="form-control" id="link_foto" name="link_foto" placeholder="Masukkan link foto" value="<?php echo $row['link_foto']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="link">Link Pembelian <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="link" name="link" placeholder="Masukkan link pembelian" value="<?php echo $row['link']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi" value="<?php echo $row['deskripsi']; ?>">
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end">
                                <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- jQuery -->
    <script src="./assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="./assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="./assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="./assets/adminlte/dist/js/adminlte.min.js"></script>
    <!-- Page specific script -->
    <script>
        document.getElementById('editbahanForm').addEventListener('submit', function (event) {
            event.preventDefault();  // Mencegah form submit biasa

            var form = event.target;
            var formData = new FormData(form);

            $.ajax({
                type: "POST",
                url: "editbahan.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log("Response dari server:", response);
                    
                    // Tampilkan pesan sukses dengan SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: response, // Langsung pakai pesan dari server
                        showConfirmButton: false,
                        // timer: 1500
                    });
                },
                error: function (xhr) {
                    console.log("Error:", xhr.responseText);

                    // Tampilkan pesan error dari server
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseText, // Menampilkan pesan error dari server
                    });
                }
            });
        });

        document.querySelectorAll('#quantity, #harga_bahan').forEach(input => {
            input.addEventListener('input', e => {
                const cleanValue = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = (cleanValue === "0" || cleanValue === "") ? "" : cleanValue;
            });
        });
    </script>

</body>

</html>
