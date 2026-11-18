<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

// Cek apakah ada parameter ID di URL
if (!isset($_GET['id'])) {
    header("Location: riwayat_booking.php");
    exit;
}

$id_booking = intval($_GET['id']);
$id_user = intval($_SESSION['id_user']);

// Keamanan ekstra: Pastikan data booking ini benar-benar milik user yang sedang login
// dan statusnya hanya boleh dihapus jika sudah 'Ditolak' atau 'Selesai' (opsional sesuai aturan bisnis)
$stmt_check = mysqli_prepare(
    $conn,
    "SELECT id_booking, status_booking
     FROM booking
     WHERE id_booking = ?
     AND id_user = ?
     AND status_booking IN ('Ditolak', 'Selesai')"
);
mysqli_stmt_bind_param($stmt_check, "ii", $id_booking, $id_user);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$booking = mysqli_fetch_assoc($result_check);

if (!$booking) {
    echo "<script>
            alert('Data booking tidak ditemukan atau Anda tidak memiliki akses.');
            window.location.href = 'riwayat_booking.php';
          </script>";
    exit;
}

// Eksekusi penghapusan data dari database
$stmt_delete = mysqli_prepare(
    $conn,
    "DELETE FROM booking
     WHERE id_booking = ?
     AND id_user = ?
     AND status_booking IN ('Ditolak', 'Selesai')"
);

mysqli_stmt_bind_param($stmt_delete, "ii", $id_booking, $id_user);

if (mysqli_stmt_execute($stmt_delete)) {
    echo "<script>
            alert('Riwayat booking berhasil dihapus.');
            window.location.href = 'riwayat_booking.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus riwayat booking.');
            window.location.href = 'riwayat_booking.php';
          </script>";
}
exit;