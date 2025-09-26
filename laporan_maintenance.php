<?php

include "connection.php";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Maintenance</title>

    <!-- Tambahkan Plotly.js melalui CDN -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <link rel="icon" href="../assets/adminlte/dist/img/OWLlogo.png" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    

    <style>
        
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
                            <h1>Laporan Maintenance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../homepage.php">Home</a></li>
                                <li class="breadcrumb-item active">Produksi</li>
                                <li class="breadcrumb-item active">Laporan Maintenance</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Tempatkan grafik -->   
                                    <!-- <h2>Grafik Maintenance</h2> -->

                                    <div id="plot1" style="width: 100%;"></div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div id="plot2" style="width: 100%;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="plot3" style="width: 100%;"></div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div id="plot4" style="width: 100%;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="plot5" style="width: 100%;"></div>
                                        </div>
                                    </div>

                                </div>    
                            </div>
                        </div>
                    </div>
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

    <!-- Skrip untuk membuat grafik dengan Plotly -->
    <script>
        // Mengambil data dan grafik dari Flask
        fetch('http://localhost:5000/maintenance')
            .then(response => response.json())
            .then(data => {   

                var graph_json = JSON.parse(data.graph_monthly_maintenance);
                Plotly.newPlot('plot1', graph_json.data, graph_json.layout, graph_json.config);

                var graph_json = JSON.parse(data.graph_average_age);
                Plotly.newPlot('plot2', graph_json.data, graph_json.layout, graph_json.config);

                var graph_json = JSON.parse(data.graph_product_age);
                Plotly.newPlot('plot3', graph_json.data, graph_json.layout, graph_json.config);

                var graph_json = JSON.parse(data.graph_component);
                Plotly.newPlot('plot4', graph_json.data, graph_json.layout, graph_json.config);

                var graph_json = JSON.parse(data.graph_comparison);
                Plotly.newPlot('plot5', graph_json.data, graph_json.layout, graph_json.config);
        
            })
            .catch(error => console.error('Error:', error));
    </script>


     
    <!-- jQuery -->
    <script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>