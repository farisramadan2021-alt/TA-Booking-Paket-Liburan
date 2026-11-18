<?php
session_start();

require_once '../config/koneksi.php';

// ===============================
// CEK LOGIN USER
// ===============================
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// ===============================
// CEK METODE REQUEST
// ===============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: riwayat_booking.php");
    exit;
}

$id_booking = filter_input(INPUT_POST, 'id_booking', FILTER_VALIDATE_INT);

$metode = $_POST['metode_pembayaran'] ?? '';

$metodeDiizinkan = [
    'Transfer Bank',
    'E-Wallet',
    'Dana',
    'Gopay'
];

if (!$id_booking || !in_array($metode, $metodeDiizinkan, true)) {
    exit("Data pembayaran tidak valid.");
}

// ===============================
// CEK BOOKING MILIK USER
// ===============================
$stmt = mysqli_prepare($conn, "
    SELECT
        id_booking,
        total_harga,
        status_booking
    FROM booking
    WHERE id_booking = ?
      AND id_user = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "ii", $id_booking, $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$booking) {
    exit("Booking tidak ditemukan atau bukan milik akun kamu.");
}

if ($booking['status_booking'] !== 'Dikonfirmasi') {
    exit("Booking belum dikonfirmasi oleh admin.");
}

// ===============================
// CEK PEMBAYARAN SEBELUMNYA
// ===============================
$stmt = mysqli_prepare($conn, "
    SELECT status_pembayaran
    FROM pembayaran
    WHERE id_booking = ?
    ORDER BY id_pembayaran DESC
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "i", $id_booking);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$pembayaranLama = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (
    $pembayaranLama &&
    in_array(
        $pembayaranLama['status_pembayaran'],
        ['Menunggu', 'Terverifikasi'],
        true
    )
) {
    header("Location: Pembayaran.php?id=" . $id_booking);
    exit;
}

// ===============================
// CEK FILE BUKTI PEMBAYARAN
// ===============================
if (
    !isset($_FILES['bukti_pembayaran']) ||
    $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK
) {
    exit("Bukti pembayaran wajib diunggah.");
}

$file = $_FILES['bukti_pembayaran'];

if ($file['size'] > 5 * 1024 * 1024) {
    exit("Ukuran file maksimal 5 MB.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$jenisFile = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'application/pdf' => 'pdf'
];

if (!isset($jenisFile[$mime])) {
    exit("Format file harus JPG, PNG, atau PDF.");
}

// ===============================
// SIMPAN FILE
// ===============================
$folderUpload = __DIR__ . '/../uploads/bukti_pembayaran/';

if (!is_dir($folderUpload)) {
    if (!mkdir($folderUpload, 0755, true)) {
        exit("Folder upload tidak dapat dibuat.");
    }
}

$namaFile = 'bukti_' . $id_booking . '_' .
    bin2hex(random_bytes(8)) . '.' . $jenisFile[$mime];

$tujuanFile = $folderUpload . $namaFile;

// Lokasi yang disimpan ke database
$lokasiDatabase = 'uploads/bukti_pembayaran/' . $namaFile;

if (!move_uploaded_file($file['tmp_name'], $tujuanFile)) {
    exit("Gagal mengunggah bukti pembayaran.");
}

// ===============================
// SIMPAN DATA PEMBAYARAN
// ===============================
$jumlahBayar = (float) $booking['total_harga'];
$statusPembayaran = 'Menunggu';

$stmt = mysqli_prepare($conn, "
    INSERT INTO pembayaran (
        id_booking,
        metode_pembayaran,
        jumlah_bayar,
        bukti_pembayaran,
        status_pembayaran
    ) VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    unlink($tujuanFile);
    exit("Gagal menyiapkan penyimpanan pembayaran.");
}

mysqli_stmt_bind_param(
    $stmt,
    "isdss",
    $id_booking,
    $metode,
    $jumlahBayar,
    $lokasiDatabase,
    $statusPembayaran
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    // Kembali ke halaman pembayaran
    header("Location: Pembayaran.php?id=" . $id_booking);
    exit;
}

mysqli_stmt_close($stmt);

// Hapus file jika penyimpanan database gagal
if (file_exists($tujuanFile)) {
    unlink($tujuanFile);
}

exit("Pembayaran gagal disimpan. Silakan periksa kembali database.");
?>