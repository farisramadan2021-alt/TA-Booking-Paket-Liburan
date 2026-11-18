<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: riwayat_booking.php");
    exit;
}

$id_booking = (int) $_GET['id'];

// Struk hanya untuk booking milik user dengan pembayaran Terverifikasi
$stmt = mysqli_prepare($conn, "
    SELECT
        b.id_booking,
        b.tanggal_booking,
        b.tanggal_keberangkatan,
        b.jumlah_peserta,
        b.total_harga,
        p.nama_paket,
        p.destinasi,
        pb.id_pembayaran,
        pb.metode_pembayaran,
        pb.jumlah_bayar,
        pb.status_pembayaran
    FROM booking b
    JOIN paket_wisata p ON b.id_paket = p.id_paket
    JOIN pembayaran pb
        ON pb.id_pembayaran = (
            SELECT MAX(x.id_pembayaran) FROM pembayaran x WHERE x.id_booking = b.id_booking
        )
    WHERE b.id_booking = ? AND b.id_user = ?
");
mysqli_stmt_bind_param($stmt, "ii", $id_booking, $id_user);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data || $data['status_pembayaran'] !== 'Terverifikasi') {
    exit("Struk belum tersedia. Pembayaran belum diverifikasi admin atau data bukan milik akun kamu.");
}

$no_struk = 'RPB-' . str_pad((string) $data['id_pembayaran'], 5, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk <?= htmlspecialchars($no_struk); ?> - ADF Travel</title>

    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        body { background-color: #f1f3f5; }

        .struk {
            max-width: 520px;
            margin: 32px auto;
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
        }

        .garis { border-top: 2px dashed #ced4da; margin: 20px 0; }

        @media print {
            body { background: #fff; }
            .struk { box-shadow: none; margin: 0 auto; }
            .tanpa-cetak { display: none !important; }
        }
    </style>
</head>

<body>

    <div class="struk">

        <div class="text-center mb-3">
            <h4 class="fw-bold text-primary mb-0">ADF Travel</h4>
            <small class="text-muted">Bon / Struk Pembayaran</small>
        </div>

        <div class="garis"></div>

        <table class="table table-sm table-borderless mb-0">
            <tr>
                <td class="text-muted">No. Struk</td>
                <td class="text-end fw-semibold"><?= htmlspecialchars($no_struk); ?></td>
            </tr>
            <tr>
                <td class="text-muted">ID Booking</td>
                <td class="text-end">#<?= (int) $data['id_booking']; ?></td>
            </tr>
            <tr>
                <td class="text-muted">Atas Nama</td>
                <td class="text-end"><?= htmlspecialchars($_SESSION['nama'] ?? 'User'); ?></td>
            </tr>
            <tr>
                <td class="text-muted">Tanggal Pemesanan</td>
                <td class="text-end"><?= date('d-m-Y', strtotime($data['tanggal_booking'])); ?></td>
            </tr>
        </table>

        <div class="garis"></div>

        <h6 class="fw-bold mb-1"><?= htmlspecialchars($data['nama_paket']); ?></h6>
        <p class="text-muted mb-3"><?= htmlspecialchars($data['destinasi']); ?></p>

        <table class="table table-sm table-borderless mb-0">
            <tr>
                <td class="text-muted">Keberangkatan</td>
                <td class="text-end"><?= date('d-m-Y', strtotime($data['tanggal_keberangkatan'])); ?></td>
            </tr>
            <tr>
                <td class="text-muted">Jumlah Peserta</td>
                <td class="text-end"><?= (int) $data['jumlah_peserta']; ?> orang</td>
            </tr>
            <tr>
                <td class="text-muted">Metode Pembayaran</td>
                <td class="text-end"><?= htmlspecialchars($data['metode_pembayaran']); ?></td>
            </tr>
        </table>

        <div class="garis"></div>

        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Total Dibayar</span>
            <span class="fs-4 fw-bold text-primary">
                Rp <?= number_format((float) ($data['jumlah_bayar'] ?: $data['total_harga']), 0, ',', '.'); ?>
            </span>
        </div>

        <div class="text-center mt-4">
            <span class="badge bg-success fs-6">LUNAS</span>
            <p class="text-muted small mt-3 mb-0">
                Terima kasih telah mempercayakan perjalananmu kepada ADF Travel.
            </p>
        </div>

        <div class="text-center mt-4 tanpa-cetak">
            <button onclick="window.print()" class="btn btn-primary">Cetak / Simpan PDF</button>
            <a href="riwayat_booking.php" class="btn btn-secondary">Kembali</a>
        </div>

    </div>

</body>

</html>