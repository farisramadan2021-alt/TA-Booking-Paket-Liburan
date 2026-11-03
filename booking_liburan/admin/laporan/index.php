<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

// ===============================
// CEK LOGIN ADMIN
// ===============================
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../login/login.php");
    exit;
}

$halamanAktif = 'laporan';
$judulHalaman = 'Laporan Booking dan Statistik Pemesanan';
$namaAdmin = $_SESSION['nama'] ?? 'Admin';

// ===============================
// FILTER TANGGAL
// ===============================
$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalSelesai = $_GET['tanggal_selesai'] ?? '';

if (
    $tanggalMulai !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalMulai)
) {
    $tanggalMulai = '';
}

if (
    $tanggalSelesai !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalSelesai)
) {
    $tanggalSelesai = '';
}

$where = [];
$params = [];
$types = '';

if ($tanggalMulai !== '') {
    $where[] = "DATE(b.tanggal_booking) >= ?";
    $params[] = $tanggalMulai;
    $types .= 's';
}

if ($tanggalSelesai !== '') {
    $where[] = "DATE(b.tanggal_booking) <= ?";
    $params[] = $tanggalSelesai;
}

$whereSQL = '';

if (!empty($where)) {
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

// ===============================
// RINGKASAN BOOKING DAN PENDAPATAN
// ===============================
// Pendapatan hanya dihitung dari booking Dikonfirmasi.
// Jumlah booking dan rating mengikuti filter tanggal.

$sqlRingkasan = "
    SELECT
        COUNT(
            CASE
                WHEN b.status_booking = 'Dikonfirmasi'
                THEN b.id_booking
            END
        ) AS jumlah_booking,

        COALESCE(
            SUM(
                CASE
                    WHEN b.status_booking = 'Dikonfirmasi'
                    THEN b.total_harga
                    ELSE 0
                END
            ),
            0
        ) AS total_pendapatan,

        AVG(
            CASE
                WHEN b.status_booking = 'Dikonfirmasi'
                THEN ul.rating
            END
        ) AS rata_rating

    FROM booking b
    LEFT JOIN ulasan ul
        ON b.id_booking = ul.id_booking

    $whereSQL
";

$stmtRingkasan = mysqli_prepare($conn, $sqlRingkasan);

if (!$stmtRingkasan) {
    die("Gagal menyiapkan ringkasan: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmtRingkasan, $types, ...$params);
}

mysqli_stmt_execute($stmtRingkasan);

$resultRingkasan = mysqli_stmt_get_result($stmtRingkasan);
$ringkasan = mysqli_fetch_assoc($resultRingkasan);

$jumlahBooking = (int) ($ringkasan['jumlah_booking'] ?? 0);
$totalPendapatan = (float) ($ringkasan['total_pendapatan'] ?? 0);
$rataRating = $ringkasan['rata_rating'] !== null
    ? (float) $ringkasan['rata_rating']
    : 0;

mysqli_stmt_close($stmtRingkasan);

// ===============================
// STATISTIK STATUS BOOKING
// ===============================
$sqlStatistikStatus = "
    SELECT
        b.status_booking,
        COUNT(*) AS jumlah
    FROM booking b
    $whereSQL
    GROUP BY b.status_booking
";

$stmtStatistikStatus = mysqli_prepare($conn, $sqlStatistikStatus);

if (!$stmtStatistikStatus) {
    die("Gagal menyiapkan statistik status: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param(
        $stmtStatistikStatus,
        $types,
        ...$params
    );
}

mysqli_stmt_execute($stmtStatistikStatus);

$queryStatistikStatus = mysqli_stmt_get_result($stmtStatistikStatus);

$statistikStatus = [];

while ($dataStatus = mysqli_fetch_assoc($queryStatistikStatus)) {
    $statistikStatus[$dataStatus['status_booking']] =
        (int) $dataStatus['jumlah'];
}

mysqli_stmt_close($stmtStatistikStatus);

$jumlahMenunggu = $statistikStatus['Menunggu'] ?? 0;
$jumlahDikonfirmasi = $statistikStatus['Dikonfirmasi'] ?? 0;
$jumlahDitolak = $statistikStatus['Ditolak'] ?? 0;
$jumlahDibatalkan = $statistikStatus['Dibatalkan'] ?? 0;

// ===============================
// DETAIL LAPORAN BOOKING
// ===============================
$sqlLaporan = "
    SELECT
        b.id_booking,
        b.tanggal_booking,
        b.jumlah_peserta,
        b.total_harga,
        b.status_booking,

        u.nama AS nama_pengguna,
        p.nama_paket,

        ul.rating,
        ul.komentar

    FROM booking b

    LEFT JOIN `user` u
        ON b.id_user = u.id_user

    LEFT JOIN paket_wisata p
        ON b.id_paket = p.id_paket

    LEFT JOIN ulasan ul
        ON b.id_booking = ul.id_booking

    $whereSQL

    ORDER BY b.tanggal_booking DESC
";

$stmtLaporan = mysqli_prepare($conn, $sqlLaporan);

if (!$stmtLaporan) {
    die("Gagal menyiapkan laporan: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param(
        $stmtLaporan,
        $types,
        ...$params
    );
}

mysqli_stmt_execute($stmtLaporan);

$queryLaporan = mysqli_stmt_get_result($stmtLaporan);

// ===============================
// FORMAT RUPIAH
// ===============================
function rupiah($angka)
{
    return "Rp " . number_format((float) $angka, 0, ',', '.');
}

include __DIR__ . '/../partials/header.php';
?>

<div class="container-fluid">

    <!-- JUDUL HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                Laporan Booking dan Statistik Pemesanan
            </h3>

            <p class="text-muted mb-0">
                Ringkasan dan detail pemesanan paket wisata.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            onclick="window.print()"
        >
            <i class="bi bi-printer me-1"></i>
            Cetak Laporan
        </button>
    </div>

    <!-- FILTER TANGGAL -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label for="tanggal_mulai" class="form-label">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        id="tanggal_mulai"
                        class="form-control"
                        value="<?= htmlspecialchars($tanggalMulai); ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label for="tanggal_selesai" class="form-label">
                        Tanggal Selesai
                    </label>

                    <input
                        type="date"
                        name="tanggal_selesai"
                        id="tanggal_selesai"
                        class="form-control"
                        value="<?= htmlspecialchars($tanggalSelesai); ?>"
                    >
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Cari
                    </button>

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        Reset
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- RINGKASAN -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Booking Dikonfirmasi
                    </div>

                    <h3 class="fw-bold mb-0">
                        <?= number_format($jumlahBooking); ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Total Pendapatan
                    </div>

                    <h3 class="fw-bold mb-0">
                        <?= rupiah($totalPendapatan); ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Rata-rata Rating
                    </div>

                    <h3 class="fw-bold mb-0">
                        <?= number_format($rataRating, 1); ?>
                        <small class="text-warning">
                            <i class="bi bi-star-fill"></i>
                        </small>
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <!-- STATISTIK STATUS BOOKING -->
    <h5 class="fw-bold mb-3">
        Statistik Status Booking
    </h5>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Menunggu
                    </div>

                    <h3 class="fw-bold text-warning mb-0">
                        <?= number_format($jumlahMenunggu); ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Dikonfirmasi
                    </div>

                    <h3 class="fw-bold text-success mb-0">
                        <?= number_format($jumlahDikonfirmasi); ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Ditolak
                    </div>

                    <h3 class="fw-bold text-danger mb-0">
                        <?= number_format($jumlahDitolak); ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Dibatalkan
                    </div>

                    <h3 class="fw-bold text-secondary mb-0">
                        <?= number_format($jumlahDibatalkan); ?>
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TABEL LAPORAN -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Detail Laporan Booking
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table
                    id="tabelLaporan"
                    class="table table-bordered table-striped align-middle"
                >
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Booking</th>
                            <th>Nama Pengguna</th>
                            <th>Paket Wisata</th>
                            <th>Tanggal Booking</th>
                            <th>Jumlah Peserta</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        while ($b = mysqli_fetch_assoc($queryLaporan)):
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <?= (int) $b['id_booking']; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $b['nama_pengguna'] ?? '-'
                                    ); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $b['nama_paket'] ?? '-'
                                    ); ?>
                                </td>

                                <td>
                                    <?= !empty($b['tanggal_booking'])
                                        ? date(
                                            'd-m-Y',
                                            strtotime($b['tanggal_booking'])
                                        )
                                        : '-'; ?>
                                </td>

                                <td>
                                    <?= (int) $b['jumlah_peserta']; ?>
                                </td>

                                <td>
                                    <?= rupiah($b['total_harga']); ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $b['status_booking'] ?? '';

                                    $badgeStatus = 'secondary';

                                    if ($status === 'Menunggu') {
                                        $badgeStatus = 'warning';
                                    } elseif ($status === 'Dikonfirmasi') {
                                        $badgeStatus = 'success';
                                    } elseif ($status === 'Ditolak') {
                                        $badgeStatus = 'danger';
                                    } elseif ($status === 'Dibatalkan') {
                                        $badgeStatus = 'secondary';
                                    }
                                    ?>

                                    <span class="badge bg-<?= $badgeStatus; ?>">
                                        <?= htmlspecialchars($status); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($b['rating'] !== null): ?>
                                        <?= (int) $b['rating']; ?>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    <?php else: ?>
                                        <span class="text-muted">
                                            Belum ada
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($b['komentar'] !== null): ?>
                                        <?= htmlspecialchars($b['komentar']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">
                                            Belum ada ulasan
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<style>
@media print {
    .sidebar,
    .navbar,
    .btn,
    form,
    footer {
        display: none !important;
    }

    body {
        background: #fff !important;
    }

    .container-fluid {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }

    table {
        font-size: 10px !important;
    }

    th,
    td {
        padding: 5px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabelLaporan').DataTable({
            pageLength: 10,
            order: [],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });
    }
});
</script>

<?php
mysqli_stmt_close($stmtLaporan);

include __DIR__ . '/../partials/footer.php';
?>