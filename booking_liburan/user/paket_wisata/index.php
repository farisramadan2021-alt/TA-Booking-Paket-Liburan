<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: ../../login/login.php");
    exit;
}

if ($_SESSION['role'] !== 'pelanggan') {
    header("Location: ../../login/login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM paket_wisata ORDER BY id_paket ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Wisata - Booking Liburan</title>
    <link rel="stylesheet" href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../dashboard.php">
                RPBNusa
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    Halo, <?= htmlspecialchars($_SESSION['nama']); ?> 👋
                </span>
                <a href="../../logout.php" class="btn btn-outline-light btn-sm">
                    Logout
                </a>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Paket Wisata</h2>
                <p class="text-muted mb-0">
                    Pilih paket wisata yang ingin kamu pesan.
                </p>
            </div>
            <a href="../dashboard.php" class="btn btn-secondary">
                Kembali
            </a>
        </div>
        <div class="row g-4">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <?php if (!empty($data['gambar'])): ?>
                                <img
                                    src="../../uploads/paket_wisata/<?= htmlspecialchars($data['gambar']); ?>"
                                    class="card-img-top"
                                    style="height: 220px; object-fit: cover;"
                                    alt="<?= htmlspecialchars($data['nama_paket']); ?>">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                     style="height: 220px;">
                                    Tidak ada gambar
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">
                                    <?= htmlspecialchars($data['nama_paket']); ?>
                                </h5>
                                <p class="text-muted mb-2">
                                    <?= htmlspecialchars($data['destinasi']); ?>
                                </p>
                                <p class="mb-2">
                                    <?= htmlspecialchars($data['durasi']); ?>
                                </p>
                                <h5 class="text-primary fw-bold mb-3">
                                    Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                </h5>
                                <p class="card-text text-muted">
                                    <?= htmlspecialchars($data['deskripsi']); ?>
                                </p>
                                <div class="mt-auto">
                                    <a href="../../booking/formBooking.php?id=<?= $data['id_paket']; ?>"
                                       class="btn btn-primary w-100">
                                        Booking Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Belum ada paket wisata yang tersedia.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>