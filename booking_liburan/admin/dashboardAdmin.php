<?php
session_start();

require_once __DIR__ . "/../config/koneksi.php";

if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../login/login.php");
    exit;
}

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

$halamanAktif = 'dashboard';
$judulHalaman = 'Dashboard Admin';

include __DIR__ . '/partials/header.php';
?>

<!-- KONTEN DASHBOARD DIMULAI DI SINI -->

<div class="mb-4">
    <h1 class="page-title">Dashboard Admin</h1>
    <p class="page-description">
        Kelola seluruh sistem booking paket liburan dengan mudah.
    </p>
</div>

<!-- KARTU STATISTIK -->
<div class="row g-4 mb-5">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon icon-blue">
                    <i class="bi bi-map-fill"></i>
                </div>
                <div class="stat-label">Paket Wisata</div>
                <div class="stat-number"><?= $totalPaket; ?></div>
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
                <div class="stat-icon icon-green">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-label">Pengguna</div>
                <div class="stat-number"><?= $totalPengguna; ?></div>
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
                <div class="stat-icon icon-yellow">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="stat-label">Booking</div>
                <div class="stat-number"><?= $totalBooking; ?></div>
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
                <div class="stat-icon icon-cyan">
                    <i class="bi bi-credit-card-fill"></i>
                </div>
                <div class="stat-label">Pembayaran</div>
                <div class="stat-number"><?= $totalPembayaran; ?></div>
                <a href="pembayaran/index.php"
                    class="btn btn-primary btn-sm">
                    Lihat Pembayaran
                </a>
            </div>
        </div>
    </div>

</div>

<!-- TABEL BOOKING -->
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
                                } elseif (
                                    $status === 'Ditolak' ||
                                    $status === 'Dibatalkan'
                                ) {
                                    $badge = 'bg-danger';
                                } else {
                                    $badge = 'bg-warning text-dark';
                                }
                        ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($b['nama_pengguna'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($b['nama_paket'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($b['tanggal_booking'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($b['tanggal_keberangkatan'] ?? '-'); ?></td>
                                    <td><?= (int) ($b['jumlah_peserta'] ?? 0); ?> orang</td>
                                    <td class="text-nowrap">
                                        Rp <?= number_format((float) ($b['total_harga'] ?? 0), 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $badge; ?>">
                                            <?= htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="booking/index.php"
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

<div class="footer-text mt-4">
    RPBNusa &copy; <?= date('Y'); ?> — Sistem Booking Paket Wisata
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>