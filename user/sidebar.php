<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 fixed">
    <!-- Brand Logo -->
    <a href="/owl_inventory/user/homepage.php" class="brand-link">
        <img src="/owl_inventory/assets/adminlte/dist/img/OWLlogo.png" alt="OWL Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-heavy">OWL RnD</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
        with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="/owl_inventory/user/homepage.php" class="nav-link <?php echo (strpos($current_page, 'homepage.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>
                </li>
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item <?php
                                    echo (
                                        strpos($current_page, 'inventaris_device.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user/produksi/detail/detail.php')) !== false
                                        ? 'menu-open'
                                        : ''; ?>">
                    <a href="#" class="nav-link <?php
                                                echo (
                                                    strpos($current_page, 'inventaris_device.php') !== false ||
                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user/produksi/detail/detail.php')) !== false
                                                    ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-toolbox"></i>
                        <p>
                            Produksi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/owl_inventory/user/produksi/inventaris_device.php" class="nav-link <?php echo (strpos($current_page, 'inventaris_device.php') !== false || strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/detail/detail.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inventaris Device</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php
                                    echo (
                                        strpos($current_page, 'monitoring.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user/maintenance/edit/edit.php') !== false)
                                        ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php
                                                echo (
                                                    strpos($current_page, 'monitoring.php') !== false ||
                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user/maintenance/edit/edit.php') !== false)
                                                    ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>
                            Maintenance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/owl_inventory/user/maintenance/monitoring.php" class="nav-link <?php
                                                                                                echo (strpos($current_page, 'monitoring.php') !== false) ||
                                                                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user/maintenance/edit/edit.php') !== false
                                                                                                    ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Monitoring</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">PELAPORAN</li>
                <li class="nav-item">
                    <a href="/owl_inventory/user/stok_bahan.php" class="nav-link <?php echo (strpos($current_page, 'stok_bahan.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon ion ion-pie-graph"></i>
                        <p>
                            Stok Bahan
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/user/stok_produk.php" class="nav-link <?php echo (strpos($current_page, 'stok_produk.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-microchip"></i>
                        <p>
                            Stok Produk
                        </p>
                    </a>
                </li>
                <li class="nav-item" style="margin-bottom: 40px;">
                    <a href="/owl_inventory/user/histori_transaksi.php" class="nav-link <?php echo (strpos($current_page, 'histori_transaksi.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-history"></i>
                        <p>
                            Histori Transaksi
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>