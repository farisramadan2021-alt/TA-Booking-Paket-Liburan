
<?php
require_once __DIR__ . "/../../config/koneksi.php";

// Pastikan ID booking tersedia
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_booking = (int) $_GET['id'];

// Ubah status booking yang masih Menunggu
$stmt = mysqli_prepare(
    $conn,
    "UPDATE booking
     SET status_booking = 'Dikonfirmasi'
     WHERE id_booking = ? AND status_booking = 'Menunggu'"
);

mysqli_stmt_bind_param($stmt, "i", $id_booking);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// Kembali ke halaman Data Booking
header("Location: index.php");
exit;
?>