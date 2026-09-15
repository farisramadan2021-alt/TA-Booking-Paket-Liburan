<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_paket       = mysqli_real_escape_string($conn, $_POST['id_paket']);
    $nama           = mysqli_real_escape_string($conn, $_POST['nama']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $tlp            = mysqli_real_escape_string($conn, $_POST['tlp']);
    $tanggal        = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah_peserta = (int) $_POST['jumlah_peserta']; // Pastikan angkanya bulat

    // VALIDASI SERVER: Cek kalau ada inputan yang kosong atau jumlah pesertanya ngawur (<= 0)
    if (empty($id_paket) || empty($nama) || empty($email) || empty($tlp) || empty($tanggal) || $jumlah_peserta <= 0) {
        echo "<script>
                alert('Waduh, datanya belum lengkap bro! Isi semua dulu ya.');
                window.history.back();
              </script>";
        exit(); 
    }

    $query_harga = mysqli_query($conn, "SELECT harga FROM paket_wisata WHERE id_paket = $id_paket");
    $data_paket  = mysqli_fetch_assoc($query_harga);
    $harga       = $data_paket['harga']; 

    $total_harga = $harga * $jumlah_peserta;

    $id_user   = 1; 
    $id_jadwal = 1; 
    $status    = 'Menunggu';

    $query_simpan = mysqli_query($conn, "INSERT INTO booking (id_user, id_jadwal, jumlah_peserta, total_harga, status_booking) 
                        VALUES ('$id_user', '$id_jadwal', '$jumlah_peserta', '$total_harga', '$status')");
    
    if ($query_simpan) {
        echo "<script>
                alert('Mantap! Pemesanan berhasil disimpan ke database.');
                window.location.href = 'formBooking.php?id=$id_paket';
              </script>";
    } else {
        echo "<script>
                alert('Yah, gagal nyimpen data. Coba cek kodingannya lagi.');
                window.history.back();
              </script>";
    }

} else {
    header('Location: formBooking.php');
    exit();
}
?>