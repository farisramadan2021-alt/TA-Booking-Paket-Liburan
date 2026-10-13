
<?php
require_once __DIR__ . "/../../config/koneksi.php";

$query = mysqli_query($conn, "
    SELECT
        p.*,
        b.id_user,
        u.nama AS nama_pengguna,
        w.nama_paket
    FROM pembayaran p
    LEFT JOIN booking b ON p.id_booking = b.id_booking
    LEFT JOIN `user` u ON b.id_user = u.id_user
    LEFT JOIN paket_wisata w ON b.id_paket = w.id_paket
    ORDER BY p.id_pembayaran DESC
");

if (!$query) {
    die("Gagal mengambil data pembayaran: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - RPBNusa</title>

    <link rel="stylesheet"
        href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../dashboard.php">
            RPBNusa
        </a>

        <div>
            <a href="../dashboard.php" class="btn btn-light btn-sm">
                Dashboard
            </a>
            <a href="../../logout.php" class="btn btn-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <h2 class="fw-bold">Data Pembayaran</h2>
    <p class="text-muted mb-4">
        Periksa bukti pembayaran dan status transaksi pelanggan.
    </p>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Pengguna</th>
                            <th>Paket Wisata</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th>Jumlah Bayar</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;

                    if (mysqli_num_rows($query) > 0):
                        while ($p = mysqli_fetch_assoc($query)):
                            $status = $p['status_pembayaran'] ?? 'Menunggu';
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($p['nama_pengguna'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['nama_paket'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['tanggal_bayar'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['metode_pembayaran'] ?? '-'); ?>
                            </td>

                            <td>
                                Rp <?= number_format((float) $p['jumlah_bayar'], 0, ',', '.'); ?>
                            </td>

                            <td>
                                <?php if (!empty($p['bukti_pembayaran'])): ?>
                                    <a href="../../uploads/<?= rawurlencode(basename($p['bukti_pembayaran'])); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-info btn-sm">
                                        Lihat Bukti
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada bukti</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="badge <?= $status === 'Terverifikasi' ? 'bg-success' : ($status === 'Ditolak' ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                                    <?= htmlspecialchars($status); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($status === 'Menunggu'): ?>
                                    <a href="verifikasi.php?id=<?= (int) $p['id_pembayaran']; ?>"
                                       class="btn btn-success btn-sm mb-1"
                                       onclick="return confirm('Verifikasi pembayaran ini?')">
                                        Verifikasi
                                    </a>

                                    <a href="tolak.php?id=<?= (int) $p['id_pembayaran']; ?>"
                                       class="btn btn-danger btn-sm mb-1"
                                       onclick="return confirm('Tolak pembayaran ini?')">
                                        Tolak
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                Belum ada data pembayaran.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>

            <a href="../dashboard.php" class="btn btn-secondary">
                Kembali
            </a>

        </div>
    </div>
</div>

<script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
