<?php
require_once __DIR__ . "/../../config/koneksi.php";

$query = mysqli_query($conn, "SELECT * FROM paket_wisata ORDER BY id_paket DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket Wisata</title>
    <link rel="stylesheet" href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Kelola Paket Wisata</h2>
                <p class="text-muted mb-0">
                    Kelola data paket wisata yang tersedia
                </p>
            </div>
            <a href="tambah.php" class="btn btn-primary">
                + Tambah Paket Wisata
            </a>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Paket</th>
                                <th>Destinasi</th>
                                <th>Durasi</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td>
                                        <?= $no++; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($data['gambar'])): ?>
                                            <img
                                                src="../../uploads/paket_wisata/<?= htmlspecialchars($data['gambar']); ?>"
                                                alt="<?= htmlspecialchars($data['nama_paket']); ?>"
                                                width="100"
                                                height="70"
                                                class="rounded"
                                                style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">
                                                Tidak ada gambar
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold">
                                        <?= htmlspecialchars($data['nama_paket']); ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($data['destinasi']); ?>
                                    </td>
                                    <td>
                                        <?= $data['durasi']; ?> Hari
                                    </td>
                                    <td class="text-nowrap">
                                        Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($data['deskripsi']); ?>
                                    </td>
                                    <td>
                                        <?php if ($data['status'] == 'Aktif'): ?>
                                            <span class="badge text-bg-success">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">
                                                <?= htmlspecialchars($data['status']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a
                                            href="edit.php?id=<?= $data['id_paket']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <a
                                            href="hapus.php?id=<?= $data['id_paket']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus paket wisata ini?');">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>