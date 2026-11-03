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
$id_booking = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_booking <= 0) {
    header("Location: riwayat_booking.php");
    exit;
}

// Ambil data booking dan jadwal
$stmt = mysqli_prepare($conn, "
    SELECT
        b.id_booking,
        b.id_user,
        b.id_paket,
        b.status_booking,
        b.tanggal_keberangkatan,
        p.nama_paket,
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
    WHERE b.id_booking = ?
      AND b.id_user = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "ii", $id_booking, $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking tidak ditemukan atau bukan milik akun kamu.");
}

// Pastikan booking sudah dikonfirmasi
if ($booking['status_booking'] !== 'Dikonfirmasi') {
    die("Booking ini belum dikonfirmasi.");
}

// Pastikan wisata sudah selesai
$hariIni = date('Y-m-d');

if (
    empty($booking['tanggal_pulang']) ||
    $booking['tanggal_pulang'] >= $hariIni
) {
    die("Kamu hanya dapat memberikan ulasan setelah perjalanan selesai.");
}

// Pastikan belum pernah memberikan ulasan
if (!empty($booking['id_ulasan'])) {
    die("Booking ini sudah memiliki ulasan.");
}

$error = '';

// Proses simpan ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $komentar = trim($_POST['komentar'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $error = "Silakan pilih rating antara 1 sampai 5.";
    } elseif ($komentar === '') {
        $error = "Komentar ulasan tidak boleh kosong.";
    } else {

        $stmtUlasan = mysqli_prepare($conn, "
            INSERT INTO ulasan (id_booking, rating, komentar)
            VALUES (?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmtUlasan,
            "iis",
            $id_booking,
            $rating,
            $komentar
        );

        if (mysqli_stmt_execute($stmtUlasan)) {
            header("Location: riwayat_booking.php");
            exit;
        } else {
            $error = "Ulasan gagal disimpan. Pastikan booking belum pernah diulas.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Beri Ulasan</title>

    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #e3f2fd;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #0d47a1;
        }

        .judul {
            color: #0d47a1;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 35px;
            color: #ccc;
            cursor: pointer;
        }

        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffc107;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
            </a>

            <a href="riwayat_booking.php" class="btn btn-light btn-sm">
                Kembali
            </a>
        </div>
    </nav>

    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-md-7">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h3 class="fw-bold judul mb-2">
                            Beri Ulasan Perjalanan
                        </h3>

                        <p class="text-muted">
                            Bagikan pengalaman perjalananmu kepada pelanggan lainnya.
                        </p>

                        <hr>

                        <div class="mb-4">
                            <div class="text-muted small">
                                Paket Wisata
                            </div>

                            <div class="fw-bold fs-5">
                                <?= htmlspecialchars($booking['nama_paket']); ?>
                            </div>

                            <div class="text-muted">
                                Booking #<?= (int) $booking['id_booking']; ?>
                            </div>
                        </div>

                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Rating Pengalaman
                                </label>

                                <div class="rating">
                                    <input type="radio" name="rating" id="bintang5" value="5">
                                    <label for="bintang5">&#9733;</label>

                                    <input type="radio" name="rating" id="bintang4" value="4">
                                    <label for="bintang4">&#9733;</label>

                                    <input type="radio" name="rating" id="bintang3" value="3">
                                    <label for="bintang3">&#9733;</label>

                                    <input type="radio" name="rating" id="bintang2" value="2">
                                    <label for="bintang2">&#9733;</label>

                                    <input type="radio" name="rating" id="bintang1" value="1">
                                    <label for="bintang1">&#9733;</label>
                                </div>

                                <small class="text-muted">
                                    Pilih bintang 1 sampai 5.
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="komentar" class="form-label fw-bold">
                                    Komentar
                                </label>

                                <textarea
                                    name="komentar"
                                    id="komentar"
                                    class="form-control"
                                    rows="5"
                                    placeholder="Ceritakan pengalaman perjalananmu..."
                                    required><?= htmlspecialchars($_POST['komentar'] ?? ''); ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="riwayat_booking.php" class="btn btn-secondary">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Kirim Ulasan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>

</body>
</html>