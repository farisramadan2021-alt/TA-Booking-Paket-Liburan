<?php
require_once 'config/koneksi.php';

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$nama = "Administrator";

$stmt = $conn->prepare(
    "INSERT INTO admin (username, password, nama)
     VALUES (?, ?, ?)"
);

$stmt->bind_param("sss", $username, $password, $nama);

if ($stmt->execute()) {
    echo "Akun admin berhasil dibuat!";
} else {
    echo "Gagal membuat akun: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>