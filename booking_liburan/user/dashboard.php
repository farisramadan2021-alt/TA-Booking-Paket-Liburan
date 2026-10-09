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

    <title>RPBNusa</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg navbar-dark"
        style="background-color: #0D47A1;">

        <div class="container">

            <a class="navbar-brand fw-bold"
                href="dashboard.php">
                RPBNusa
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
                            href="../user/dashboard.php#paket_wisata">
                            Paket Wisata
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="riwayat_booking.php">
                            Riwayat Booking
                        </a>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-3">

                    <span class="text-white">
                        Halo,
                        <strong>
                            <?= htmlspecialchars($_SESSION['nama']); ?>
                        </strong>
                    </span>

                    <a href="../logout.php"
                        class="btn btn-light btn-sm">
                        Logout
                    </a>

                </div>

            </div>
        </div>
    </nav>


    <!-- ================= HERO ================= -->
    <section class="py-5"
        style="background-color: #E3F2FD;">

        <div class="container">

            <div class="row align-items-center g-4">

                <!-- Teks -->
                <div class="col-lg-6">

                    <span class="badge rounded-pill text-white mb-3"
                        style="background-color: #2196F3;">
                        ✈️ RPBNusa
                    </span>

                    <h1 class="display-5 fw-bold"
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
                            class="btn btn-lg text-white"
                            style="background-color: #2196F3;">
                            Jelajahi Paket Wisata
                        </a>

                    </div>

                </div>

                <!-- Gambar -->
                <div class="col-lg-6">

                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow">

                        <img
                            src="../uploads/paket_wisata/bali.jpg"
                            class="img-fluid object-fit-cover"
                            alt="Destinasi wisata">

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= INFORMASI ================= -->
    <section class="py-4"
        style="background-color: #90CAF9;">

        <div class="container">

            <div class="row text-center g-3">

                <div class="col-md-4">

                    <div class="bg-white rounded-3 p-3 shadow-sm">

                        <h4 class="fw-bold mb-1"
                            style="color: #0D47A1;">
                            🌴
                        </h4>

                        <h6 class="fw-bold">
                            Banyak Destinasi
                        </h6>

                        <p class="mb-0 text-secondary small">
                            Pilihan wisata menarik untuk liburanmu.
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="bg-white rounded-3 p-3 shadow-sm">

                        <h4 class="fw-bold mb-1"
                            style="color: #0D47A1;">
                            💳
                        </h4>

                        <h6 class="fw-bold">
                            Harga Terjangkau
                        </h6>

                        <p class="mb-0 text-secondary small">
                            Temukan paket wisata sesuai budget.
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="bg-white rounded-3 p-3 shadow-sm">

                        <h4 class="fw-bold mb-1"
                            style="color: #0D47A1;">
                            📅
                        </h4>

                        <h6 class="fw-bold">
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
        class="py-5">

        <div class="container">

            <div class="text-center mb-5">

                <span class="badge rounded-pill text-white mb-2"
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

                            <div class="card h-100 border-0 shadow-sm">

                                <!-- Gambar -->
                                <div class="ratio ratio-16x9">

                                    <?php if (!empty($paket['gambar'])): ?>

                                        <img
                                            src="../uploads/paket_wisata/<?= htmlspecialchars($paket['gambar']); ?>"
                                            class="card-img-top object-fit-cover rounded-top"
                                            alt="<?= htmlspecialchars($paket['nama_paket']); ?>">

                                    <?php else: ?>

                                        <div class="d-flex align-items-center justify-content-center bg-secondary text-white rounded-top">
                                            Tidak ada gambar
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- Isi Card -->
                                <div class="card-body d-flex flex-column">

                                    <h5 class="card-title fw-bold"
                                        style="color: #0D47A1;">

                                        <?= htmlspecialchars($paket['nama_paket']); ?>

                                    </h5>


                                    <p class="mb-2 text-secondary">

                                        📍
                                        <?= htmlspecialchars($paket['destinasi']); ?>

                                    </p>


                                    <p class="mb-2">

                                        🕐
                                        <?= htmlspecialchars($paket['durasi']); ?>
                                        Hari

                                    </p>


                                    <h5 class="fw-bold mb-3"
                                        style="color: #2196F3;">

                                        Rp <?= number_format(
                                                $paket['harga'],
                                                0,
                                                ',',
                                                '.'
                                            ); ?>

                                    </h5>


                                    <p class="card-text text-secondary">

                                        <?= htmlspecialchars($paket['deskripsi']); ?>

                                    </p>


                                    <div class="mt-auto pt-3">

                                        <a
                                            href="../booking/formBooking.php?id_paket=<?= $paket['id_paket']; ?>"
                                            class="btn text-white w-100"
                                            style="background-color: #2196F3;">

                                            Booking Sekarang

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="col-12">

                        <div class="alert text-center"
                            style="background-color: #E3F2FD; color: #0D47A1;">

                            Belum ada paket wisata yang tersedia.

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- ================= CTA ================= -->
    <section class="py-5"
        style="background-color: #0D47A1;">

        <div class="container text-center">

            <h2 class="text-white fw-bold">
                Siap Memulai Liburanmu?
            </h2>

            <p class="text-white-50">
                Pilih paket wisata dan lakukan booking sekarang.
            </p>

            <a href="#paket_wisata"
                class="btn btn-light px-4">
                Lihat Paket Wisata
            </a>

        </div>

    </section>


    <!-- ================= FOOTER ================= -->
    <footer class="py-4 bg-white">

        <div class="container text-center">

            <p class="mb-1 fw-bold"
                style="color: #0D47A1;">
                RPBNusa
            </p>

            <p class="mb-0 text-secondary small">
                &copy; <?= date('Y'); ?> RPBNusa.
                Semua hak dilindungi.
            </p>

        </div>

    </footer>


    <script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>