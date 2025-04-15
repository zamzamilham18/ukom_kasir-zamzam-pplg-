<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$total_produk = 0;
$total_transaksi = 0;
$total_pendapatan = 0;

// Total produk
$sql_produk = "SELECT COUNT(*) AS total_produk FROM produk";
$result_produk = $conn->query($sql_produk);
if ($result_produk && $result_produk->num_rows > 0) {
    $row_produk = $result_produk->fetch_assoc();
    $total_produk = $row_produk["total_produk"];
}

// Total transaksi & pendapatan hari ini
$sql_transaksi = "SELECT COUNT(*) AS total_transaksi, COALESCE(SUM(TotalHarga), 0) AS total_pendapatan 
                  FROM penjualan WHERE DATE(TanggalPenjualan) = CURDATE()";
$result_transaksi = $conn->query($sql_transaksi);
if ($result_transaksi && $result_transaksi->num_rows > 0) {
    $row_transaksi = $result_transaksi->fetch_assoc();
    $total_transaksi = $row_transaksi["total_transaksi"];
    $total_pendapatan = $row_transaksi["total_pendapatan"];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../asset/favicon-16x16.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Selamat Datang <?php echo $_SESSION['username']; ?>!</h1>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card text-bg-primary mb-3">
                        <div class="card-header">
                            <i class="fas fa-box-open"></i> Total Produk
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-cubes"></i> <?php echo $total_produk; ?></h5>
                            <p class="card-text">Semua produk yang tersedia di toko.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-bg-success mb-3">
                        <div class="card-header">
                            <i class="fas fa-handshake"></i> Total Transaksi Hari Ini
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-shopping-cart"></i> <?php echo $total_transaksi; ?></h5>
                            <p class="card-text">Total transaksi yang terjadi hari ini.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-bg-warning mb-3">
                        <div class="card-header">
                            <i class="fas fa-wallet"></i> Pendapatan Hari Ini
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-money-bill-wave"></i> Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h5>
                            <p class="card-text">Pendapatan dari transaksi hari ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pendapatan 7 Hari Terakhir -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-calendar-day"></i> Pendapatan 7 Hari Terakhir
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pendapatan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_pendapatan = "
                                SELECT DATE(TanggalPenjualan) AS tanggal, COALESCE(SUM(TotalHarga), 0) AS pendapatan
                                FROM penjualan
                                WHERE TanggalPenjualan >= CURDATE() - INTERVAL 6 DAY
                                GROUP BY DATE(TanggalPenjualan)
                                ORDER BY tanggal ASC
                            ";
                            $result = $conn->query($sql_pendapatan);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . date('d M Y', strtotime($row['tanggal'])) . "</td>
                                        <td>Rp " . number_format($row['pendapatan'], 0, ',', '.') . "</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>