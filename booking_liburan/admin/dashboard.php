<?php
session_start();

require_once __DIR__ . "/../config/koneksi.php";

// Pastikan hanya admin yang dapat mengakses dashboard.
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../login/login.php");
    exit;
}

// Mengambil jumlah data dari database.
function hitungData($conn, $tabel)
{
    $tabelDiizinkan = ['paket_wisata', 'user', 'booking'];

    if (!in_array($tabel, $tabelDiizinkan, true)) {
        return 0;
    }

    $query = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM `$tabel`"
    );

    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);

    return (int) ($data['total'] ?? 0);
}

$totalPaket = hitungData($conn, 'paket_wisata');
$totalPengguna = hitungData($conn, 'user');
$totalBooking = hitungData($conn, 'booking');

// Menghitung seluruh data pembayaran.
$queryPembayaran = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM pembayaran"
);

$totalPembayaran = 0;

if ($queryPembayaran) {
    $dataPembayaran = mysqli_fetch_assoc($queryPembayaran);
    $totalPembayaran = (int) ($dataPembayaran['total'] ?? 0);
}

$namaAdmin = $_SESSION['nama'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - RPBNusa</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <style>
        :root {
            --sidebar-width: 255px;
            --sidebar-bg: #173f70;
            --sidebar-active: #24558f;
            --primary: #0866f5;
            --page-bg: #f1f5fa;
            --text-main: #183153;
            --text-muted: #6b7b90;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--page-bg);
            color: var(--text-main);
            font-family: Arial, Helvetica, sans-serif;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            overflow-y: auto;
            transition: transform 0.25s ease;
        }

        .brand {
            color: white;
            text-decoration: none;
            font-size: 25px;
            font-weight: 800;
            padding: 0 12px;
            margin-bottom: 4px;
        }

        .brand:hover {
            color: white;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #d5e5fa;
            padding: 0 12px;
        }

        .sidebar-heading {
            font-size: 11px;
            letter-spacing: 1.5px;
            font-weight: bold;
            color: #c1d6ef;
            margin: 34px 12px 12px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin-bottom: 5px;
            color: #fff;
            text-decoration: none;
            border-radius: 9px;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--sidebar-active);
            color: white;
        }

        .sidebar-icon {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .sidebar-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: auto;
            padding-top: 18px;
        }

        /* KONTEN UTAMA */
        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        .topbar {
            min-height: 76px;
            background: white;
            border-bottom: 1px solid #e7edf5;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: bold;
            margin: 0;
        }

        .admin-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dceaff;
            color: #0755c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .content-area {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 7px;
            color: #173f70;
        }

        .page-description {
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        /* STATISTIK */
        .stat-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(24, 49, 83, 0.06);
            height: 100%;
            transition: transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .stat-card:hover,
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 24px rgba(24, 49, 83, 0.1);
        }

        .stat-card .card-body {
            padding: 22px;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .icon-blue {
            background: #dceaff;
        }

        .icon-green {
            background: #d8f2e6;
        }

        .icon-yellow {
            background: #fff0c7;
        }

        .icon-cyan {
            background: #d7f5fb;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 30px;
            line-height: 1.2;
            font-weight: 800;
            color: #183153;
            margin-bottom: 14px;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 7px;
        }

        .btn-primary:hover {
            background: #0755c9;
            border-color: #0755c9;
        }

        /* MENU PENGELOLAAN */
        .section-title {
            font-size: 23px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .section-description {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .menu-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(24, 49, 83, 0.06);
            height: 100%;
            transition: transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .menu-card .card-body {
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .menu-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            font-size: 23px;
            margin-bottom: 17px;
        }

        .menu-card h5 {
            font-size: 19px;
            font-weight: 750;
            margin-bottom: 10px;
            color: #182b45;
        }

        .menu-card p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.65;
            margin-bottom: 20px;
        }

        .menu-card .btn {
            margin-top: auto;
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 12px;
            text-align: center;
            padding: 25px 0 5px;
        }

        .sidebar-overlay {
            display: none;
        }

        .menu-toggle {
            display: none;
        }

        /* TAMPILAN TABLET DAN HP */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar-overlay.show {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1030;
            }

            .content-area {
                padding: 24px;
            }

            .topbar {
                padding: 15px 24px;
            }
        }

        @media (max-width: 575.98px) {
            .content-area {
                padding: 20px 15px;
            }

            .topbar {
                padding: 13px 15px;
            }

            .page-title {
                font-size: 24px;
            }

            .topbar-title {
                font-size: 15px;
            }

            .admin-name {
                display: none;
            }

            .stat-card .card-body,
            .menu-card .card-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">

    <a href="dashboard.php" class="brand">
        <span class="brand-icon">
            <i class="bi bi-airplane-fill"></i>
        </span>
        <span>ADF Travel</span>
    </a>

    <div class="brand-subtitle">Admin Panel</div>

    <div class="sidebar-heading">MENU UTAMA</div>

    <a href="dashboard.php" class="sidebar-link active">
        <i class="bi bi-grid-1x2-fill sidebar-icon"></i>
        <span>Dashboard</span>
    </a>

    <a href="paket_wisata/index.php" class="sidebar-link">
        <i class="bi bi-map-fill sidebar-icon"></i>
        <span>Paket Wisata</span>
    </a>

    <a href="pengguna/index.php" class="sidebar-link">
        <i class="bi bi-people-fill sidebar-icon"></i>
        <span>Data Pengguna</span>
    </a>

    <a href="booking/index.php" class="sidebar-link">
        <i class="bi bi-calendar2-check-fill sidebar-icon"></i>
        <span>Data Booking</span>
    </a>

    <a href="pembayaran/index.php" class="sidebar-link">
        <i class="bi bi-credit-card-2-front-fill sidebar-icon"></i>
        <span>Pembayaran</span>
    </a>

    <div class="sidebar-heading">LAPORAN DAN LAINNYA</div>

    <a href="laporan/index.php" class="sidebar-link">
        <i class="bi bi-file-earmark-bar-graph-fill sidebar-icon"></i>
        <span>Laporan Booking</span>
    </a>

    <div class="sidebar-bottom">
        <a href="../logout.php"
            class="sidebar-link"
            onclick="return confirm('Yakin ingin logout?');">
            <i class="bi bi-box-arrow-right sidebar-icon"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>


    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- KONTEN -->
    <main class="main-content">

        <!-- NAVBAR -->
        <nav class="topbar">

            <div class="d-flex align-items-center gap-3">
                <button type="button"
                    class="btn btn-outline-primary menu-toggle"
                    id="menuToggle"
                    aria-label="Buka menu">
                    ☰
                </button>

                <p class="topbar-title">Dashboard Admin</p>
            </div>

            <div class="admin-label">
                <div class="admin-avatar">
                    <?= strtoupper(substr($namaAdmin, 0, 1)); ?>
                </div>

                <span class="admin-name">
                    <?= htmlspecialchars($namaAdmin); ?>
                </span>

                <a href="../logout.php"
                    class="btn btn-outline-secondary btn-sm"
                    onclick="return confirm('Yakin ingin logout?');">
                    Logout
                </a>
            </div>

        </nav>

        <div class="content-area">

            <!-- JUDUL -->
            <div class="mb-4">
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="page-description">
                    Kelola seluruh sistem booking paket liburan dengan mudah.
                </p>
            </div>

            <!-- STATISTIK -->
            <div class="row g-4 mb-5">

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-icon icon-blue">🌴</div>
                            <div class="stat-label">Paket Wisata</div>
                            <div class="stat-number">
                                <?= $totalPaket; ?>
                            </div>
                            <a href="paket_wisata/index.php"
                                class="btn btn-primary btn-sm">
                                Kelola Paket
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-icon icon-green">♙</div>
                            <div class="stat-label">Pengguna</div>
                            <div class="stat-number">
                                <?= $totalPengguna; ?>
                            </div>
                            <a href="pengguna/index.php"
                                class="btn btn-primary btn-sm">
                                Data Pengguna
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-icon icon-yellow">▣</div>
                            <div class="stat-label">Booking</div>
                            <div class="stat-number">
                                <?= $totalBooking; ?>
                            </div>
                            <a href="booking/index.php"
                                class="btn btn-primary btn-sm">
                                Kelola Booking
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-icon icon-cyan">💳</div>
                            <div class="stat-label">Pembayaran</div>
                            <div class="stat-number">
                                <?= $totalPembayaran; ?>
                            </div>
                            <a href="pembayaran/index.php"
                                class="btn btn-primary btn-sm">
                                Lihat Pembayaran
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Menu Pengelolaan: Data Booking -->
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Menu Pengelolaan</h4>
                        <p class="text-muted mb-0">
                            Daftar pemesanan paket wisata pelanggan.
                        </p>
                    </div>

                    <a href="booking/index.php" class="btn btn-primary">
                        Kelola Booking
                    </a>
                </div>

                <?php
                $query_data_booking = mysqli_query($conn, "
        SELECT
            b.*,
            u.nama AS nama_pengguna,
            w.nama_paket
        FROM booking b
        LEFT JOIN `user` u ON b.id_user = u.id_user
        LEFT JOIN paket_wisata w ON b.id_paket = w.id_paket
        ORDER BY b.id_booking DESC
    ");

                if (!$query_data_booking) {
                    die('Gagal mengambil data booking: ' . mysqli_error($conn));
                }
                ?>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Pengguna</th>
                                        <th>Paket Wisata</th>
                                        <th>Tanggal Booking</th>
                                        <th>Keberangkatan</th>
                                        <th>Peserta</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no = 1;

                                    if (mysqli_num_rows($query_data_booking) > 0):
                                        while ($b = mysqli_fetch_assoc($query_data_booking)):

                                            $status = $b['status_booking'] ?? 'Menunggu';

                                            if ($status === 'Dikonfirmasi') {
                                                $badge = 'bg-success';
                                            } elseif ($status === 'Ditolak') {
                                                $badge = 'bg-danger';
                                            } else {
                                                $badge = 'bg-warning text-dark';
                                            }

                                            $total = $b['total_harga'] ?? 0;
                                    ?>
                                            <tr>
                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <?= htmlspecialchars($b['nama_pengguna'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($b['nama_paket'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($b['tanggal_booking'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($b['tanggal_keberangkatan'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= (int) ($b['jumlah_peserta'] ?? 0); ?> orang
                                                </td>

                                                <td class="text-nowrap">
                                                    Rp <?= number_format((float) $total, 0, ',', '.'); ?>
                                                </td>

                                                <td>
                                                    <span class="badge <?= $badge; ?>">
                                                        <?= htmlspecialchars($status); ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <a
                                                        href="booking/index.php"
                                                        class="btn btn-primary btn-sm">
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                Belum ada data booking.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-text">
                RPBNusa &copy; <?= date('Y'); ?> — Sistem Booking Paket Wisata
            </div>

        </div>
    </main>

    <script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuToggle = document.getElementById('menuToggle');

        function bukaSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        }

        function tutupSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }

        menuToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('show')) {
                tutupSidebar();
            } else {
                bukaSidebar();
            }
        });

        overlay.addEventListener('click', tutupSidebar);

        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    tutupSidebar();
                }
            });
        });
    </script>

</body>

</html>