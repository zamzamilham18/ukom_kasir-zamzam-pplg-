<?php
// Memulai session untuk memeriksa apakah user sudah login
session_start();

// Jika belum login, arahkan kembali ke dashboard (seharusnya ke login.php, tapi sesuai alur kamu)
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Menghubungkan ke database
include '../config/koneksi.php';

// Ambil data produk berdasarkan ProdukID dari parameter GET
if (isset($_GET['ProdukID'])) {
    $id = $_GET['ProdukID'];
    $sql = "SELECT * FROM produk WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql); // Gunakan prepared statement untuk keamanan
    $stmt->bind_param("i", $id); // i = integer
    $stmt->execute();
    $result = $stmt->get_result();
    $produk = $result->fetch_assoc(); // Ambil data produk sebagai array asosiatif
}

// Proses form saat tombol submit ditekan (POST method)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];   // Ambil nama produk dari input form
    $harga = $_POST['harga']; // Ambil harga produk dari input form
    $stok = $_POST['stok'];   // Ambil stok produk dari input form

    // Update data produk berdasarkan ProdukID
    $sql = "UPDATE produk SET NamaProduk=?, Harga=?, Stok=? WHERE ProdukID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $nama, $harga, $stok, $id); // s = string, i = integer

    // Eksekusi query dan berikan notifikasi
    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='data_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui produk!'); window.location='data_barang.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <!-- Memasukkan Bootstrap untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Produk</h2>

        <!-- Form untuk mengedit data produk -->
        <form method="POST">
            <!-- Input Nama Produk -->
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="nama" class="form-control" 
                       value="<?php echo $produk['NamaProduk']; ?>" required>
            </div>

            <!-- Input Harga Produk -->
            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" name="harga" class="form-control" 
                       value="<?php echo $produk['Harga']; ?>" required>
            </div>

            <!-- Input Stok Produk -->
            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" 
                       value="<?php echo $produk['Stok']; ?>" required>
            </div>

            <!-- Tombol submit dan batal -->
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="data_barang.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
