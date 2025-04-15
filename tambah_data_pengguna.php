<?php
// Memulai sesi untuk mengecek autentikasi pengguna
session_start();

// Jika belum login, arahkan ke halaman dashboard
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Menghubungkan ke database
include '../config/koneksi.php';

// Mengecek apakah request berasal dari metode POST (form disubmit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil input dari form
    $username = $_POST['username'];
    
    // Password di-hash terlebih dahulu agar aman sebelum disimpan ke database
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $role = $_POST['role']; // Mengambil role dari form (admin/petugas)

    // Query untuk memasukkan data user baru ke tabel `user`
    $sql = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Binding parameter: semua string (s = string)
    $stmt->bind_param("sss", $username, $password, $role);

    // Mengeksekusi query, tampilkan alert sesuai hasilnya
    if ($stmt->execute()) {
        echo "<script>alert('Pengguna berhasil ditambahkan!'); window.location='kelola-user.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan pengguna!'); window.location='tambah_data_pengguna.php';</script>";
    }

    // Menutup statement dan koneksi database
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Pengaturan dasar HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>

    <!-- Favicon kecil di tab browser -->
    <link rel="icon" href="../asset/favicon-16x16.png" type="image/x-icon">

    <!-- Bootstrap untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Container utama -->
<div class="container mt-5">
    <h2 class="mb-4">Tambah Pengguna</h2>

    <!-- Form input user baru -->
    <form method="POST">
        <!-- Input Username -->
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <!-- Input Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Pilihan Role -->
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
            </select>
        </div>

        <!-- Tombol submit dan kembali -->
        <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
        <a href="kelola-user.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
