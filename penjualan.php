<?php
session_start();
include '../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$tanggalTransaksi = date("Y-m-d H:i:s");

// Cek login dan role
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if (!isset($_SESSION['nama_kasir'])) {
    $_SESSION['nama_kasir'] = $_SESSION['username']; // Nama kasir dari session login
}

// Reset keranjang jika transaksi selesai
if (isset($_GET['selesai'])) {
    unset($_SESSION['keranjang'], $_SESSION['sudah_simpan'], $_SESSION['penjualan_id'], $_SESSION['uang_diberikan']);
    header("Location: penjualan.php");
    exit;
}

// Tambah produk ke keranjang dari barcode
if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];
    $query = mysqli_query($conn, "SELECT * FROM produk WHERE Barcode = '$barcode'");
    if ($produk = mysqli_fetch_assoc($query)) {
        $id = $produk['ProdukID'];
        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id]['Jumlah']++;
        } else {
            $_SESSION['keranjang'][$id] = [
                'ProdukID' => $id,
                'NamaProduk' => $produk['NamaProduk'],
                'Harga' => $produk['Harga'],
                'Jumlah' => 1
            ];
        }
    }
    header("Location: penjualan.php");
    exit;
}

// Hapus keranjang (khusus admin)
if (isset($_POST['hapus']) && $_SESSION['role'] === 'admin') {
    unset($_SESSION['keranjang'], $_SESSION['sudah_simpan'], $_SESSION['penjualan_id'], $_SESSION['uang_diberikan']);
    header("Location: penjualan.php");
    exit;
}

// Hitung total
$totalHarga = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $totalHarga += $item['Harga'] * $item['Jumlah'];
}

$kembalian = 0;
$uang = $_POST['uang'] ?? 0;

// Simpan transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uang']) && !isset($_SESSION['sudah_simpan'])) {
    if ($uang >= $totalHarga) {
        mysqli_query($conn, "INSERT INTO penjualan (TanggalPenjualan, TotalHarga) VALUES (NOW(), $totalHarga)");
        $penjualanID = mysqli_insert_id($conn);

        foreach ($_SESSION['keranjang'] as $item) {
            $produkID = $item['ProdukID'];
            $jumlah = $item['Jumlah'];
            $subtotal = $item['Harga'] * $jumlah;
            mysqli_query($conn, "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal)
                VALUES ($penjualanID, $produkID, $jumlah, $subtotal)");
        }

        $_SESSION['sudah_simpan'] = true;
        $_SESSION['penjualan_id'] = $penjualanID;
        $_SESSION['uang_diberikan'] = $uang;
        $kembalian = $uang - $totalHarga;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penjualan</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../asset/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * { visibility: hidden; }
            .struk-print, .struk-print * { visibility: visible; }
            .struk-print {
                position: absolute; left: 0; top: 0; width: 100%;
                background: white; padding: 20px;
            }
            .btn, .form-control, input, textarea, .form, form {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="row">
    <?php include 'sidebar.php'; ?>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container mt-4">
            <h2>Penjualan</h2>

            <!-- Ringkasan pendapatan (admin saja) -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <?php
                $hari_ini = date('Y-m-d');
                $sql_total = mysqli_query($conn, "SELECT SUM(TotalHarga) AS TotalPendapatan FROM penjualan WHERE DATE(TanggalPenjualan) = '$hari_ini'");
                $data_total = mysqli_fetch_assoc($sql_total);
                ?>
                <div class="alert alert-info">
                    <strong>Total Pendapatan Hari Ini:</strong> Rp <?= number_format($data_total['TotalPendapatan'] ?? 0) ?>
                </div>
            <?php endif; ?>

            <!-- Form input barcode -->
            <form method="post" class="mb-3">
                <label for="barcode">Scan Barcode:</label>
                <input type="text" name="barcode" class="form-control" autofocus required>
            </form>

            <!-- Tabel keranjang -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['keranjang'] as $item): ?>
                        <tr>
                            <td><?= $item['NamaProduk'] ?></td>
                            <td>Rp <?= number_format($item['Harga']) ?></td>
                            <td><?= $item['Jumlah'] ?></td>
                            <td>Rp <?= number_format($item['Harga'] * $item['Jumlah']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total: Rp <?= number_format($totalHarga) ?></strong></p>

            <!-- Form transaksi -->
            <form method="post">
                <div class="mb-2">
                    <label for="uang">Uang Diberikan:</label>
                    <input type="number" name="uang" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan & Hitung Kembalian</button>

                <!-- Hanya admin bisa hapus keranjang -->
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <button type="submit" name="hapus" class="btn btn-danger">Hapus Keranjang</button>
                <?php endif; ?>
            </form>

            <!-- Struk -->
            <?php if (isset($_SESSION['sudah_simpan'])): ?>
                <div class="alert alert-success mt-4 struk-print">
                    <div class="text-center">
                        <img src="../asset/jkt48.png" alt="Logo" style="height: 40px;"><br>
                        <strong>Struk Penjualan</strong>
                    </div>
                    <p><strong>Tanggal:</strong> <?= date("d-m-Y H:i:s") ?><br>
                    <strong>Kasir:</strong> <?= $_SESSION['nama_kasir'] ?></p>

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['keranjang'] as $item): ?>
                                <tr>
                                    <td><?= $item['NamaProduk'] ?></td>
                                    <td>Rp <?= number_format($item['Harga']) ?></td>
                                    <td><?= $item['Jumlah'] ?></td>
                                    <td>Rp <?= number_format($item['Harga'] * $item['Jumlah']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p><strong>Total: Rp <?= number_format($totalHarga) ?></strong><br>
                    <strong>Uang Diberikan: Rp <?= number_format($_SESSION['uang_diberikan']) ?></strong><br>
                    <strong>Kembalian: Rp <?= number_format($kembalian) ?></strong></p>

                    <button class="btn btn-secondary" onclick="window.print()">Cetak Struk</button>
                    <a href="?selesai=1" class="btn btn-primary">Selesai</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
