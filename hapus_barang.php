<?php
// hapus_barang.php

// Mulai session untuk cek login
session_start();

// Jika belum login (session username belum diset), redirect ke dashboard
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Include koneksi ke database
include '../config/koneksi.php';

// Cek apakah parameter ProdukID dikirim melalui URL
if (isset($_GET['ProdukID'])) {
    $id = $_GET['ProdukID']; // Ambil ProdukID dari URL

    // Query untuk menghapus data produk berdasarkan ProdukID
    $sql = "DELETE FROM produk WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql);               // Siapkan statement SQL
    $stmt->bind_param("i", $id);                // Bind parameter id sebagai integer

    // Eksekusi statement
    if ($stmt->execute()) {
        // Jika berhasil, tampilkan alert dan redirect ke halaman data_barang
        echo "<script>alert('Produk berhasil dihapus!'); window.location='data_barang.php';</script>";
    } else {
        // Jika gagal, tampilkan pesan gagal
        echo "<script>alert('Gagal menghapus produk!'); window.location='data_barang.php';</script>";
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close();
}
?>
