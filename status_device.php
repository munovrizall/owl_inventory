<?php

include "connection.php";

$queryClient = "SELECT * FROM client ORDER BY nama_client";
$resultClient = $conn->query($queryClient);

if (isset($_POST['pilihClient'])) {
    $selectedClient = $_POST['pilihClient'];

    // Milih bahan untuk produksi
    $query = "SELECT i.id, i.nama_client, i.produk, i.no_sn, i.firmware_version, i.hardware_version, i.temperature, i.last_online, i.bat, i.pt, i.unit, i.status_error, p.gambar_produk
    FROM inventaris_produk i
    JOIN produk p ON i.produk = p.nama_produk
    WHERE i.nama_client = ? AND
        i.id IS NOT NULL AND 
        i.type_produk IS NOT NULL AND 
        i.produk IS NOT NULL AND 
        i.chip_id IS NOT NULL AND 
        i.no_sn IS NOT NULL AND 
        i.nama_client IS NOT NULL AND 
        i.garansi_awal IS NOT NULL AND 
        i.garansi_akhir IS NOT NULL AND 
        i.garansi_void IS NOT NULL AND 
        i.keterangan_void IS NOT NULL AND 
        i.ip_address IS NOT NULL AND 
        i.mac_wifi IS NOT NULL AND 
        i.mac_bluetooth IS NOT NULL AND 
        i.firmware_version IS NOT NULL AND 
        i.hardware_version IS NOT NULL AND 
        i.free_ram IS NOT NULL AND 
        i.min_ram IS NOT NULL AND 
        i.batt_low IS NOT NULL AND 
        i.batt_high IS NOT NULL AND 
        i.temperature IS NOT NULL AND 
        i.status_error IS NOT NULL AND 
        i.gps_latitude IS NOT NULL AND 
        i.gps_longitude IS NOT NULL AND 
        i.status_qc_sensor_1 IS NOT NULL AND 
        i.status_qc_sensor_2 IS NOT NULL AND 
        i.status_qc_sensor_3 IS NOT NULL AND 
        i.status_qc_sensor_4 IS NOT NULL AND 
        i.status_qc_sensor_5 IS NOT NULL AND 
        i.status_qc_sensor_6 IS NOT NULL
    ORDER BY i.last_online DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedClient);
    $stmt->execute();
    $stmt->bind_result($id, $namaClient, $produk, $noSN, $firmwareVersion, $hardwareVersion, $temperature, $lastOnline, $battery, $pt, $unit, $statusError, $gambarProduk);
    $resultDevices = array();
    while ($stmt->fetch()) {
        $resultDevices[] = array(
            'id' => $id,
            'namaClient' => $namaClient,
            'produk' => $produk,
            'noSN' => $noSN,
            'firmwareVersion' => $firmwareVersion,
            'hardwareVersion' => $hardwareVersion,
            'temperature' => $temperature,
            'lastOnline' => $lastOnline,
            'battery' => $battery,
            'pt' => $pt,
            'unit' => $unit,
            'statusError' => $statusError,
            'gambarProduk' => $gambarProduk,
        );
    }

    $stmt->close();
    echo json_encode($resultDevices);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Device</title>

    <link rel="icon" href="assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Table with search -->
    <link href="https://cdn.datatables.net/v/bs4/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-print-2.4.2/fh-3.4.0/r-2.5.0/rg-1.4.1/sb-1.6.0/sp-2.2.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="assets/adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        .card-container {
            max-width: 300px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .card-device {
            width: 300px;
            /* Set a specific width for each card */
            margin: 10px;
        }

        .image img {
            width: 100%;
        }

        .version-text {
            font-size: 12px;
            margin-bottom: 2px;
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
                            <h1>Status Device</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Status Device</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <section class="content">

                        <!-- general form elements -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">List Device</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pilihClient">Pilih PT <span style="color: red;">*</span></label>
                                    <select class="form-control select2" id="pilihClient" name="pilihClient">
                                        <option value="">--- Pilih PT ---</option>
                                        <?php
                                        while ($row = $resultClient->fetch_assoc()) {
                                            echo '<option value="' . $row['nama_client'] . '">' . $row['nama_client'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-outline-info btn-block" id="cekButton" name="cekButton" style="margin-top: 10px; margin-bottom: 24px; max-width: 180px;"><i class="fas fa-sync-alt" style="margin-right: 8px;" onclick="cekDevices()"></i>Cek</button>
                                <div id="cardsContainer" class="row"></div>

                            </div>

                        </div>

                </div>
                <!-- general form elements -->
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
    <!-- Bootstrap 4 -->
    <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/adminlte/dist/js/adminlte.min.js"></script>
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
    <!-- Select2 -->
    <script src="assets/adminlte/plugins/select2/js/select2.full.min.js"></script>
    <!-- Moment js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/locale/id.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(document).ready(function() {

            //Initialize Select2 Elements
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                containerCssClass: 'height-40px',
            });
        });

        $(function() {
            document.getElementById("cekButton").addEventListener("click", function() {
                cekDevices();
            });
        });

        setInterval(function() {
            cekDevices();
        }, 60000);

        function cekDevices() {
            var selectedClient = document.getElementById("pilihClient").value;

            // Fetch and update the table
            $.ajax({
                type: "POST",
                url: "status_device.php",
                data: {
                    pilihClient: selectedClient,
                },
                dataType: "json",
                success: function(response) {
                    var cardsContainer = document.getElementById("cardsContainer");
                    cardsContainer.innerHTML = ""; // Clear existing cards

                    response.forEach(function(device) {
                        var cardContainer = document.createElement("div");
                        cardContainer.className = "height card-container d-flex justify-content-center align-items-center";
                        var card = document.createElement("div");
                        card.className = "card card-device p-3";

                        var headerContainer = document.createElement("div");
                        headerContainer.className = "d-flex justify-content-between align-items-center";

                        var titleContainer = document.createElement("div");
                        titleContainer.className = "mt-2";

                        var snNumber = document.createElement("h4");
                        snNumber.className = "text-uppercase";
                        snNumber.textContent = device.noSN; // Use the relevant property from the response

                        var firmware = document.createElement("p");
                        firmware.className = "version-text";
                        firmware.textContent = "Firmware : v" + device.firmwareVersion; // Use the relevant property from the response

                        var hardware = document.createElement("p");
                        hardware.className = "version-text";
                        hardware.textContent = "Hardware: v" + device.hardwareVersion; // Use the relevant property from the response

                        titleContainer.appendChild(snNumber);
                        titleContainer.appendChild(firmware);
                        titleContainer.appendChild(hardware);

                        var productDetailsContainer = document.createElement("div");
                        productDetailsContainer.className = "mt-5";

                        var mainHeading = document.createElement("h3");
                        mainHeading.className = "main-heading mt-0";
                        mainHeading.style.marginBottom = "4px";
                        mainHeading.textContent = device.produk;

                        var imageContainer = document.createElement("div");
                        imageContainer.className = "image";

                        var image = document.createElement("img");

                        if (device.gambarProduk != null) {
                            image.src = device.gambarProduk; 
                        } else {
                            image.src = "assets/adminlte/dist/img/owl.png"
                        }
                        image.height = "160";

                        var blinkingImage = document.createElement("img");
                        var lastOnlineMoment = moment(device.lastOnline);
                        var now = moment(); // Current time
                        var minutesAgo = now.diff(lastOnlineMoment, 'minutes');

                        if (minutesAgo < 60) {
                            blinkingImage.src = 'assets/adminlte/dist/img/online.gif'; // Set the path to your blinking GIF
                            blinkingImage.style.position = "absolute";
                            blinkingImage.style.top = "8px";
                            blinkingImage.style.right = "8px";
                            blinkingImage.style.display = "block";
                        } else {
                            blinkingImage.style.display = "none";
                        }
                        var owlImage = document.createElement("img");
                        owlImage.src = 'assets/adminlte/dist/img/owl.png'; // Set the path to your blinking GIF
                        owlImage.style.position = "absolute";
                        owlImage.style.height = "24px";
                        owlImage.style.width = "24px";
                        owlImage.style.top = "8px";
                        owlImage.style.left = "16px";
                        owlImage.style.display = "block";

                        var description = document.createElement("p");
                        if (device.pt == null) {
                            device.pt = ""
                        }

                        if (device.unit == null) {
                            device.unit = ""
                        }
                        description.style.marginBottom = "4px";
                        description.textContent = device.pt + " - " + device.unit;

                        var temperature = document.createElement("p");
                        temperature.style.marginBottom = "2px";

                        if (device.temperature > 40) {
                            temperature.innerHTML = "Temperature: <span class='badge bg-danger'>" + device.temperature + "°C</span>"; // Use the relevant property from the response
                        } else {
                            temperature.innerHTML = "Temperature: <span class='badge bg-success'>" + device.temperature + "°C</span>"; // Use the relevant property from the response
                        }
                        var battery = document.createElement("p");
                        battery.style.marginBottom = "2px";

                        if (device.battery == null) {
                            battery.textContent = "Battery: -";
                        } else {
                            var batteryIcon = document.createElement("i");

                            // Menentukan warna ikon berdasarkan level baterai
                            if (device.battery > 70) {
                                batteryIcon.className = "fas fa-battery-full";
                                batteryIcon.style.color = "green";
                            } else if (device.battery > 60) {
                                batteryIcon.className = "fas fa-battery-three-quarters";
                                batteryIcon.style.color = "orange";
                            } else if (device.battery > 50) {
                                batteryIcon.className = "fas fa-battery-half";
                                batteryIcon.style.color = "orange";
                            } else if (device.battery > 10) {
                                batteryIcon.className = "fas fa-battery-quarter";
                                batteryIcon.style.color = "orange";
                            } else {
                                batteryIcon.className = "fas fa-battery-empty";
                                batteryIcon.style.color = "red";
                            }

                            // Menambahkan teks dan ikon ke elemen battery
                            battery.appendChild(document.createTextNode("Battery: "));
                            battery.appendChild(batteryIcon);
                            battery.appendChild(document.createTextNode(" " + device.battery + "%"));
                        }

                        var statusError = document.createElement("p");
                        statusError.style.marginBottom = "2px";
                        statusError.textContent = "Status Error: " + device.statusError;

                        var lastOnline = document.createElement("p");

                        // Assuming device.lastOnline is a string in the format "YYYY-MM-DD HH:mm:ss"
                        var lastOnlineMoment = moment(device.lastOnline);

                        // Display the time difference in a human-readable format
                        lastOnline.innerHTML = "Online: " + formatTimeAgo(lastOnlineMoment);

                        var detailLink = document.createElement("a");
                        detailLink.className = "btn btn-info";
                        detailLink.textContent = "Detail";
                        detailLink.href = 'produksi/detail/detail.php?id=' + device.id;

                        productDetailsContainer.appendChild(mainHeading);
                        headerContainer.appendChild(titleContainer);
                        headerContainer.appendChild(imageContainer.appendChild(image));
                        headerContainer.appendChild(blinkingImage);
                        headerContainer.appendChild(owlImage);
                        card.appendChild(headerContainer);
                        card.appendChild(mainHeading);
                        card.appendChild(description);
                        card.appendChild(temperature);
                        card.appendChild(battery);
                        card.appendChild(statusError);
                        card.appendChild(lastOnline);
                        card.appendChild(detailLink);
                        cardContainer.appendChild(card);
                        cardsContainer.appendChild(cardContainer);
                    });



                },
                error: function(xhr, status, error) {
                    console.error("Error fetching produksi data:", error);
                    console.log("XHR status:", status);
                    console.log("XHR response:", xhr.responseText);
                }
            });
        }

        function formatTimeAgo(lastOnlineMoment) {
            moment.locale('id');

            if (!lastOnlineMoment || !lastOnlineMoment.isValid()) {
                return "<span class='badge bg-danger'>offline</span>";
            }

            var now = moment(); // Current time
            var minutesAgo = now.diff(lastOnlineMoment, 'minutes');

            if (minutesAgo < 1) {
                return "<span class='badge bg-success'>sekarang</span>";
            } else if (minutesAgo < 60) {
                return "<span class='badge bg-success'>" + lastOnlineMoment.fromNow() + "</span>";
            } else if (minutesAgo < 1440) {
                return "<span class='badge bg-warning'>" + lastOnlineMoment.fromNow() + "</span>";
            } else if (minutesAgo < 7200) {
                return "<span class='badge bg-warning'>kemarin</span>";
            } else {
                return "<span class='badge bg-warning'>" + lastOnlineMoment.fromNow() + "</span>";
            }
        }
    </script>

</body>

</html>