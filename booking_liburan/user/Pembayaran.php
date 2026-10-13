<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Ambil ID booking dari URL
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: riwayat_booking.php");
    exit;
}

$id_booking = (int) $_GET['id'];

// Ambil booking milik user yang sedang login
$stmt = mysqli_prepare($conn, "
    SELECT
        b.id_booking,
        b.id_user,
        b.total_harga,
        b.status_booking,
        p.nama_paket,
        pb.id_pembayaran,
        pb.metode_pembayaran,
        pb.jumlah_bayar,
        pb.bukti_pembayaran,
        pb.status_pembayaran
    FROM booking b
    JOIN paket_wisata p ON b.id_paket = p.id_paket
    LEFT JOIN pembayaran pb ON b.id_booking = pb.id_booking
    WHERE b.id_booking = ? AND b.id_user = ?
    ORDER BY pb.id_pembayaran DESC
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "ii", $id_booking, $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$booking) {
    exit("Data booking tidak ditemukan atau bukan milik akun kamu.");
}

$status_bayar = $booking['status_pembayaran'] ?? 'Belum Bayar';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - RPBNusa</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
            </a>

            <a href="riwayat_booking.php" class="btn btn-light btn-sm">
                Riwayat Booking
            </a>
        </div>
    </nav>

    <div class="container py-5">

        <h2 class="fw-bold mb-1">Pembayaran Paket Wisata</h2>
        <p class="text-muted mb-4">
            Periksa tagihan dan status pembayaran kamu.
        </p>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h5 class="fw-bold">
                    <?= htmlspecialchars($booking['nama_paket']); ?>
                </h5>

                <p class="mb-1">
                    ID Booking: #<?= (int) $booking['id_booking']; ?>
                </p>

                <p class="text-muted">Total tagihan</p>

                <h3 class="fw-bold text-primary mb-4">
                    Rp <?= number_format(
                            (float) $booking['total_harga'],
                            0,
                            ',',
                            '.'
                        ); ?>
                </h3>

                <p>
                    Status Booking:
                    <strong>
                        <?= htmlspecialchars($booking['status_booking']); ?>
                    </strong>
                </p>

                <p>
                    Status Pembayaran:
                    <span class="badge bg-warning text-dark">
                        <?= htmlspecialchars($status_bayar); ?>
                    </span>
                </p>

                <?php if ($booking['status_booking'] === 'Ditolak'): ?>

                    <div class="alert alert-danger">
                        Booking ini ditolak. Silakan hubungi admin.
                    </div>

                <?php elseif ($status_bayar === 'Terverifikasi'): ?>

                    <div class="alert alert-success">
                        Pembayaran kamu sudah diverifikasi oleh admin.
                    </div>

                <?php elseif ($booking['status_booking'] !== 'Dikonfirmasi'): ?>

                    <div class="alert alert-info">
                        Silakan tunggu admin mengonfirmasi booking sebelum melanjutkan pembayaran.
                    </div>

                <?php elseif ($status_bayar === 'Menunggu'): ?>

                    <div class="alert alert-warning">
                        Bukti pembayaran sudah tercatat atau sedang menunggu verifikasi admin.
                    </div>

                <?php else: ?>

                    <div class="alert alert-info">
                        Booking sudah dikonfirmasi. Kamu dapat melanjutkan ke proses pembayaran.
                    </div>

                    <p>
                        Langkah berikutnya adalah memilih metode pembayaran
                        dan mengunggah bukti pembayaran.
                    </p>

                    <form action="proses_pembayaran.php"
                        method="POST"
                        enctype="multipart/form-data">

                        <input type="hidden"
                            name="id_booking"
                            value="<?= (int) $booking['id_booking']; ?>">

                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">
                                Metode Pembayaran
                            </label>

                            <select name="metode_pembayaran"
                                id="metode_pembayaran"
                                class="form-select"
                                required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Dana">Dana</option>
                                <option value="Gopay">Gopay</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="bukti_pembayaran" class="form-label">
                                Upload Bukti Pembayaran
                            </label>

                            <input type="file"
                                name="bukti_pembayaran"
                                id="bukti_pembayaran"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required>

                            <small class="text-muted">
                                Format JPG, JPEG, PNG, atau PDF.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Kirim Pembayaran
                        </button>
                    </form>

                <?php endif; ?>

                <a href="riwayat_booking.php"
                    class="btn btn-secondary mt-3">
                    Kembali
                </a>

            </div>
        </div>

    </div>

</body>

</html>