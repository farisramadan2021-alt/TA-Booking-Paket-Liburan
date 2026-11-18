<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

// ===============================
// CEK LOGIN ADMIN
// ===============================
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../login/login.php");
    exit;
}

// ===============================
// CEK ID PEMBAYARAN
// ===============================
$id_pembayaran = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id_pembayaran) {
    header("Location: index.php?pesan=id_tidak_valid");
    exit;
}

// ===============================
// CEK DATA PEMBAYARAN
// ===============================
$stmt = mysqli_prepare($conn, "
    SELECT id_pembayaran, status_pembayaran
    FROM pembayaran
    WHERE id_pembayaran = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "i", $id_pembayaran);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$pembayaran = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$pembayaran) {
    header("Location: index.php?pesan=data_tidak_ditemukan");
    exit;
}

// ===============================
// CEK STATUS PEMBAYARAN
// ===============================
if ($pembayaran['status_pembayaran'] !== 'Menunggu') {
    header("Location: index.php?pesan=status_sudah_diproses");
    exit;
}

// ===============================
// PROSES VERIFIKASI
// ===============================
$stmt = mysqli_prepare($conn, "
    UPDATE pembayaran
    SET status_pembayaran = 'Terverifikasi'
    WHERE id_pembayaran = ?
      AND status_pembayaran = 'Menunggu'
");

mysqli_stmt_bind_param($stmt, "i", $id_pembayaran);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) === 1) {
        mysqli_stmt_close($stmt);

        header("Location: index.php?pesan=berhasil_verifikasi");
        exit;
    }
}

mysqli_stmt_close($stmt);

header("Location: index.php?pesan=gagal_verifikasi");
exit;
?>