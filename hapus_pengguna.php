<?php
// Mulai session untuk memeriksa apakah pengguna sudah login
session_start();

// Jika pengguna belum login, arahkan ke halaman dashboard (bisa kamu ubah jadi login.php kalau perlu)
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Sertakan file koneksi database
include '../config/koneksi.php';

// Periksa apakah ada parameter userID yang dikirim lewat URL
if (isset($_GET['userID'])) {
    $id = $_GET['userID']; // Ambil nilai userID dari URL

    // Cegah agar admin utama (misalnya userID = 6) tidak bisa dihapus
    if ($id == 6) {
        echo "<script>alert('Admin utama tidak dapat dihapus!'); window.location='kelola-user.php';</script>";
        exit();
    }

    // Query untuk menghapus user berdasarkan userID
    $sql = "DELETE FROM user WHERE userID = ?";
    $stmt = $conn->prepare($sql); // Siapkan query menggunakan prepared statement untuk keamanan
    $stmt->bind_param("i", $id); // Bind parameter (i = integer)

    // Eksekusi query dan cek apakah berhasil
    if ($stmt->execute()) {
        echo "<script>alert('Pengguna berhasil dihapus!'); window.location='kelola-user.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengguna!'); window.location='kelola-user.php';</script>";
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close();
} else {
    // Jika tidak ada userID di URL, tampilkan pesan error
    echo "<script>alert('UserID tidak ditemukan!'); window.location='kelola-user.php';</script>";
}
?>
