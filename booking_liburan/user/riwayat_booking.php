
<?php
session_start();
require_once __DIR__ . "/../config/koneksi.php";

// Cek login pelanggan
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Ambil riwayat booking dan pembayaran terakhir
$query = mysqli_query($conn, "
    SELECT
        b.*,
        w.nama_paket,
        w.destinasi,
        w.gambar,
        pb.id_pembayaran,
        pb.status_pembayaran
    FROM booking b
    LEFT JOIN paket_wisata w
        ON b.id_paket = w.id_paket
    LEFT JOIN pembayaran pb
        ON pb.id_pembayaran = (
            SELECT MAX(p2.id_pembayaran)
            FROM pembayaran p2
            WHERE p2.id_booking = b.id_booking
        )
    WHERE b.id_user = $id_user
    ORDER BY b.id_booking DESC
");

if (!$query) {
    die("Gagal mengambil riwayat booking: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - RPBNusa</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #e3f2fd;
            font-family: Arial, sans-serif;
        }

        .judul {
            color: #0751a8;
            font-weight: bold;
        }

        .card-riwayat {
            border: none;
            border-radius: 14px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }

        .gambar-paket {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }

        .harga {
            color: #0066ff;
            font-weight: bold;
            white-space: nowrap;
        }

        .table th {
            white-space: nowrap;
        }
    </style>
</head>

<body>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h2 class="judul mb-1">Riwayat Booking</h2>
            <p class="text-muted mb-0">
                Daftar perjalanan yang pernah kamu pesan.
            </p>
        </div>

        <div>
            <a href="dashboard.php" class="btn btn-secondary">
                Kembali Dashboard
            </a>

            <a href="paket_wisata/index.php" class="btn btn-primary">
                + Booking Paket
            </a>
        </div>

    </div>

    <div class="card card-riwayat">
        <div class="card-body p-3">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Paket Wisata</th>
                            <th>Keberangkatan</th>
                            <th>Peserta</th>
                            <th>Total Harga</th>
                            <th>Status Booking</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    if (mysqli_num_rows($query) > 0):
                        while ($data = mysqli_fetch_assoc($query)):

                            $status = $data['status_booking'] ?? 'Menunggu';
                            $status_bayar = $data['status_pembayaran'] ?? '';

                            $gambar = !empty($data['gambar'])
                                ? "../uploads/paket_wisata/" . basename($data['gambar'])
                                : "../uploads/paket_wisata/default.jpg";
                    ?>

                        <tr>

                            <td><?= $no++; ?></td>

                            <td>
                                <div class="d-flex align-items-center gap-3">

                                    <img
                                        src="<?= htmlspecialchars($gambar); ?>"
                                        class="gambar-paket"
                                        alt="Foto Paket Wisata"
                                        onerror="this.style.display='none';">

                                    <div>
                                        <strong>
                                            <?= htmlspecialchars($data['nama_paket'] ?? '-'); ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($data['destinasi'] ?? '-'); ?>
                                        </small>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <?= !empty($data['tanggal_keberangkatan'])
                                    ? date('d-m-Y', strtotime($data['tanggal_keberangkatan']))
                                    : '-'; ?>
                            </td>

                            <td>
                                <?= (int) $data['jumlah_peserta']; ?> orang
                            </td>

                            <td class="harga">
                                Rp <?= number_format(
                                    (float) $data['total_harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td>
                                <?php if ($status === 'Dikonfirmasi'): ?>
                                    <span class="badge bg-success">
                                        Dikonfirmasi
                                    </span>
                                <?php elseif ($status === 'Ditolak'): ?>
                                    <span class="badge bg-danger">
                                        Ditolak
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">
                                        <?= htmlspecialchars($status); ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($status !== 'Dikonfirmasi'): ?>

                                    <span class="badge bg-secondary">
                                        Belum tersedia
                                    </span>

                                <?php elseif ($status_bayar === 'Terverifikasi'): ?>

                                    <span class="badge bg-success">
                                        Lunas
                                    </span>

                                <?php elseif ($status_bayar === 'Menunggu'): ?>

                                    <span class="badge bg-warning text-dark">
                                        Menunggu Verifikasi
                                    </span>

                                <?php elseif ($status_bayar === 'Ditolak'): ?>

                                    <span class="badge bg-danger">
                                        Ditolak
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        Belum Dibayar
                                    </span>

                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($status === 'Dikonfirmasi'): ?>

                                    <?php if ($status_bayar === 'Terverifikasi'): ?>

                                        <span class="text-success fw-bold">
                                            <i class="bi bi-check-circle"></i>
                                            Lunas
                                        </span>

                                    <?php elseif ($status_bayar === 'Menunggu'): ?>

                                        <span class="text-warning">
                                            Menunggu verifikasi
                                        </span>

                                    <?php else: ?>

                                        <a
                                            href="Pembayaran.php?id_booking=<?= (int) $data['id_booking']; ?>"
                                            class="btn btn-primary btn-sm">

                                            <?= $status_bayar === 'Ditolak'
                                                ? 'Bayar Ulang'
                                                : 'Bayar Sekarang'; ?>

                                        </a>

                                    <?php endif; ?>

                                <?php elseif ($status === 'Ditolak'): ?>

                                    <span class="text-muted">
                                        Booking ditolak
                                    </span>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Menunggu konfirmasi
                                    </span>

                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php
                        endwhile;
                    else:
                    ?>

                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada riwayat booking.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>