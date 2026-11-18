<?php
session_start();

require_once __DIR__ . "/../config/koneksi.php";

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

// Jika yang login admin, jangan masuk ke dashboard user
if ($_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Ambil data paket wisata yang aktif
$query = mysqli_query(
    $conn,
    "SELECT * FROM paket_wisata 
     WHERE status = 'Aktif'
     ORDER BY id_paket DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ADF Travel</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 70px;
        }

        body {
            background-color: #F4F9FE;
        }

        /* Ikon SVG (tanpa internet / tanpa emoji) */
        .icon {
            width: 1.1em;
            height: 1.1em;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            vertical-align: -0.15em;
            flex-shrink: 0;
        }

        /* Navbar */
        .navbar-adf {
            background: linear-gradient(90deg, #0D47A1, #1976D2);
        }

        .navbar-adf .nav-link {
            border-radius: 50rem;
            padding: .4rem 1rem !important;
            transition: background .2s;
        }

        .navbar-adf .nav-link:hover,
        .navbar-adf .nav-link.active {
            background: rgba(255, 255, 255, .18);
        }

        /* Hero */
        .hero-adf {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-adf::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: #0D47A1;
            opacity: .08;
            top: -140px;
            right: -120px;
        }

        .hero-adf::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: #2196F3;
            opacity: .10;
            bottom: -110px;
            left: -80px;
        }

        .hero-adf .container {
            position: relative;
            z-index: 1;
        }

        .hero-img {
            border: 6px solid #fff;
            transform: rotate(1.5deg);
            transition: transform .3s;
        }

        .hero-img:hover {
            transform: rotate(0deg) scale(1.02);
        }

        /* Kartu informasi */
        .info-card {
            transition: transform .25s, box-shadow .25s;
        }

        .info-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 .75rem 1.5rem rgba(13, 71, 161, .18) !important;
        }

        .info-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1976D2, #2196F3);
            color: #fff;
            font-size: 1.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 .5rem 1rem rgba(33, 150, 243, .35);
        }

        .info-icon .icon {
            width: 1em;
            height: 1em;
            vertical-align: 0;
        }

        /* Kartu paket */
        .paket-card {
            border-radius: 1.25rem;
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
        }

        .paket-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 2rem rgba(13, 71, 161, .2) !important;
        }

        .paket-card img {
            transition: transform .4s;
        }

        .paket-card:hover img {
            transform: scale(1.07);
        }

        .badge-durasi {
            background: #E3F2FD;
            color: #0D47A1;
            font-weight: 500;
        }

        .btn-adf {
            background: linear-gradient(90deg, #1976D2, #2196F3);
            color: #fff;
            border: 0;
            border-radius: 50rem;
            padding: .6rem 1rem;
            font-weight: 600;
            transition: transform .2s, box-shadow .2s;
        }

        .btn-adf:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(33, 150, 243, .4);
        }

        /* CTA */
        .cta-adf {
            background: linear-gradient(135deg, #0D47A1, #1976D2);
            position: relative;
            overflow: hidden;
        }

        .cta-adf::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: #fff;
            opacity: .06;
            top: -120px;
            left: -80px;
        }
    </style>
</head>

<body>

    <!-- ================= KUMPULAN IKON (SVG) ================= -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
        <symbol id="i-globe" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="2" y1="12" x2="22" y2="12" />
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
        </symbol>
        <symbol id="i-user" viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
        </symbol>
        <symbol id="i-pin" viewBox="0 0 24 24">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
            <circle cx="12" cy="10" r="3" />
        </symbol>
        <symbol id="i-clock" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
        </symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
        </symbol>
        <symbol id="i-card" viewBox="0 0 24 24">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
            <line x1="1" y1="10" x2="23" y2="10" />
        </symbol>
        <symbol id="i-map" viewBox="0 0 24 24">
            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
            <line x1="8" y1="2" x2="8" y2="18" />
            <line x1="16" y1="6" x2="16" y2="22" />
        </symbol>
        <symbol id="i-plane" viewBox="0 0 24 24">
            <line x1="22" y1="2" x2="11" y2="13" />
            <polygon points="22 2 15 22 11 13 2 9 22 2" />
        </symbol>
        <symbol id="i-arrow" viewBox="0 0 24 24">
            <line x1="5" y1="12" x2="19" y2="12" />
            <polyline points="12 5 19 12 12 19" />
        </symbol>
    </svg>


    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-adf sticky-top shadow-sm">

        <div class="container">

            <a class="navbar-brand fw-bold d-flex align-items-center gap-2"
                href="dashboard.php">
                <svg class="icon"><use href="#i-globe" /></svg>
                ADF Travel
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarUser"
                aria-controls="navbarUser"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse"
                id="navbarUser">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link active"
                            href="dashboard.php">
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
                            href="#paket_wisata">
                            Paket Wisata
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="riwayat_booking.php">
                            Riwayat Booking
                        </a>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="../login/profil.php"
                        class="btn btn-outline-light btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-2">
                        <svg class="icon"><use href="#i-user" /></svg>
                        Profil
                    </a>
                    <a href="../logout.php"
                        class="btn btn-light btn-sm rounded-pill px-3">
                        Logout
                    </a>
                </div>

            </div>
        </div>
    </nav>


    <!-- ================= HERO ================= -->
    <section class="hero-adf py-5">

        <div class="container py-lg-4">

            <div class="row align-items-center g-5">

                <!-- Teks -->
                <div class="col-lg-6">

                    <span class="badge rounded-pill text-white mb-3 px-3 py-2 d-inline-flex align-items-center gap-2"
                        style="background-color: #2196F3;">
                        <svg class="icon"><use href="#i-plane" /></svg>
                        ADF Travel
                    </span>

                    <h1 class="display-4 fw-bold"
                        style="color: #0D47A1;">
                        Temukan Liburan
                        Impianmu
                    </h1>

                    <p class="lead text-secondary">
                        Jelajahi berbagai destinasi wisata menarik
                        dan pilih paket liburan yang sesuai dengan
                        kebutuhanmu.
                    </p>

                    <div class="d-flex gap-2 flex-wrap">

                        <a href="#paket_wisata"
                            class="btn btn-adf btn-lg px-4 d-inline-flex align-items-center gap-2">
                            Jelajahi Paket Wisata
                            <svg class="icon"><use href="#i-arrow" /></svg>
                        </a>

                    </div>

                </div>

                <!-- Gambar -->
                <div class="col-lg-6">

                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg hero-img">

                        <img
                            src="../uploads/paket_wisata/lucu.png"
                            class="img-fluid object-fit-cover"
                            alt="Destinasi wisata">

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= INFORMASI ================= -->
    <section class="py-5">

        <div class="container">

            <div class="row text-center g-4">

                <div class="col-md-4">

                    <div class="bg-white rounded-4 p-4 shadow-sm info-card h-100">

                        <div class="info-icon">
                            <svg class="icon"><use href="#i-map" /></svg>
                        </div>

                        <h6 class="fw-bold" style="color: #0D47A1;">
                            Banyak Destinasi
                        </h6>

                        <p class="mb-0 text-secondary small">
                            Pilihan wisata menarik untuk liburanmu.
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="bg-white rounded-4 p-4 shadow-sm info-card h-100">

                        <div class="info-icon">
                            <svg class="icon"><use href="#i-card" /></svg>
                        </div>

                        <h6 class="fw-bold" style="color: #0D47A1;">
                            Harga Terjangkau
                        </h6>

                        <p class="mb-0 text-secondary small">
                            Temukan paket wisata sesuai budget.
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="bg-white rounded-4 p-4 shadow-sm info-card h-100">

                        <div class="info-icon">
                            <svg class="icon"><use href="#i-calendar" /></svg>
                        </div>

                        <h6 class="fw-bold" style="color: #0D47A1;">
                            Booking Mudah
                        </h6>

                        <p class="mb-0 text-secondary small">
                            Pesan paket wisata dengan mudah.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= PAKET WISATA ================= -->
    <section id="paket_wisata"
        class="pb-5 pt-2">

        <div class="container">

            <div class="text-center mb-5">

                <span class="badge rounded-pill text-white mb-2 px-3 py-2"
                    style="background-color: #2196F3;">
                    Paket Wisata
                </span>

                <h2 class="fw-bold"
                    style="color: #0D47A1;">
                    Pilihan Paket Wisata
                </h2>

                <p class="text-secondary">
                    Pilih destinasi favoritmu dan mulai perjalananmu.
                </p>

            </div>


            <div class="row g-4">

                <?php if (mysqli_num_rows($query) > 0): ?>

                    <?php while ($paket = mysqli_fetch_assoc($query)): ?>

                        <div class="col-md-6 col-lg-4">

                            <div class="card h-100 border-0 shadow-sm paket-card">

                                <!-- Gambar -->
                                <div class="ratio ratio-16x9 overflow-hidden">

                                    <?php if (!empty($paket['gambar'])): ?>

                                        <img
                                            src="../uploads/paket_wisata/<?= htmlspecialchars($paket['gambar']); ?>"
                                            class="card-img-top object-fit-cover"
                                            alt="<?= htmlspecialchars($paket['nama_paket']); ?>">

                                    <?php else: ?>

                                        <div class="d-flex align-items-center justify-content-center bg-secondary text-white">
                                            Tidak ada gambar
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- Isi Card -->
                                <div class="card-body d-flex flex-column p-4">

                                    <h5 class="card-title fw-bold"
                                        style="color: #0D47A1;">

                                        <?= htmlspecialchars($paket['nama_paket']); ?>

                                    </h5>

                                    <p class="mb-2 text-secondary d-flex align-items-center gap-2">

                                        <svg class="icon" style="color: #2196F3;"><use href="#i-pin" /></svg>
                                        <?= htmlspecialchars($paket['destinasi']); ?>

                                    </p>


                                    <p class="mb-3">

                                        <span class="badge rounded-pill badge-durasi px-3 py-2 d-inline-flex align-items-center gap-2">
                                            <svg class="icon"><use href="#i-clock" /></svg>
                                            <?= htmlspecialchars($paket['durasi']); ?>
                                            Hari
                                        </span>

                                    </p>


                                    <h4 class="fw-bold mb-3"
                                        style="color: #2196F3;">

                                        Rp <?= number_format(
                                                $paket['harga'],
                                                0,
                                                ',',
                                                '.'
                                            ); ?>

                                    </h4>


                                    <p class="card-text text-secondary">

                                        <?= htmlspecialchars($paket['deskripsi']); ?>

                                    </p>


                                    <div class="mt-auto pt-3">

                                        <a
                                            href="../booking/formBooking.php?id=<?= $paket['id_paket']; ?>"
                                            class="btn btn-adf w-100 d-inline-flex align-items-center justify-content-center gap-2">

                                            Booking Sekarang
                                            <svg class="icon"><use href="#i-arrow" /></svg>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="col-12">

                        <div class="alert text-center rounded-4"
                            style="background-color: #E3F2FD; color: #0D47A1;">

                            Belum ada paket wisata yang tersedia.

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- ================= CTA ================= -->
    <section class="cta-adf py-5">

        <div class="container text-center position-relative">

            <h2 class="text-white fw-bold">
                Siap Memulai Liburanmu?
            </h2>

            <p class="text-white-50">
                Pilih paket wisata dan lakukan booking sekarang.
            </p>

            <a href="#paket_wisata"
                class="btn btn-light rounded-pill px-4">
                Lihat Paket Wisata
            </a>

        </div>

    </section>


    <!-- ================= FOOTER ================= -->
    <footer class="py-4 bg-white">

        <div class="container text-center">

            <p class="mb-1 fw-bold d-flex align-items-center justify-content-center gap-2"
                style="color: #0D47A1;">
                <svg class="icon"><use href="#i-globe" /></svg>
                ADF Travel
            </p>

            <p class="mb-0 text-secondary small">
                &copy; <?= date('Y'); ?> ADF Travel.
                Semua hak dilindungi.
            </p>

        </div>

    </footer>


    <script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>