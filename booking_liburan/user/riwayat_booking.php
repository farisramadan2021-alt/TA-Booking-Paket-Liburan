<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Ambil riwayat booking dan informasi jadwal serta ulasan
$query = mysqli_query(
    $conn,
    "SELECT
        b.id_booking,
        b.tanggal_booking,
        b.tanggal_keberangkatan,
        b.jumlah_peserta,
        b.total_harga,
        b.status_booking,
        p.nama_paket,
        p.destinasi,
        p.gambar,
        j.tanggal_pulang,
        u.id_ulasan
    FROM booking b
    JOIN paket_wisata p
        ON b.id_paket = p.id_paket
    LEFT JOIN jadwal_keberangkatan j
        ON j.id_paket = b.id_paket
        AND j.tanggal_keberangkatan = b.tanggal_keberangkatan
    LEFT JOIN ulasan u
        ON u.id_booking = b.id_booking
    WHERE b.id_user = $id_user
    ORDER BY b.id_booking DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Booking</title>

    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #e3f2fd;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #0d47a1;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .judul {
            color: #0d47a1;
        }

        .gambar-paket {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
            </a>

            <div>
                <span class="text-white me-3">
                    Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
                </span>

                <a href="dashboard.php" class="btn btn-light btn-sm">
                    Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold judul mb-1">Riwayat Booking</h2>
                <p class="text-muted mb-0">
                    Daftar perjalanan yang pernah kamu pesan.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali Dashboard
                </a>

                <a href="paket_wisata/index.php" class="btn btn-primary">
                    + Booking Paket
                </a>
            </div>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>

            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Paket Wisata</th>
                                    <th>Keberangkatan</th>
                                    <th>Peserta</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $no = 1;
                                $hariIni = date('Y-m-d');

                                while ($booking = mysqli_fetch_assoc($query)):
                                ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td>
                                            <div class="d-flex align-items-center">

                                                <?php if (!empty($booking['gambar'])): ?>
                                                    <img
                                                        src="../uploads/paket_wisata/<?= htmlspecialchars($booking['gambar']); ?>"
                                                        class="gambar-paket me-3"
                                                        alt="<?= htmlspecialchars($booking['nama_paket']); ?>">
                                                <?php endif; ?>

                                                <div>
                                                    <div class="fw-bold">
                                                        <?= htmlspecialchars($booking['nama_paket']); ?>
                                                    </div>

                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($booking['destinasi']); ?>
                                                    </small>
                                                </div>

                                            </div>
                                        </td>

                                        <td>
                                            <?= date(
                                                'd-m-Y',
                                                strtotime($booking['tanggal_keberangkatan'])
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= (int) $booking['jumlah_peserta']; ?> orang
                                        </td>

                                        <td class="fw-bold text-primary">
                                            Rp <?= number_format(
                                                $booking['total_harga'],
                                                0,
                                                ',',
                                                '.'
                                            ); ?>
                                        </td>

                                        <td>
                                            <?php if ($booking['status_booking'] === 'Menunggu'): ?>

                                                <span class="badge bg-warning text-dark">
                                                    Menunggu
                                                </span>

                                            <?php elseif ($booking['status_booking'] === 'Dikonfirmasi'): ?>

                                                <span class="badge bg-success">
                                                    Dikonfirmasi
                                                </span>

                                            <?php elseif ($booking['status_booking'] === 'Ditolak'): ?>

                                                <span class="badge bg-danger">
                                                    Ditolak
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($booking['status_booking']); ?>
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                        <td class="text-nowrap">

                                            <?php
                                            $wisataSelesai = (
                                                !empty($booking['tanggal_pulang']) &&
                                                $booking['tanggal_pulang'] < $hariIni
                                            );
                                            ?>

                                            <?php if (
                                                $booking['status_booking'] === 'Dikonfirmasi'
                                                && $wisataSelesai
                                                && empty($booking['id_ulasan'])
                                            ): ?>

                                                <a
                                                    href="ulasan.php?id=<?= (int) $booking['id_booking']; ?>"
                                                    class="btn btn-warning btn-sm">
                                                    Beri Ulasan
                                                </a>

                                            <?php elseif (!empty($booking['id_ulasan'])): ?>

                                                <span class="badge bg-success">
                                                    Sudah Diulas
                                                </span>

                                            <?php elseif (
                                                $booking['status_booking'] === 'Dikonfirmasi'
                                                && !$wisataSelesai
                                            ): ?>

                                                <span class="text-muted">
                                                    Menunggu wisata selesai
                                                </span>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Belum tersedia
                                                </span>

                                            <?php endif; ?>

                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        <?php else: ?>

            <div class="card shadow-sm">
                <div class="card-body text-center py-5">

                    <h4 class="fw-bold text-secondary">
                        Belum Ada Booking
                    </h4>

                    <p class="text-muted">
                        Kamu belum melakukan pemesanan paket wisata.
                    </p>

                    <a href="paket_wisata/index.php" class="btn btn-primary">
                        Lihat Paket Wisata
                    </a>

                </div>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>