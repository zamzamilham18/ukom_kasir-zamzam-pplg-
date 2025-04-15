<?php
session_start();
include '../config/koneksi.php';

// Ambil parameter filter tanggal dari URL (jika ada)
$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalAkhir = $_GET['tanggal_akhir'] ?? '';

// Query utama untuk mengambil data dari tabel penjualan
$sqlPenjualan = "SELECT * FROM penjualan";

// Tambahkan filter tanggal jika tanggal mulai dan akhir diisi
if (!empty($tanggalMulai) && !empty($tanggalAkhir)) {
    $sqlPenjualan .= " WHERE TanggalPenjualan BETWEEN '$tanggalMulai' AND '$tanggalAkhir'";
}

// Urutkan hasil berdasarkan tanggal terbaru
$sqlPenjualan .= " ORDER BY TanggalPenjualan DESC";

// Jalankan query untuk mengambil data penjualan
$resultPenjualan = mysqli_query($conn, $sqlPenjualan);

// Pastikan 'nama_kasir' ada dalam session
if (isset($_SESSION['nama_kasir'])) {
    $namaKasir = $_SESSION['nama_kasir'];
} else {
    $namaKasir = 'Kasir Tidak Diketahui'; // Pesan default jika nama kasir tidak ada
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Konten utama -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="pt-3 pb-2 mb-3">
                <h1 class="mb-4">Laporan Penjualan</h1>

                <!-- Form filter berdasarkan tanggal -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($tanggalMulai) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= htmlspecialchars($tanggalAkhir) ?>">
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="data_penjualan.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <!-- Menampilkan data penjualan -->
                <?php if ($resultPenjualan && mysqli_num_rows($resultPenjualan) > 0): ?>
                    <?php while ($penjualan = mysqli_fetch_assoc($resultPenjualan)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Transaksi ID:</strong> <?= $penjualan['PenjualanID'] ?> |
                                <strong>Tanggal:</strong> <?= $penjualan['TanggalPenjualan'] ?> |
                                <strong>Total:</strong> Rp <?= number_format($penjualan['TotalHarga'], 0, ',', '.') ?>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nama Produk</th>
                                                <th>Harga</th>
                                                <th>Jumlah</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $penjualanID = $penjualan['PenjualanID'];
                                            $sqlDetail = "SELECT dp.*, pr.NamaProduk, pr.Harga
                                                          FROM detailpenjualan dp
                                                          JOIN produk pr ON dp.ProdukID = pr.ProdukID
                                                          WHERE dp.PenjualanID = '$penjualanID'";
                                            $resultDetail = mysqli_query($conn, $sqlDetail);

                                            if ($resultDetail && mysqli_num_rows($resultDetail) > 0):
                                                while ($detail = mysqli_fetch_assoc($resultDetail)):
                                            ?>
                                                <tr>
                                                    <td><?= $detail['NamaProduk'] ?></td>
                                                    <td>Rp <?= number_format($detail['Harga'], 0, ',', '.') ?></td>
                                                    <td><?= $detail['JumlahProduk'] ?></td>
                                                    <td>Rp <?= number_format($detail['Subtotal'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada detail produk.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">Tidak ada data penjualan yang ditemukan.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

</body>
</html