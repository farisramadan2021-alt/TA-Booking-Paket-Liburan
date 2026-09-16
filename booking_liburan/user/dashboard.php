<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SESSION['role'] !== 'pelanggan') {
    header("Location: ../login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Booking Liburan</title>
    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    Halo, <?= htmlspecialchars($_SESSION['nama']); ?> 👋
                </span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">
                    Logout
                </a>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="mb-4">
            <h2 class="fw-bold">Dashboard User</h2>
            <p class="text-muted">
                Selamat datang di Booking Liburan. Silakan pilih paket wisata yang ingin kamu pesan.
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            Paket Wisata
                        </h5>
                        <p class="card-text text-muted">
                            Lihat berbagai paket wisata yang tersedia.
                        </p>
                        <a href="paket_wisata/index.php" class="btn btn-primary">
                            Lihat Paket Wisata
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            Booking Saya
                        </h5>
                        <p class="card-text text-muted">
                            Lihat daftar booking yang sudah kamu lakukan.
                        </p>
                        <a href="../booking/formBooking.php"
                           class="btn btn-primary">
                            Booking Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            👤 Profil Saya
                        </h5>
                        <p class="card-text text-muted">
                            Lihat dan kelola informasi profil kamu.
                        </p>
                        <a href="../login/profil.php"
                           class="btn btn-secondary">
                            Lihat Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>