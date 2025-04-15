<?php
// Memulai session
session_start();

// Cek apakah user sudah login, jika belum arahkan ke dashboard
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Include koneksi database
include '../config/koneksi.php';

// Proses form jika metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $barcode = $_POST['barcode'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Query untuk memasukkan data produk ke database dengan prepared statement
    $sql = "INSERT INTO produk (Barcode, NamaProduk, Harga, Stok) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // Binding parameter: s = string, i = integer
    $stmt->bind_param("ssii", $barcode, $nama_produk, $harga, $stok);

    // Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, arahkan kembali ke halaman data barang dengan pesan sukses
        header('Location: data_barang.php?pesan=berhasil');
        exit();
    } else {
        // Jika gagal, arahkan kembali ke form tambah dengan pesan gagal
        header('Location: tambah_data_barang.php?pesan=gagal');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Pengaturan karakter dan tampilan responsif -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../asset/favicon-16x16.png" type="image/x-icon">

    <!-- Link ke Bootstrap CSS untuk tampilan yang rapi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Tambah Produk</h2>

    <!-- Tampilkan pesan berhasil atau gagal -->
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil') : ?>
        <div class="alert alert-success">✅ Produk berhasil ditambahkan.</div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') : ?>
        <div class="alert alert-danger">❌ Gagal menambahkan produk. Silakan periksa input dan coba lagi.</div>
    <?php endif; ?>

    <!-- Form untuk input data produk -->
    <form method="POST">
        <!-- Input barcode -->
        <div class="mb-3">
            <label for="barcode" class="form-label">Barcode</label>
            <input type="number" class="form-control" id="barcode" name="barcode" required>
        </div>

        <!-- Input nama produk -->
        <div class="mb-3">
            <label for="nama_produk" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
        </div>

        <!-- Input harga produk -->
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" class="form-control" id="harga" name="harga" required>
        </div>

        <!-- Input stok produk -->
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" required>
        </div>

        <!-- Tombol submit dan kembali -->
        <button type="submit" class="btn btn-primary">Tambah Produk</button>
        <a href="data_barang.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
