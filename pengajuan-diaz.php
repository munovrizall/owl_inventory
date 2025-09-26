<?php

include "connection.php";
include "admin_privilege.php";



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Barang</title>
    
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
    <!-- flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div>
    <?php
        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        include $rootPath . "/owl_inventory/includes/navbar.php";
        include $rootPath . "/owl_inventory/includes/sidebar.php";
    ?>
    </div>
     <!-- Content Wrapper. Contains page content -->
     <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Pengajuan</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Pengajuan</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <section class="content">

                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">List Pengajuan</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="PengajuanForm">
                            <div class="card-header">
                                <div class="form-group">
                                        <div class="form-group">
                                            <label for="judulpengajuan">Judul Pengajuan:<span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2" id="judulpengajuan" placeholder="Masukkan judul pengajuan baru">
                                        </div>
                                        <div class="form-group">
                                            <label for="unit">Unit:<span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2" id="unit" placeholder="Masukkan unit">
                                        </div>
                                        <div class="form-group">
                                            <label for="batch">Batch:</label>
                                            <input type="text" class="form-control form-control-border border-width-2"placeholder="Masukkan batch">
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan:</label>
                                            <input type="text" class="form-control form-control-border border-width-2"placeholder="Masukkan keterangan pengajuan">
                                        </div>
                                        <div class="form-group">
                                            <label for="namapengaju">Diajukan Oleh:<span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2"placeholder="Masukkan nama pengaju">
                                        </div>
                                        <div class="form-group">
                                            <label for="namapenyetuju">Disetujui Oleh:<span style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-border border-width-2"placeholder="Masukkan nama penyetuju">
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal">Pilih Tanggal:<span style="color: red;">*</span></label>
                                            <input type="date" class="form-control form-control-border border-width-2" placeholder="Select Date">
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table id="items-container" class=" table order-list table-striped">
                                                    <thead>
                                                        <tr>
                                                            <td class="text-center lebar-kolom1"><b>Nama Barang <span style="color: red;">*</span></b></td>
                                                            <td class="text-center lebar-kolom2"><b>Kuantitas <span style="color: red;">*</span></b></td>
                                                            <td class="text-center lebar-kolom3"><b>Harga <span style="color: red;">*</span></b></td>
                                                            <td class="text-center lebar-kolom4"><b>Total Harga</b></td>
                                                            <td class="text-center lebar-kolom5"><b>Keterangan <span style="color: red;">*</span></b></td>
                                                            <td class="text-center lebar-kolom6"><b>Aksi</b></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select name="" id="">
                                                                    <option value="">Pilih Barang</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="number" min="0" value ="0" id="quantity" oninput="multiplyBy('quantity','price','total-price')"></td>
                                                            <td><input type="number" min="0" value="0" id="price" oninput="multiplyBy('quantity','price','total-price')"></td>
                                                            <td>Rp. <span id="total-price">0</span></td>
                                                            <td><input type ="text"></td>
                                                            <td>
                                                                <button class="delete-btn" onclick="deleteRow">Delete</button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5" style="text-align: left;">
                                                                <button class="btn btn-outline-info btn-block" id="add-items"> +Tambah Barang </button>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary" onclick="if(validateForm()) { validateSuccess(); resetForm(); }">Submit</button>
                                        </div>
                                        
                                </div>
                            </div>
                        </form>
                    
                </div>
                <!-- general form elements -->
            </section>
        <script>
            document.getElementById('add-items').addEventListener('click', function (event) {
                let clicks = localStorage.getItem('clicks') ? parseInt(localStorage.getItem('clicks')) : 0;
                clicks++;
                localStorage.setItem('clicks', clicks);
                var qtyId='qty'+clicks;
                var priceId='price'+clicks;
                var totalId='total'+clicks;
                event.preventDefault(); // Prevents the default form submission behavior.
                const container = document.querySelector('#items-container tbody'); // Select the correct table body.
                console.log(qtyId);
                const newRow = `
                    <tr>
                        <td>
                            <select name="" id="">
                                <option value="">Pilih Barang</option>
                            </select>
                        </td>
                        <td><input type="number" min="0" value ="0" id= "${qtyId}" oninput="multiplyBy('${qtyId}','${priceId}','${totalId}')"></td>
                        <td><input type="number" min="0" value="0" id= "${priceId}" oninput="multiplyBy('${qtyId}','${priceId}','${totalId}')"></td>
                        <td>Rp. <span id= "${totalId}">0</span></td>
                        <td><input type ="text"></td>
                        <td>
                            <button class="delete-btn" onclick="deleteRow">Delete</button>
                        </td>
                    </tr>
                `;
                console.log(newRow);
                container.insertAdjacentHTML('beforeend', newRow);
                });

            function deleteRow(button) {
                button.closest('tr').remove(); // Removes the row of the clicked delete button.
            }

            function multiplyBy(qtyId, priceId,totalId) {
                const value1 = parseFloat(document.getElementById(qtyId).value) || 0;
                const value2 = parseFloat(document.getElementById(priceId).value) || 0;
                const result = value1 * value2;
                document.getElementById(totalId).textContent = result;
        }

        </script>
</body>            
</html>