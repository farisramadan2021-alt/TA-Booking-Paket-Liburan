<?php
require_once __DIR__ . "/../../config/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM paket_wisata WHERE id_paket = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data paket wisata tidak ditemukan.";
    exit;
}

if (isset($_POST['update'])) {

    $nama_paket = $_POST['nama_paket'];
    $destinasi = $_POST['destinasi'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    $gambar_lama = $data['gambar'];

    if (!empty($_FILES['gambar']['name'])) {

        $gambar_baru = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];

        $folder = "../../uploads/paket_wisata/";

        move_uploaded_file($tmp, $folder . $gambar_baru);

        if (!empty($gambar_lama) && file_exists($folder . $gambar_lama)) {
            unlink($folder . $gambar_lama);
        }
        $query_update = mysqli_query($conn, "UPDATE paket_wisata SET
            nama_paket = '$nama_paket',
            destinasi = '$destinasi',
            durasi = '$durasi',
            harga = '$harga',
            deskripsi = '$deskripsi',
            gambar = '$gambar_baru',
            status = '$status'
            WHERE id_paket = '$id'
        ");
    } else {
        $query_update = mysqli_query($conn, "UPDATE paket_wisata SET
            nama_paket = '$nama_paket',
            destinasi = '$destinasi',
            durasi = '$durasi',
            harga = '$harga',
            deskripsi = '$deskripsi',
            status = '$status'
            WHERE id_paket = '$id'
        ");
    }
    if ($query_update) {

        header("Location: index.php");
        exit;
    } else {

        echo "Gagal mengupdate paket: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Edit Paket Wisata</title>
    <link rel="stylesheet"
        href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">
                            Edit Paket Wisata
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Paket
                                </label>
                                <input
                                    type="text"
                                    name="nama_paket"
                                    class="form-control"
                                    value="<?= htmlspecialchars($data['nama_paket']); ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Destinasi
                                </label>
                                <input
                                    type="text"
                                    name="destinasi"
                                    class="form-control"
                                    value="<?= htmlspecialchars($data['destinasi']); ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Durasi (Hari)
                                </label>
                                <input
                                    type="number"
                                    name="durasi"
                                    class="form-control"
                                    min="1"
                                    value="<?= $data['durasi']; ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Harga
                                </label>
                                <input
                                    type="number"
                                    name="harga"
                                    class="form-control"
                                    min="0"
                                    value="<?= $data['harga']; ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Deskripsi
                                </label>
                                <textarea
                                    name="deskripsi"
                                    class="form-control"
                                    rows="4"><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Gambar Saat Ini
                                </label>
                                <br>
                                <?php if (!empty($data['gambar'])): ?>
                                    <img
                                        src="../../uploads/paket_wisata/<?= htmlspecialchars($data['gambar']); ?>"
                                        width="150"
                                        height="100"
                                        class="rounded mb-2"
                                        style="object-fit: cover;">
                                <?php else: ?>
                                    <p class="text-muted">
                                        Tidak ada gambar
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Ganti Gambar
                                </label>
                                <input
                                    type="file"
                                    name="gambar"
                                    class="form-control"
                                    accept="image/*">
                                <div class="form-text">
                                    Kosongkan jika tidak ingin mengganti gambar.
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">
                                    Status
                                </label>
                                <select
                                    name="status"
                                    class="form-select">
                                    <option value="Aktif"
                                        <?= $data['status'] == 'Aktif' ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>
                                    <option value="Nonaktif"
                                        <?= $data['status'] == 'Nonaktif' ? 'selected' : ''; ?>>
                                        Nonaktif
                                    </option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    name="update"
                                    class="btn btn-warning">
                                    Simpan Perubahan
                                </button>
                                <a
                                    href="index.php"
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