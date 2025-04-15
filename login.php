<?php
// Mulai session untuk menyimpan data login
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Pengaturan karakter dan viewport untuk responsive -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Judul halaman -->
    <title>Kasir Zamzxy._</title>

    <!-- Link ke file CSS untuk styling -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Favicon untuk tab browser -->
    <link rel="icon" href="../asset/favicon-16x16.png" type="image/x-icon">
</head>
<body>

<!-- Kontainer form login -->
<div class="login-form">
    <!-- Logo aplikasi -->
    <img src="../asset/login.png" alt="Logo">

    <!-- Judul form -->
    <h2 class="title">login</h2>

    <!-- Form login, data dikirim ke login_proses.php dengan metode POST -->
    <form action="../config/login_proses.php" method="POST">
        
        <!-- Input username -->
        <div class="input-container">
            <input type="text" id="username" name="username" required>
            <label for="username">Username</label>
            <div class="underline"></div> <!-- Garis bawah animasi (jika ada di CSS) -->
        </div>

        <!-- Input password -->
        <div class="input-container">
            <input type="password" id="password" name="password" required>
            <label for="password">Password</label>
            <div class="underline"></div> <!-- Garis bawah animasi -->
        </div>

        <!-- Tombol login -->
        <button type="submit" class="login-button">Login</button>

        <!-- Tombol cancel, kembali ke halaman utama (/) -->
        <button type="button" class="cancel-button" onclick="window.location.href='/';">Cancel</button>
    </form>
</div>

</body>
</html>
