<?php
// Mulai sesi untuk memastikan pengguna sudah login
session_start();

// Jika belum login, redirect ke dashboard (harusnya idealnya ke login.php)
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Koneksi ke database
include '../config/koneksi.php';

// Cek apakah userID dikirim lewat URL (GET)
if (isset($_GET['userID'])) {
    $id = $_GET['userID'];

    // Ambil data pengguna berdasarkan userID
    $sql = "SELECT * FROM user WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // i = integer
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Simpan data user dalam bentuk array asosiatif
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Ambil input username
    $role = $_POST['role'];         // Ambil input role (admin/petugas)

    // Jika password diisi (tidak kosong), maka update juga password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
        $sql = "UPDATE user SET username=?, password=?, role=? WHERE userID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        // Jika password tidak diisi, hanya update username dan role
        $sql = "UPDATE user SET username=?, role=? WHERE userID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $role, $id);
    }

    // Jalankan query update dan beri notifikasi
    if ($stmt->execute()) {
        echo "<script>alert('Pengguna berhasil diperbarui!'); window.location='kelola-user.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui pengguna!'); window.location='edit_pengguna.php?userID=$id';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <!-- Bootstrap untuk tampilan/form -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Pengguna</h2>

        <!-- Form untuk update data pengguna -->
        <form method="POST">

            <!-- Input Username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" 
                       value="<?php echo $user['username']; ?>" required>
            </div>

            <!-- Input Password (optional) -->
            <div class="mb-3">
                <label class="form-label">Password (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <!-- Dropdown Role -->
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required 
                        <?php echo ($user['userID'] == 1) ? 'disabled' : ''; ?>>
                    <!-- Jika role saat ini adalah admin, beri atribut selected -->
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <!-- Jika role saat ini adalah petugas, beri atribut selected -->
                    <option value="petugas" <?php echo ($user['role'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                </select>
            </div>

            <!-- Tombol Submit dan Batal -->
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="data_pengguna.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
