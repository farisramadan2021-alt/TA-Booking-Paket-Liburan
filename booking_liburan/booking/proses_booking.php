<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'post') {
    $id_paket = $_POST['id_paket'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $tlp = $_POST['tlp'];
    $tanggal = $_POST['tanggal'];
    $jumlah_peserta = $_POST['jumlah_peserta'];
} else {
    header('location: formBooking.php');
    exit();
}


?>