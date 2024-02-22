<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 fixed">
    <!-- Brand Logo -->
    <a href="/owl_inventory/homepage.php" class="brand-link">
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
                    <a href="/owl_inventory/homepage.php" class="nav-link <?php echo (strpos($current_page, 'homepage.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>
                </li>
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item <?php
                                    echo (strpos($current_page, 'produksi.php') !== false ||
                                        strpos($current_page, 'quality_control.php') !== false ||
                                        strpos($current_page, 'inventaris_device.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/edit/edit.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/detail/detail.php')) !== false
                                        ? 'menu-open'
                                        : ''; ?>">
                    <a href="#" class="nav-link <?php
                                                echo (strpos($current_page, 'produksi.php') !== false ||
                                                    strpos($current_page, 'quality_control.php') !== false ||
                                                    strpos($current_page, 'inventaris_device.php') !== false ||
                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/edit/edit.php') !== false ||
                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/detail/detail.php')) !== false
                                                    ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-toolbox"></i>
                        <p>
                            Produksi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/owl_inventory/produksi/produksi.php" class="nav-link <?php echo (strpos($current_page, 'produksi.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Produksi Device</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/produksi/quality_control.php" class="nav-link <?php echo (strpos($current_page, 'quality_control.php') !== false || (strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/edit/edit.php') !== false)) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quality Control</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/produksi/inventaris_device.php" class="nav-link <?php echo (strpos($current_page, 'inventaris_device.php') !== false || (strpos($_SERVER['REQUEST_URI'], '/owl_inventory/produksi/detail/detail.php') !== false)) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inventaris Device</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php
                                    echo (strpos($current_page, 'input.php') !== false ||
                                        strpos($current_page, 'monitoring.php') !== false ||
                                        strpos($current_page, 'update.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/maintenance/edit/edit.php') !== false)
                                        ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php
                                                echo (strpos($current_page, 'input.php') !== false ||
                                                    strpos($current_page, 'monitoring.php') !== false ||
                                                    strpos($current_page, 'update.php') !== false ||
                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/maintenance/edit/edit.php') !== false)
                                                    ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>
                            Maintenance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/owl_inventory/maintenance/input.php" class="nav-link <?php echo (strpos($current_page, 'input.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Input</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/maintenance/monitoring.php" class="nav-link <?php
                                                                                                echo (strpos($current_page, 'monitoring.php') !== false) ||
                                                                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/maintenance/edit/edit.php') !== false
                                                                                                    ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Monitoring</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/maintenance/update.php" class="nav-link <?php echo (strpos($current_page, 'update.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Update</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php
                                    echo (strpos($current_page, 'pengiriman.php') !== false ||
                                        strpos($current_page, 'penarikan.php') !== false ||
                                        strpos($current_page, 'penggantian.php') !== false)
                                        ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php
                                                echo (strpos($current_page, 'pengiriman.php') !== false ||
                                                    strpos($current_page, 'penarikan.php') !== false ||
                                                    strpos($current_page, 'penggantian.php') !== false)
                                                    ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>
                            Pengelolaan Device
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/owl_inventory/pengelolaan/pengiriman.php" class="nav-link <?php echo (strpos($current_page, 'pengiriman.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengiriman</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/pengelolaan/penarikan.php" class="nav-link <?php echo (strpos($current_page, 'penarikan.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penarikan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/pengelolaan/penggantian.php" class="nav-link <?php echo (strpos($current_page, 'penggantian.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penggantian</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/prototype.php" class="nav-link <?php echo (strpos($current_page, 'prototype.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-screwdriver"></i>
                        <p>
                            Prototype
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/restock.php" class="nav-link <?php echo (strpos($current_page, 'restock.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Restock
                        </p>
                    </a>
                </li>
                <li class="nav-header">TAMBAH DATA</li>
                <li class="nav-item">
                    <a href="/owl_inventory/master_bahan.php" class="nav-link <?php echo (strpos($current_page, 'master_bahan.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fa fa-pen"></i>
                        <p>
                            Master Bahan
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/master_device.php" class="nav-link <?php echo (strpos($current_page, 'master_device.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cube"></i>
                        <p>
                            Master Device
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/harga_bahan.php" class="nav-link <?php echo (strpos($current_page, 'harga_bahan.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>
                            Harga Bahan
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/lokasi_penyimpanan.php" class="nav-link <?php echo (strpos($current_page, 'lokasi_penyimpanan.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Lokasi Penyimpanan
                        </p>
                    </a>
                </li>
                <li class="nav-item <?php
                                    echo (strpos($current_page, 'tambah_perusahaan.php') !== false ||
                                        strpos($current_page, 'list_perusahaan.php') !== false) ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/perusahaan/edit/edit.php') !== false
                                        ? 'menu-open' : ''; ?>">
                    <a href="perusahaan.php" class="nav-link <?php
                                                                echo (strpos($current_page, 'tambah_perusahaan.php') !== false ||
                                                                    strpos($current_page, 'list_perusahaan.php') !== false) ||
                                                                    strpos($_SERVER['REQUEST_URI'], '/owl_inventory/perusahaan/edit/edit.php') !== false
                                                                    ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-industry"></i>
                        <p>
                            Perusahaan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/owl_inventory/perusahaan/tambah_perusahaan.php" class="nav-link <?php echo (strpos($current_page, 'tambah_perusahaan.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tambah Perusahaan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/owl_inventory/perusahaan/list_perusahaan.php" class="nav-link <?php
                                                                                                    echo (strpos($current_page, 'list_perusahaan.php') !== false) ||
                                                                                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/perusahaan/edit/edit.php') !== false
                                                                                                        ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>List Perusahaan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php
                                    echo strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user_account/list.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user_account/tambah.php') !== false ||
                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user_account/edit.php') !== false
                                        ? 'menu-open' : ''; ?>">
                    <a href="/owl_inventory/user_account/list.php" class="nav-link <?php
                                                                    echo strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user_account/list.php') !== false ||
                                                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user_account/tambah.php') !== false ||
                                                                        strpos($_SERVER['REQUEST_URI'], '/owl_inventory/user_account/edit.php') !== false
                                                                        ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            User
                        </p>
                    </a>
                </li>
                <li class="nav-header">PELAPORAN</li>
                <li class="nav-item">
                    <a href="/owl_inventory/status_device.php" class="nav-link <?php echo (strpos($current_page, 'status_device.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-walkie-talkie"></i>
                        <p>
                            Status Device
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/stok_bahan.php" class="nav-link <?php echo (strpos($current_page, 'stok_bahan.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon ion ion-pie-graph"></i>
                        <p>
                            Stok Bahan
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/owl_inventory/stok_produk.php" class="nav-link <?php echo (strpos($current_page, 'stok_produk.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-microchip"></i>
                        <p>
                            Stok Produk
                        </p>
                    </a>
                </li>
                <li class="nav-item" style="margin-bottom: 40px;">
                    <a href="/owl_inventory/histori_transaksi.php" class="nav-link <?php echo (strpos($current_page, 'histori_transaksi.php') !== false) ? 'active' : ''; ?>">
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