<?php
require_once __DIR__ . "/config/koneksi.php";

// Ambil paket wisata yang aktif
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

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>RPBNusa</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
        href="bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

</head>

<body class="bg-light">


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top"
        style="background-color: #0D47A1;">

        <div class="container">

            <!-- Logo / Nama Website -->

            <a class="navbar-brand fw-bold"
                href="index.php">

                ✈️ RPBNusa

            </a>


            <!-- Tombol Mobile -->

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarLanding"
                aria-controls="navbarLanding"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- Menu -->

            <div class="collapse navbar-collapse"
                id="navbarLanding">

                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

                    <li class="nav-item">

                        <a class="nav-link active"
                            href="#home">

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

                        <a class="nav-link"
                            href="#tentang">

                            Tentang Kami

                        </a>

                    </li>

                </ul>


                <!-- Tombol Login/Register -->

                <div class="d-flex gap-2">

                    <a href="login/login.php"
                        class="btn btn-light">

                        Login

                    </a>


                    <a href="login/register.php"
                        class="btn btn-outline-light">

                        Daftar

                    </a>

                </div>

            </div>

        </div>

    </nav>



    <!-- =====================================================
         HERO
    ====================================================== -->

    <section id="home"
        class="py-5"
        style="background-color: #E3F2FD;">

        <div class="container">

            <div class="row align-items-center g-5">


                <!-- Teks -->

                <div class="col-lg-6">

                    <span class="badge rounded-pill text-white px-3 py-2 mb-3"
                        style="background-color: #2196F3;">

                        ✈️ Booking Liburan

                    </span>


                    <h1 class="display-4 fw-bold"
                        style="color: #0D47A1;">

                        Temukan Liburan
                        <br>

                        <span style="color: #2196F3;">

                            Impianmu

                        </span>

                    </h1>


                    <p class="lead text-secondary mt-3">

                        Nikmati pengalaman liburan yang
                        menyenangkan dengan berbagai pilihan
                        paket wisata menarik dan terjangkau.

                    </p>


                    <div class="d-flex gap-2 flex-wrap mt-4">

                        <a href="#paket_wisata"
                            class="btn btn-lg text-white px-4"
                            style="background-color: #2196F3;">

                            Jelajahi Wisata

                        </a>


                        <a href="login/login.php"
                            class="btn btn-lg btn-outline-primary px-4">

                            Login

                        </a>

                    </div>

                </div>



                <!-- Gambar Hero -->

                <div class="col-lg-6">

                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow">

                        <?php
                        $gambar_hero = "uploads/paket_wisata/bali.jpg";
                        ?>

                        <?php if (file_exists($gambar_hero)): ?>

                            <img
                                src="<?= $gambar_hero; ?>"
                                class="img-fluid object-fit-cover"
                                alt="Destinasi wisata">

                        <?php else: ?>

                            <div class="d-flex align-items-center justify-content-center text-white"
                                style="background-color: #90CAF9;">

                                <div class="text-center">

                                    <div class="display-1">
                                        🌴
                                    </div>

                                    <h4 style="color: #0D47A1;">
                                        RPBNusa
                                    </h4>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =====================================================
         INFORMASI
    ====================================================== -->

    <section class="py-4"
        style="background-color: #90CAF9;">

        <div class="container">

            <div class="row g-3 text-center">


                <!-- Item 1 -->

                <div class="col-md-4">

                    <div class="bg-white rounded-3 shadow-sm p-4 h-100">

                        <div class="fs-1 mb-2">
                            🌴
                        </div>

                        <h5 class="fw-bold"
                            style="color: #0D47A1;">

                            Destinasi Menarik

                        </h5>

                        <p class="text-secondary mb-0">

                            Berbagai pilihan destinasi
                            wisata untuk liburanmu.

                        </p>

                    </div>

                </div>



                <!-- Item 2 -->

                <div class="col-md-4">

                    <div class="bg-white rounded-3 shadow-sm p-4 h-100">

                        <div class="fs-1 mb-2">
                            💰
                        </div>

                        <h5 class="fw-bold"
                            style="color: #0D47A1;">

                            Harga Terjangkau

                        </h5>

                        <p class="text-secondary mb-0">

                            Pilihan paket wisata dengan
                            harga yang sesuai kebutuhan.

                        </p>

                    </div>

                </div>



                <!-- Item 3 -->

                <div class="col-md-4">

                    <div class="bg-white rounded-3 shadow-sm p-4 h-100">

                        <div class="fs-1 mb-2">
                            📅
                        </div>

                        <h5 class="fw-bold"
                            style="color: #0D47A1;">

                            Booking Mudah

                        </h5>

                        <p class="text-secondary mb-0">

                            Pesan paket wisata dengan
                            proses yang mudah dan praktis.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =====================================================
         PAKET WISATA
    ====================================================== -->

    <section id="paket_wisata"
        class="py-5">

        <div class="container">


            <!-- Judul -->

            <div class="text-center mb-5">

                <span class="badge rounded-pill text-white px-3 py-2 mb-2"
                    style="background-color: #2196F3;">

                    PAKET WISATA

                </span>


                <h2 class="fw-bold"
                    style="color: #0D47A1;">

                    Jelajahi Destinasi Pilihan

                </h2>


                <p class="text-secondary">

                    Temukan paket wisata yang cocok
                    untuk perjalananmu.

                </p>

            </div>



            <!-- Card Paket -->

            <div class="row g-4">

                <?php if (mysqli_num_rows($query) > 0): ?>

                    <?php while ($paket = mysqli_fetch_assoc($query)): ?>


                        <div class="col-md-6 col-lg-4">

                            <div class="card border-0 shadow-sm h-100 overflow-hidden">


                                <!-- Foto -->

                                <div class="ratio ratio-16x9">

                                    <?php if (!empty($paket['gambar'])): ?>

                                        <img
                                            src="uploads/paket_wisata/<?= htmlspecialchars($paket['gambar']); ?>"
                                            class="card-img-top object-fit-cover"
                                            alt="<?= htmlspecialchars($paket['nama_paket']); ?>">

                                    <?php else: ?>

                                        <div class="d-flex align-items-center justify-content-center text-white"
                                            style="background-color: #90CAF9;">

                                            <span class="fs-1">
                                                🌴
                                            </span>

                                        </div>

                                    <?php endif; ?>

                                </div>



                                <!-- Isi Card -->

                                <div class="card-body d-flex flex-column">


                                    <h5 class="card-title fw-bold"
                                        style="color: #0D47A1;">

                                        <?= htmlspecialchars($paket['nama_paket']); ?>

                                    </h5>


                                    <p class="text-secondary mb-2">

                                        📍
                                        <?= htmlspecialchars($paket['destinasi']); ?>

                                    </p>


                                    <p class="mb-2">

                                        🕐
                                        <?= htmlspecialchars($paket['durasi']); ?>
                                        Hari

                                    </p>


                                    <h5 class="fw-bold"
                                        style="color: #2196F3;">

                                        Rp
                                        <?= number_format(
                                            $paket['harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>

                                    </h5>


                                    <p class="text-secondary">

                                        <?= htmlspecialchars($paket['deskripsi']); ?>

                                    </p>


                                    <!-- Tombol -->

                                    <div class="mt-auto pt-3">

                                        <a
                                            href="login/login.php"
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


                    <!-- Tidak ada paket -->

                    <div class="col-12">

                        <div class="alert text-center"
                            style="background-color: #E3F2FD; color: #0D47A1;">

                            <h5 class="fw-bold">

                                Belum Ada Paket Wisata

                            </h5>

                            <p class="mb-0">

                                Saat ini belum tersedia paket wisata
                                yang dapat ditampilkan.

                            </p>

                        </div>

                    </div>


                <?php endif; ?>

            </div>

        </div>

    </section>



    <!-- =====================================================
         TENTANG KAMI
    ====================================================== -->

    <section id="tentang"
        class="py-5"
        style="background-color: #E3F2FD;">

        <div class="container">

            <div class="row align-items-center g-4">


                <div class="col-lg-6">

                    <h2 class="fw-bold"
                        style="color: #0D47A1;">

                        Tentang RPBNusa

                    </h2>

                    <p class="text-secondary mt-3">

                        RPBNusa merupakan platform
                        yang membantu pengguna menemukan dan
                        melakukan pemesanan paket wisata dengan
                        mudah.

                    </p>

                    <p class="text-secondary">

                        Kami menyediakan berbagai pilihan
                        destinasi wisata dengan informasi
                        mengenai durasi, harga, dan deskripsi
                        paket.

                    </p>

                </div>


                <div class="col-lg-6">

                    <div class="bg-white rounded-4 shadow-sm p-4">

                        <h5 class="fw-bold"
                            style="color: #0D47A1;">

                            Kenapa memilih kami?

                        </h5>


                        <div class="d-flex gap-3 mt-4">

                            <div class="fs-4">
                                ✓
                            </div>

                            <div>

                                <h6 class="fw-bold mb-1">

                                    Informasi Lengkap

                                </h6>

                                <p class="text-secondary mb-0">

                                    Lihat informasi paket wisata
                                    sebelum melakukan booking.

                                </p>

                            </div>

                        </div>


                        <div class="d-flex gap-3 mt-4">

                            <div class="fs-4">
                                ✓
                            </div>

                            <div>

                                <h6 class="fw-bold mb-1">

                                    Proses Mudah

                                </h6>

                                <p class="text-secondary mb-0">

                                    Booking paket wisata dengan
                                    proses yang sederhana.

                                </p>

                            </div>

                        </div>


                        <div class="d-flex gap-3 mt-4">

                            <div class="fs-4">
                                ✓
                            </div>

                            <div>

                                <h6 class="fw-bold mb-1">

                                    Pilihan Beragam

                                </h6>

                                <p class="text-secondary mb-0">

                                    Tersedia berbagai destinasi
                                    wisata untuk dipilih.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =====================================================
         CALL TO ACTION
    ====================================================== -->

    <section class="py-5"
        style="background-color: #0D47A1;">

        <div class="container text-center">

            <h2 class="fw-bold text-white">

                Siap Merencanakan Liburanmu?

            </h2>


            <p class="text-white-50">

                Daftar sekarang dan mulai pilih
                paket wisata favoritmu.

            </p>


            <div class="d-flex justify-content-center gap-2">

                <a href="login/register.php"
                    class="btn btn-light px-4">

                    Daftar Sekarang

                </a>


                <a href="login/login.php"
                    class="btn btn-outline-light px-4">

                    Login

                </a>

            </div>

        </div>

    </section>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="bg-white py-4">

        <div class="container">

            <div class="row align-items-center">


                <div class="col-md-6 text-center text-md-start">

                    <h5 class="fw-bold"
                        style="color: #0D47A1;">

                        ✈️ RPBNusa

                    </h5>

                    <p class="text-secondary mb-0">

                        Temukan liburan impianmu.

                    </p>

                </div>


                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">

                    <p class="text-secondary mb-0">

                        &copy; <?= date('Y'); ?>
                        RPBNusa

                    </p>

                </div>

            </div>

        </div>

    </footer>



    <!-- Bootstrap JS -->

    <script src="bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>