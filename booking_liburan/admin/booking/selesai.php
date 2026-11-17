<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

// Periksa akses admin
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../login/login.php");
    exit;
}

// Pastikan menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Validasi ID booking
$id_booking = filter_input(
    INPUT_POST,
    'id_booking',
    FILTER_VALIDATE_INT
);

if (!$id_booking || $id_booking <= 0) {
    header("Location: index.php");
    exit;
}

// Ubah status hanya jika sebelumnya Dikonfirmasi
$stmt = mysqli_prepare($conn, "
    UPDATE booking
    SET status_booking = 'Selesai'
    WHERE id_booking = ?
    AND status_booking = 'Dikonfirmasi'
");

mysqli_stmt_bind_param($stmt, "i", $id_booking);
mysqli_stmt_execute($stmt);

$berhasil = mysqli_stmt_affected_rows($stmt) === 1;

mysqli_stmt_close($stmt);

// Kembali ke halaman booking
if ($berhasil) {
    header("Location: index.php?pesan=selesai");
} else {
    header("Location: index.php?pesan=gagal");
}
exit;