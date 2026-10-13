
<?php
require_once __DIR__ . "/../../config/koneksi.php";

$query = mysqli_query($conn, "
    SELECT
        b.*,
        u.nama AS nama_pengguna,
        p.nama_paket
    FROM booking b
    LEFT JOIN `user` u ON b.id_user = u.id_user
    LEFT JOIN paket_wisata p ON b.id_paket = p.id_paket
    ORDER BY b.id_booking DESC
");

if (!$query) {
    die("Gagal mengambil data booking: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Booking - RPBNusa</title>

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

    <h2 class="fw-bold">Data Booking</h2>
    <p class="text-muted mb-4">
        Kelola pemesanan paket liburan pelanggan.
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
                            <th>Tanggal Booking</th>
                            <th>Tanggal Keberangkatan</th>
                            <th>Jumlah Peserta</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;

                    if (mysqli_num_rows($query) > 0):
                        while ($b = mysqli_fetch_assoc($query)):
                            $status = $b['status_booking'] ?? 'Menunggu';

                            $badge = 'bg-warning text-dark';

                            if ($status === 'Dikonfirmasi') {
                                $badge = 'bg-success';
                            } elseif ($status === 'Ditolak' || $status === 'Dibatalkan') {
                                $badge = 'bg-danger';
                            }
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($b['nama_pengguna'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($b['nama_paket'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($b['tanggal_booking'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($b['tanggal_keberangkatan'] ?? '-'); ?>
                            </td>

                            <td><?= (int) $b['jumlah_peserta']; ?></td>

                            <td>
                                Rp <?= number_format((float) $b['total_harga'], 0, ',', '.'); ?>
                            </td>

                            <td>
                                <span class="badge <?= $badge ?>">
                                    <?= htmlspecialchars($status); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($status === 'Menunggu'): ?>

                                    <a href="konfirmasi.php?id=<?= (int) $b['id_booking']; ?>"
                                       class="btn btn-success btn-sm mb-1"
                                       onclick="return confirm('Konfirmasi booking ini?')">
                                        Konfirmasi
                                    </a>

                                    <a href="tolak.php?id=<?= (int) $b['id_booking']; ?>"
                                       class="btn btn-danger btn-sm mb-1"
                                       onclick="return confirm('Tolak booking ini?')">
                                        Tolak
                                    </a>

                                <?php else: ?>

                                    <span class="text-muted">Tidak ada aksi</span>

                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                Belum ada data booking.
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
