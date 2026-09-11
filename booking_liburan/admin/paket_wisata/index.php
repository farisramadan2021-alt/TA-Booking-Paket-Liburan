<?php
include "../../config/koneksi.php";

$query = mysqli_query($conn, "SELECT * FROM paket_wisata ORDER BY id_paket DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket Wisata</title>
</head>

<body>

    <h2>Kelola Paket Wisata</h2>

    <a href="tambah.php" class="btn-tambah">
        + Tambah Paket Wisata
    </a>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Paket</th>
            <th>Destinasi</th>
            <th>Durasi</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Gambar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        while ($data = mysqli_fetch_assoc($query)) {
        ?>

            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($data['nama_paket']); ?></td>
                <td><?= htmlspecialchars($data['destinasi']); ?></td>
                <td><?= $data['durasi']; ?> Hari</td>
                <td>Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                <td><?= htmlspecialchars($data['deskripsi']); ?></td>
                <td>
                    <?php if (!empty($data['gambar'])): ?>
                        <img src="../../uploads/paket_wisata/<?= htmlspecialchars($data['gambar']); ?>"
                            width="100"
                            height="70"
                            style="object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                        Tidak ada gambar
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($data['status']); ?></td>

                <td>
                    <a href="edit.php?id=<?= $data['id_paket']; ?>" class="edit">
                        Edit
                    </a>

                    <a href="hapus.php?id=<?= $data['id_paket']; ?>"
                        class="hapus"
                        onclick="return confirm('Yakin ingin menghapus paket ini?');">
                        Hapus
                    </a>
                </td>
            </tr>

        <?php } ?>

    </table>

</body>

</html>