<?php
require_once __DIR__ . "/../config/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Booking Liburan</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">

            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
            </a>

            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    Admin
                </span>

                <a href="../logout.php"
                    class="btn btn-light btn-sm">
                    Logout
                </a>
            </div>

        </div>
    </nav>


    <!-- Isi Dashboard -->
    <div class="container py-5">

        <!-- Judul -->
        <div class="mb-4">
            <h2 class="fw-bold">
                Dashboard Admin
            </h2>

            <p class="text-muted">
                Kelola seluruh sistem booking paket liburan dengan mudah.
            </p>
        </div>


        <!-- Statistik -->
        <div class="row g-4 mb-5">

            <!-- Paket Wisata -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Paket Wisata
                        </h6>

                        <h2 class="fw-bold">
                            <?php
                            $query_paket = mysqli_query(
                                $conn,
                                "SELECT COUNT(*) AS total FROM paket_wisata"
                            );

                            $paket = mysqli_fetch_assoc($query_paket);

                            echo $paket['total'];
                            ?>
                        </h2>

                        <a href="paket_wisata/index.php"
                            class="btn btn-primary btn-sm">
                            Kelola Paket
                        </a>

                    </div>

                </div>

            </div>


            <!-- Pengguna -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Pengguna
                        </h6>

                        <h2 class="fw-bold">
                            0
                        </h2>

                        <a href="pengguna/index.php"
                            class="btn btn-primary btn-sm">
                            Data Pengguna
                        </a>

                    </div>

                </div>

            </div>


            <!-- Booking -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Booking
                        </h6>

                        <h2 class="fw-bold">
                            0
                        </h2>

                        <a href="booking/index.php"
                            class="btn btn-primary btn-sm">
                            Kelola Booking
                        </a>

                    </div>

                </div>

            </div>


            <!-- Laporan -->
            <div class="col-md-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Laporan
                        </h6>

                        <h2 class="fw-bold">
                            0
                        </h2>

                        <a href="laporan/index.php"
                            class="btn btn-primary btn-sm">
                            Lihat Laporan
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- Menu Utama -->
        <h4 class="fw-bold mb-3">
            Menu Admin
        </h4>

        <div class="row g-4">

            <!-- Paket Wisata -->
            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold">
                            Paket Wisata
                        </h5>

                        <p class="text-muted">
                            Kelola data paket wisata yang tersedia.
                        </p>

                        <a href="paket_wisata/index.php"
                            class="btn btn-primary">
                            Kelola Paket
                        </a>

                    </div>

                </div>

            </div>


            <!-- Pengguna -->
            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold">
                            Data Pengguna
                        </h5>

                        <p class="text-muted">
                            Melihat dan mengelola data pengguna.
                        </p>

                        <a href="pengguna/index.php"
                            class="btn btn-primary">
                            Kelola Pengguna
                        </a>

                    </div>

                </div>

            </div>


            <!-- Booking -->
            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold">
                            Data Booking
                        </h5>

                        <p class="text-muted">
                            Mengelola pemesanan paket liburan.
                        </p>

                        <a href="booking/index.php"
                            class="btn btn-primary">
                            Kelola Booking
                        </a>

                    </div>

                </div>

            </div>


            <!-- Pembayaran -->
            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold">
                            Pembayaran
                        </h5>

                        <p class="text-muted">
                            Memeriksa dan memverifikasi pembayaran.
                        </p>

                        <a href="pembayaran/index.php"
                            class="btn btn-primary">
                            Kelola Pembayaran
                        </a>

                    </div>

                </div>

            </div>


            <!-- Laporan -->
            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold">
                            Laporan Booking
                        </h5>

                        <p class="text-muted">
                            Melihat laporan data pemesanan.
                        </p>

                        <a href="laporan/index.php"
                            class="btn btn-primary">
                            Lihat Laporan
                        </a>

                    </div>

                </div>

            </div>


            <!-- Pengaturan -->
            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold">
                            Pengaturan
                        </h5>

                        <p class="text-muted">
                            Pengaturan akun dan sistem admin.
                        </p>

                        <a href="#" class="btn btn-secondary">
                            Pengaturan
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>