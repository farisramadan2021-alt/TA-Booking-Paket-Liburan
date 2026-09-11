<?php

require_once __DIR__ . "/../../config/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query(
    $conn,
    "SELECT gambar FROM paket_wisata WHERE id_paket = '$id'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data paket wisata tidak ditemukan.";
    exit;
}

if (!empty($data['gambar'])) {

    $folder = "../../uploads/paket_wisata/";
    $file = $folder . $data['gambar'];
    if (file_exists($file)) {
        unlink($file);
    }
}
$query_hapus = mysqli_query(
    $conn,
    "DELETE FROM paket_wisata WHERE id_paket = '$id'"
);
if ($query_hapus) {
    header("Location: index.php");
    exit;
} else {
    echo "Gagal menghapus paket: " . mysqli_error($conn);
}
?>