<?php
// Memulai session
session_start();

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, redirect ke login page
    header('Location: login.php');
    exit();
}

// Menghubungkan ke database
include '../config/koneksi.php';

// Mengambil data produk dari tabel produk
$sql = "SELECT ProdukID, Barcode, NamaProduk, Harga, Stok FROM produk"; 
$result = $conn->query($sql);

// Cek apakah query berhasil dijalankan
if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <!-- Memuat style dari project dan Bootstrap -->
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../asset/favicon-16x16.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="row">
        <!-- Sidebar diambil dari file terpisah agar bisa dipakai ulang -->
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Judul halaman -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Data Produk</h1>
            </div>

            <div class="container mt-5">
                <!-- Tombol untuk tambah produk dan kembali ke dashboard -->
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="tambah_data_barang.php" class="btn btn-primary mb-3">Tambah Produk</a>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali</a>

                <!-- Tabel untuk menampilkan semua data produk -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Inisialisasi nomor urut
                        $no = 1;

                        // Cek apakah ada data yang ditemukan dari database
                        if ($result->num_rows > 0) {
                            // Menampilkan data produk satu per satu
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['Barcode'] . "</td>";
                                echo "<td>" . $row['NamaProduk'] . "</td>";
                                echo "<td>Rp " . number_format($row['Harga'], 0, ',', '.') . "</td>";
                                echo "<td>" . $row['Stok'] . "</td>";
                                
                                // Tampilkan kolom aksi hanya untuk admin
                                if ($_SESSION['role'] == 'admin') {
                                    echo "<td>
                                            <!-- Tombol edit dan hapus dengan konfirmasi -->
                                            <a hsref='edit_barang.php?ProdukID=" . $row['ProdukID'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                            <a href='hapus_barang.php?ProdukID=" . $row['ProdukID'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus produk ini?\")'>Hapus</a>
                                          </td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            // Tampilkan pesan jika tidak ada data
                            echo "<tr><td colspan='6'>Tidak ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

<?php
// Menutup koneksi database
$conn->close();
?>
