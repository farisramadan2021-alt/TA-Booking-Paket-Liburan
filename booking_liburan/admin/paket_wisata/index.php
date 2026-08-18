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

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f5f7fa;
        }

        h2 {
            color: #333;
        }

        .btn-tambah {
            display: inline-block;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #007bff;
            color: white;
        }

        .edit {
            background: #ffc107;
            color: black;
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .hapus {
            background: #dc3545;
            color: white;
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
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