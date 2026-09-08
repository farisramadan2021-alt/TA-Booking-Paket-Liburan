<?php
require_once __DIR__ . "/../../config/koneksi.php";

if (isset($_POST['simpan'])) {

    $nama_paket = $_POST['nama_paket'];
    $destinasi = $_POST['destinasi'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    $folder = "../../uploads/paket_wisata/";

    if (!empty($gambar)) {
        move_uploaded_file($tmp, $folder . $gambar);
    }

    $query = mysqli_query($conn, "INSERT INTO paket_wisata
        (nama_paket, destinasi, durasi, harga, deskripsi, gambar, status)
        VALUES
        ('$nama_paket', '$destinasi', '$durasi', '$harga', '$deskripsi', '$gambar', '$status')
    ");

    if ($query) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menambahkan paket: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Paket Wisata</title>
    <link rel="stylesheet"
        href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Tambah Paket Wisata
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST"
                          enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">
                                Nama Paket
                            </label>
                            <input type="text"
                                   name="nama_paket"
                                   class="form-control"
                                   placeholder="Masukan nama paket wisata"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                Destinasi
                            </label>
                            <input type="text"
                                   name="destinasi"
                                   class="form-control"
                                   placeholder="Masukan destinasi"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                Durasi (Hari)
                            </label>
                            <input type="number"
                                   name="durasi"
                                   class="form-control"
                                   min="1"
                                   placeholder="Contoh: 2"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                Harga
                            </label>
                            <input type="number"
                                   name="harga"
                                   class="form-control"
                                   min="0"
                                   placeholder="Contoh: 10000"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Deskripsi
                            </label>

                            <textarea name="deskripsi"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan deskripsi paket wisata"></textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Gambar Paket
                            </label>

                            <input type="file"
                                   name="gambar"
                                   class="form-control"
                                   accept="image/*">

                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select">
                                <option value="Aktif">
                                    Aktif
                                </option>
                                <option value="Nonaktif">
                                    Nonaktif
                                </option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan
                            </button>
                            <a href="index.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>