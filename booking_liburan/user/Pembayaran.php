<?php
session_start();
require_once __DIR__ . "/../config/koneksi.php";

// Cek login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Cek ID booking
if (!isset($_GET['id_booking'])) {
    header("Location: riwayat_booking.php");
    exit;
}

$id_booking = (int) $_GET['id_booking'];

// Ambil data booking milik user
$stmt = mysqli_prepare($conn, "
    SELECT b.*, w.nama_paket
    FROM booking b
    LEFT JOIN paket_wisata w ON b.id_paket = w.id_paket
    WHERE b.id_booking = ? AND b.id_user = ?
    AND b.status_booking = 'Dikonfirmasi'
");

mysqli_stmt_bind_param($stmt, "ii", $id_booking, $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    echo "<script>
        alert('Booking tidak ditemukan atau belum dikonfirmasi.');
        window.location='riwayat_booking.php';
    </script>";
    exit;
}

// Cek pembayaran sebelumnya
$stmt = mysqli_prepare($conn, "
    SELECT * FROM pembayaran WHERE id_booking = ?
");

mysqli_stmt_bind_param($stmt, "i", $id_booking);
mysqli_stmt_execute($stmt);

$result_bayar = mysqli_stmt_get_result($stmt);
$pembayaran = mysqli_fetch_assoc($result_bayar);

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($pembayaran) {
        echo "<script>
            alert('Pembayaran untuk booking ini sudah dikirim.');
            window.location='riwayat_booking.php';
        </script>";
        exit;
    }

    $metode = $_POST['metode_pembayaran'] ?? '';
    $jumlah = (float) $booking['total_harga'];

    $metode_valid = ['Transfer Bank', 'E-Wallet', 'Gopay'];

    if (!in_array($metode, $metode_valid, true)) {
        die("Metode pembayaran tidak valid.");
    }

    if (
        !isset($_FILES['bukti_pembayaran']) ||
        $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK
    ) {
        die("Silakan upload bukti pembayaran.");
    }

    $file = $_FILES['bukti_pembayaran'];

    if ($file['size'] > 2 * 1024 * 1024) {
        die("Ukuran file maksimal 2 MB.");
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $jenis_file = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    if (!isset($jenis_file[$mime])) {
        die("Bukti harus berupa gambar JPG atau PNG.");
    }

    // Simpan bukti ke folder uploads/bukti_pembayaran
    $folder = __DIR__ . "/../uploads/bukti_pembayaran/";

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $nama_file = "bukti_" . $id_booking . "_" .
        bin2hex(random_bytes(8)) . "." . $jenis_file[$mime];

    if (!move_uploaded_file(
        $file['tmp_name'],
        $folder . $nama_file
    )) {
        die("Gagal mengunggah bukti pembayaran.");
    }

    // Simpan data pembayaran
    $stmt = mysqli_prepare($conn, "
        INSERT INTO pembayaran
        (id_booking, metode_pembayaran, jumlah_bayar,
         bukti_pembayaran, status_pembayaran)
        VALUES (?, ?, ?, ?, 'Menunggu')
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "isds",
        $id_booking,
        $metode,
        $jumlah,
        $nama_file
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Pembayaran berhasil dikirim. Menunggu verifikasi admin.');
            window.location='riwayat_booking.php';
        </script>";
        exit;
    }

    unlink($folder . $nama_file);
    die("Gagal menyimpan pembayaran: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pembayaran - RPBNusa</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        body {
            background: #e3f2fd;
            min-height: 100vh;
        }

        .judul {
            color: #0d47a1;
        }

        .card {
            border: none;
            border-radius: 14px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
            </a>

            <a href="riwayat_booking.php"
                class="btn btn-light btn-sm">
                Kembali
            </a>
        </div>
    </nav>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-7">

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <h3 class="judul fw-bold mb-3">
                            Pembayaran Paket Wisata
                        </h3>

                        <p class="text-muted">
                            Silakan lakukan pembayaran dan unggah
                            bukti pembayaran Anda.
                        </p>

                        <hr>

                        <h5 class="fw-bold">
                            <?= htmlspecialchars($booking['nama_paket']); ?>
                        </h5>

                        <div class="mb-3">
                            <span class="text-muted">Tanggal Keberangkatan</span>
                            <div class="fw-semibold">
                                <?= date(
                                    'd-m-Y',
                                    strtotime($booking['tanggal_keberangkatan'])
                                ); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="text-muted">Jumlah Peserta</span>
                            <div class="fw-semibold">
                                <?= (int) $booking['jumlah_peserta']; ?> orang
                            </div>
                        </div>

                        <div class="bg-light rounded p-3 mb-4">
                            <span class="text-muted">
                                Total Pembayaran
                            </span>

                            <h3 class="text-primary fw-bold mb-0">
                                Rp <?= number_format(
                                        $booking['total_harga'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                            </h3>
                        </div>

                        <form method="POST"
                            enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Metode Pembayaran
                                </label>

                                <select name="metode_pembayaran"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Pilih Metode Pembayaran
                                    </option>

                                    <option value="Transfer Bank">
                                        Transfer Bank
                                    </option>

                                    <option value="E-Wallet">
                                        E-Wallet
                                    </option>

                                    <option value="E-Wallet">
                                        Gopay
                                    </option>

                                </select>
                            </div>

                            <div class="alert alert-info">
                                <strong>Informasi Pembayaran</strong>
                                <p class="mb-0">
                                    Silakan lakukan transfer sesuai
                                    total pembayaran melalui rekening
                                    atau akun pembayaran yang ditentukan
                                    oleh pihak RPBNusa.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Upload Bukti Pembayaran
                                </label>

                                <input type="file"
                                    name="bukti_pembayaran"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png"
                                    required>

                                <small class="text-muted">
                                    Format JPG atau PNG, maksimal 2 MB.
                                </small>
                            </div>

                            <button type="submit"
                                class="btn btn-primary w-100 fw-bold">
                                Kirim Pembayaran
                            </button>

                            <a href="riwayat_booking.php"
                                class="btn btn-secondary w-100 mt-2">
                                Kembali ke Riwayat Booking
                            </a>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>