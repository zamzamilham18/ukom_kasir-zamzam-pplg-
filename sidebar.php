<!-- Sidebar navigasi -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar bg-dark">
    <!-- Bagian logo dan judul -->
    <div class="text-center py-3">
        <!-- Logo toko/kasir -->
        <img src="../asset/dashboard.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
        <!-- Judul sidebar -->
        <h2 class="text-white mt-2">Kasir</h2>
    </div>

    <!-- Menu navigasi utama -->
    <ul class="nav flex-column">
        <!-- Menu Home -->
        <li class="nav-item">
            <a class="nav-link text-white" href="dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
        </li>

        <!-- Menu Penjualan -->
        <li class="nav-item">
            <a class="nav-link text-white" href="penjualan.php">
                <i class="fas fa-shopping-cart"></i> Penjualan
            </a>
        </li>

        <!-- Menu Data Barang -->
        <li class="nav-item">
            <a class="nav-link text-white" href="data_barang.php">
                <i class="fas fa-box"></i> Data Barang
            </a>
        </li>

        <!-- Menu Data Penjualan -->
        <li class="nav-item">
            <a class="nav-link text-white" href="data_penjualan.php">
                <i class="fas fa-receipt"></i> Data Penjualan
            </a>
        </li>

        <!-- Menu Kelola User (Hanya untuk Admin) -->
        <?php if ($_SESSION['role'] == 'admin') : ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="kelola-user.php">
                    <i class="fas fa-users"></i> User
                </a>
            </li>
        <?php endif; ?>

        <!-- Tombol Logout -->
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<!-- Tambahkan link ke Font Awesome agar ikon bisa muncul -->
<!-- CDN Font Awesome versi 6.4.2, digunakan untuk ikon di sidebar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
