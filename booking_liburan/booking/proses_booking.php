<?php
session_start();

require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_paket       = (int) $_POST['id_paket'];
    $nama           = mysqli_real_escape_string($conn, $_POST['nama']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $tlp            = mysqli_real_escape_string($conn, $_POST['tlp']);
    $tanggal        = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah_peserta = (int) $_POST['jumlah_peserta'];

    // Validasi data
    if (
        $id_paket <= 0 ||
        empty($nama) ||
        empty($email) ||
        empty($tlp) ||
        empty($tanggal) ||
        $jumlah_peserta <= 0
    ) {
        echo "<script>
                alert('Data booking belum lengkap.');
                window.history.back();
              </script>";
        exit();
    }

    $query_paket = mysqli_query(
        $conn,
        "SELECT * FROM paket_wisata WHERE id_paket = $id_paket"
    );

    $paket = mysqli_fetch_assoc($query_paket);

    if (!$paket) {
        echo "<script>
                alert('Paket wisata tidak ditemukan.');
                window.history.back();
              </script>";
        exit();
    }

    $harga = $paket['harga'];

// Hitung total harga
$total_harga = $harga * $jumlah_peserta;

    $id_user = 1;

    $status = 'Menunggu';

    $query_simpan = mysqli_query(
        $conn,
        "INSERT INTO booking
        (
            id_user,
            id_paket,
            tanggal_booking,
            tanggal_keberangkatan,
            jumlah_peserta,
            total_harga,
            status_booking
        )
        VALUES
        (
            '$id_user',
            '$id_paket',
            NOW(),
            '$tanggal',
            '$jumlah_peserta',
            '$total_harga',
            '$status'
        )"
    );

    if ($query_simpan) {

        echo "<script>
                alert('Pemesanan berhasil disimpan ke database.');
                window.location.href = 'formBooking.php?id=$id_paket';
              </script>";

    } else {

        echo "<script>
                alert('Gagal menyimpan booking: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }

} else {

    header('Location: formBooking.php');
    exit();
}
?>