<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

// Pastikan data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../user/paket_wisata/index.php");
    exit;
}

// Ambil data dari form
$id_user = (int) $_SESSION['id_user'];
$id_paket = isset($_POST['id_paket']) ? (int) $_POST['id_paket'] : 0;
$tanggal_keberangkatan = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$jumlah_peserta = isset($_POST['jumlah_peserta']) ? (int) $_POST['jumlah_peserta'] : 0;

// Validasi
if ($id_paket <= 0 || empty($tanggal_keberangkatan) || $jumlah_peserta <= 0) {
    echo "<script>
            alert('Data booking belum lengkap.');
            window.history.back();
          </script>";
    exit;
}

// Pastikan tanggal tidak boleh sebelum hari ini
if ($tanggal_keberangkatan < date('Y-m-d')) {
    echo "<script>
            alert('Tanggal keberangkatan tidak boleh sebelum hari ini.');
            window.history.back();
          </script>";
    exit;
}

// Ambil data paket
$query_paket = mysqli_query(
    $conn,
    "SELECT * FROM paket_wisata
     WHERE id_paket = $id_paket
     AND status = 'Aktif'"
);

$paket = mysqli_fetch_assoc($query_paket);

if (!$paket) {
    echo "<script>
            alert('Paket wisata tidak ditemukan atau sudah tidak aktif.');
            window.history.back();
          </script>";
    exit;
}

// Ambil harga paket
$harga = (float) $paket['harga'];

// Hitung total harga
$total_harga = $harga * $jumlah_peserta;

// Status awal booking
$status_booking = 'Menunggu';

// Simpan booking
$query_booking = mysqli_query(
    $conn,
    "INSERT INTO booking
    (
        id_user,
        id_paket,
        tanggal_keberangkatan,
        jumlah_peserta,
        total_harga,
        status_booking
    )
    VALUES
    (
        $id_user,
        $id_paket,
        '$tanggal_keberangkatan',
        $jumlah_peserta,
        $total_harga,
        '$status_booking'
    )"
);

if ($query_booking) {

    echo "<script>
            alert('Booking berhasil disimpan!');
            window.location.href = '../user/riwayat_booking.php';
          </script>";

} else {

    echo "<script>
            alert('Booking gagal disimpan: " . addslashes(mysqli_error($conn)) . "');
            window.history.back();
          </script>";
}
?>