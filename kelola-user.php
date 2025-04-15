<?php
// Mulai session untuk mengecek apakah user sudah login
session_start();

// Jika belum login, redirect ke dashboard atau bisa diganti ke login.php
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Sertakan file koneksi ke database
include '../config/koneksi.php';

// Ambil semua data dari tabel user
$sql = "SELECT userID, username, password, role FROM user";
$result = $conn->query($sql);

// Periksa apakah query berhasil dieksekusi
if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <!-- CSS lokal dan Bootstrap -->
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../asset/favicon-16x16.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="row">
        <!-- Sidebar ditampilkan di sini -->
        <?php include 'sidebar.php'; ?>

        <!-- Konten utama -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header halaman -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Data Pengguna</h1>
            </div>

            <div class="container mt-4">
                <!-- Tombol tambah pengguna dan kembali -->
                <a href="tambah_data_pengguna.php" class="btn btn-primary mb-3">Tambah Pengguna</a>
                <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali</a>

                <!-- Tabel data pengguna -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1; // Untuk penomoran tabel
                            if ($result->num_rows > 0) {
                                // Loop semua data pengguna
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>********</td>"; // Jangan tampilkan password (meskipun sudah di-hash)
                                    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                    echo "<td>
                                            <!-- Tombol Edit dan Hapus -->
                                            <a href='edit_pengguna.php?userID=" . $row['userID'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                            <a href='hapus_pengguna.php?userID=" . $row['userID'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus pengguna ini?\")'>Hapus</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                // Jika tidak ada data pengguna
                                echo "<tr><td colspan='5'>Tidak ada data pengguna.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>

