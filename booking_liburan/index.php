<?php
require_once __DIR__ . "/config/koneksi.php";

// ==========================================
// AMBIL PAKET WISATA AKTIF
// ==========================================
$query = mysqli_query($conn, "
    SELECT *
    FROM paket_wisata
    WHERE status = 'Aktif'
    ORDER BY id_paket DESC
");

if (!$query) {
    die("Gagal mengambil paket wisata: " . mysqli_error($conn));
}

$paketList = [];

while ($row = mysqli_fetch_assoc($query)) {
    $paketList[] = $row;
}

// ==========================================
// FUNGSI ESCAPE HTML
// ==========================================
function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// ==========================================
// GAMBAR HERO
// ==========================================
$gambarHero = "uploads/hero-pantai.png";

if (!empty($paketList)) {
    $gambarPertama = $paketList[0]['gambar'] ?? '';

    if ($gambarPertama !== '') {
        $lokasiGambar = __DIR__ . "/uploads/hero-pantai.png/" . $gambarPertama;

        if (file_exists($lokasiGambar)) {
            $gambarHero = "uploads/hero-pantai.png/" . rawurlencode($gambarPertama);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description"
        content="RPBNusa menyediakan berbagai pilihan paket wisata menarik dengan proses pemesanan yang mudah.">

    <title>RPBNusa - Temukan Liburan Impianmu</title>

    <!-- Bootstrap 5.3.8 -->
    <link rel="stylesheet"
        href="bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        .hero-section {
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-overlay {
            background: rgba(0, 0, 0, 0.35);
        }

        .package-card {
            transition: transform 0.3s ease,
                box-shadow 0.3s ease;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark position-absolute top-0 start-0 w-100 z-3">

        <div class="container py-2">

            <a class="navbar-brand fw-bold fs-4 text-white" href="index.php">
                ADF Travel
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarLanding"
                aria-controls="navbarLanding"
                aria-expanded="false"
                aria-label="Buka navigasi">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarLanding">

                <ul class="navbar-nav mx-auto gap-lg-3">

                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold" href="#home">
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold" href="#paket_wisata">
                            Paket Wisata
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold" href="#tentang">
                            Tentang Kami
                        </a>
                    </li>

                </ul>

                <div class="d-flex gap-2 mt-3 mt-lg-0">

                    <a href="login/login.php"
                        class="btn btn-outline-light px-4">
                        Login
                    </a>

                    <a href="login/register.php"
                        class="btn btn-light px-4 fw-semibold">
                        Daftar
                    </a>

                </div>

            </div>
        </div>
    </nav>



    <!-- ==========================================
     HERO SECTION
========================================== -->


    <!-- HERO SECTION -->
    
<!-- HERO SECTION -->
<section id="home"
    class="hero-section position-relative d-flex align-items-center"
    style="background-image: url('<?= e($gambarHero); ?>');">

    <!-- OVERLAY -->
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container position-relative text-white py-5">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-8 col-xl-7 pt-5">
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-4">
                    EXPLORE INDONESIA
                </span>
                <h1 class="display-2 fw-bold mb-4">
                    Temukan Keindahan
                    <br>
                    Indonesia Bersama
                    <br>
                    ADF Travel
                </h1>
                <p class="lead mb-4">
                    Jelajahi berbagai destinasi wisata menarik
                    dan temukan pengalaman liburan terbaik
                    bersama ADF Travel.
                </p>
                <a href="#paket_wisata"
                    class="btn btn-light btn-lg px-4 fw-semibold">
                    Jelajahi Destinasi
                </a>

                <!-- FORM PENCARIAN -->
                <div class="bg-white rounded-4 shadow p-3 mt-5">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-8">

                            <label for="pilihanPaket"
                                class="form-label fw-semibold text-dark">
                                Pilih Paket Wisata
                            </label>

                            <select class="form-select"
                                id="pilihanPaket">

                                <option value="">
                                    Semua Destinasi
                                </option>

                                <?php foreach ($paketList as $paket): ?>

                                    <option value="<?= e($paket['id_paket']); ?>">
                                        <?= e($paket['nama_paket']); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-4">

                            <button type="button"
                                class="btn btn-primary w-100"
                                onclick="cariWisata()">
                                Cari Wisata
                            </button>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
    <!-- AKHIR HERO -->


    <!-- ==========================================
     PAKET WISATA
========================================== -->

    <section id="paket_wisata" class="py-5 bg-light">

        <div class="container">

            <div class="text-center mb-5">

                <span class="badge bg-success-subtle text-success px-3 py-2 mb-3">
                    DESTINASI WISATA
                </span>

                <h2 class="fw-bold display-6 text-dark">
                    Popular Destinations
                </h2>

                <p class="text-secondary mt-3">
                    Temukan berbagai destinasi wisata menarik
                    untuk pengalaman liburan terbaikmu.
                </p>

            </div>


            <div class="row g-4">

                <?php if (!empty($paketList)): ?>

                    <?php foreach ($paketList as $paket): ?>

                        <?php
                        $namaGambar = $paket['gambar'] ?? '';
                        $pathGambar = __DIR__ . "/uploads/paket_wisata/" . $namaGambar;

                        $gambarTersedia = (
                            $namaGambar !== '' &&
                            file_exists($pathGambar)
                        );
                        ?>

                        <div class="col-md-6 col-lg-4 paket-item"
                            data-id-paket="<?= e($paket['id_paket']); ?>">

                            <div class="card h-100 shadow-sm package-card">

                                <?php if ($gambarTersedia): ?>

                                    <div class="ratio ratio-16x9">
                                        <img
                                            src="uploads/paket_wisata/<?= rawurlencode($namaGambar); ?>"
                                            class="w-100 h-100 object-fit-cover"
                                            alt="<?= e($paket['nama_paket']); ?>"
                                            loading="lazy">
                                    </div>

                                <?php else: ?>

                                    <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                        <span class="fs-1">🌴</span>
                                    </div>

                                <?php endif; ?>

                                <div class="card-body d-flex flex-column p-3">

                                    <h5 class="card-title fw-bold text-dark">
                                        <?= e($paket['nama_paket']); ?>
                                    </h5>

                                    <p class="small text-secondary mb-2">
                                        📍 <?= e($paket['destinasi']); ?>
                                    </p>

                                    <p class="small text-secondary mb-2">
                                        🕐 <?= (int) $paket['durasi']; ?> Hari
                                    </p>

                                    <p class="card-text text-secondary small">
                                        <?= e($paket['deskripsi']); ?>
                                    </p>

                                    <h5 class="fw-bold text-success mt-auto mb-3">
                                        Rp <?= number_format(
                                                (float) $paket['harga'],
                                                0,
                                                ',',
                                                '.'
                                            ); ?>
                                    </h5>

                                    <a href="login/login.php"
                                        class="btn btn-primary w-100">
                                        Booking Sekarang
                                    </a>

                                </div>
                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Belum ada paket wisata yang tersedia.
                        </div>
                    </div>

                <?php endif; ?>

            </div>


    </section>


    <!-- ==========================================
     TENTANG KAMI
========================================== -->

    <section id="tentang" class="about-section section-padding">

        <div class="container">

            <div class="row align-items-center g-4">

                <div class="col-lg-6">

                    <span class="section-badge">
                        TENTANG KAMI
                    </span>

                    <h2 class="section-title">
                        Mengenal Lebih Dekat RPBNusa
                    </h2>

                    <p class="text-secondary mt-4"
                        style="line-height: 1.9;">

                        RPBNusa merupakan platform berbasis website
                        yang membantu pengguna menemukan dan melakukan
                        pemesanan paket wisata dengan mudah.

                    </p>

                    <p class="text-secondary"
                        style="line-height: 1.9;">

                        Kami menyediakan berbagai pilihan destinasi
                        wisata dengan informasi mengenai durasi,
                        harga, dan deskripsi paket wisata.

                    </p>

                    <a href="#paket_wisata"
                        class="btn btn-primary-custom mt-3">

                        Lihat Paket Wisata

                    </a>

                </div>

                <div class="col-lg-6">

                    <div class="about-card">

                        <h5 class="fw-bold"
                            style="color: var(--primary);">

                            Mengapa Memilih RPBNusa?

                        </h5>

                        <div class="about-item">

                            <div class="about-check">✓</div>

                            <div>

                                <h6 class="fw-bold">
                                    Informasi Lengkap
                                </h6>

                                <p class="text-secondary small mb-0">
                                    Lihat informasi paket wisata
                                    sebelum melakukan pemesanan.
                                </p>

                            </div>

                        </div>

                        <div class="about-item">

                            <div class="about-check">✓</div>

                            <div>

                                <h6 class="fw-bold">
                                    Proses Pemesanan Mudah
                                </h6>

                                <p class="text-secondary small mb-0">
                                    Lakukan booking paket wisata
                                    melalui sistem website.
                                </p>

                            </div>

                        </div>

                        <div class="about-item">

                            <div class="about-check">✓</div>

                            <div>

                                <h6 class="fw-bold">
                                    Pilihan Destinasi
                                </h6>

                                <p class="text-secondary small mb-0">
                                    Temukan berbagai pilihan
                                    destinasi wisata menarik.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ==========================================
     CALL TO ACTION
========================================== -->

    <section class="cta-section">

        <div class="container text-center">

            <h2 class="fw-bold text-white mb-3">

                Siap Merencanakan Liburanmu?

            </h2>

            <p class="text-white-50 mb-4">

                Daftar sekarang dan temukan paket wisata
                favoritmu bersama RPBNusa.

            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">

                <a href="login/register.php"
                    class="btn btn-cta">

                    Daftar Sekarang

                </a>

                <a href="login/login.php"
                    class="btn btn-outline-light px-4 py-2 fw-semibold">

                    Login

                </a>

            </div>

        </div>

    </section>


    <!-- ==========================================
     FOOTER
========================================== -->

    <footer>

        <div class="container">

            <div class="row align-items-center">

                <div class="col-md-6 text-center text-md-start">

                    <div class="footer-brand">
                        ✈️ RPBNusa
                    </div>

                    <p class="text-secondary small mb-0">
                        Temukan liburan impianmu.
                    </p>

                </div>

                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">

                    <p class="text-secondary small mb-0">

                        &copy; <?= date('Y'); ?>
                        RPBNusa. All Rights Reserved.

                    </p>

                </div>

            </div>

        </div>

    </footer>


    <!-- ==========================================
     BOOTSTRAP JAVASCRIPT
========================================== -->


    <script>
        function cariWisata() {

            const pilihanPaket = document.getElementById("pilihanPaket").value;
            const semuaPaket = document.querySelectorAll(".paket-item");

            let jumlahHasil = 0;

            semuaPaket.forEach(function(kartu) {

                const idPaket = kartu.dataset.idPaket;

                if (pilihanPaket === "" || idPaket === pilihanPaket) {
                    kartu.classList.remove("d-none");
                    jumlahHasil++;
                } else {
                    kartu.classList.add("d-none");
                }

            });

            document.getElementById("paket_wisata").scrollIntoView({
                behavior: "smooth"
            });

            if (jumlahHasil === 0) {
                alert("Paket wisata tidak ditemukan.");
            }

        }
    </script>

    <!-- BOOTSTRAP JAVASCRIPT -->
    <script src="bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>