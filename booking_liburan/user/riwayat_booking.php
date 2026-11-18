
<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

<<<<<<< HEAD
// ==========================================
// FILTER STATUS BOOKING
// ==========================================

$filter_status = $_GET['status'] ?? '';

// Batasi pilihan status yang diperbolehkan
$status_valid = ['Menunggu', 'Dikonfirmasi', 'Ditolak'];

if (!in_array($filter_status, $status_valid, true)) {
    $filter_status = '';
}

// ==========================================
// AMBIL RIWAYAT BOOKING DAN PEMBAYARAN
// ==========================================
=======
// Daftar status yang valid beserta tampilan badge-nya
$daftar_status = [
    'Menunggu'            => ['badge' => 'bg-warning text-dark', 'btn' => 'warning text-dark'],
    'Dikonfirmasi'        => ['badge' => 'bg-success',           'btn' => 'success'],
    'Proses'              => ['badge' => 'bg-info text-dark',    'btn' => 'info text-dark'],
    'Menunggu Verifikasi' => ['badge' => 'bg-primary',           'btn' => 'primary'],
    'Lunas'               => ['badge' => 'bg-success',           'btn' => 'success'],
    'Ditolak'             => ['badge' => 'bg-danger',            'btn' => 'danger'],
];

// Ambil filter status dari URL (hanya diterima jika termasuk status yang valid)
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
if ($filter_status !== '' && !array_key_exists($filter_status, $daftar_status)) {
    $filter_status = '';
}
>>>>>>> 3033be54bbd224614777619b30ad2d9b7a67d1f9

$sql = "SELECT
<<<<<<< HEAD
            b.id_booking,
            b.tanggal_booking,
            b.tanggal_keberangkatan,
            b.jumlah_peserta,
            b.total_harga,
            b.status_booking,
            p.nama_paket,
            p.destinasi,
            p.gambar,
            pb.status_pembayaran
=======
        b.id_booking,
        b.tanggal_booking,
        b.tanggal_keberangkatan,
        b.jumlah_peserta,
        b.total_harga,
        b.status_booking,
        b.bukti_pembayaran,
        p.nama_paket,
        p.destinasi,
        p.gambar
     FROM booking b
     JOIN paket_wisata p
        ON b.id_paket = p.id_paket
     WHERE b.id_user = ?";
>>>>>>> 3033be54bbd224614777619b30ad2d9b7a67d1f9

        FROM booking b

        JOIN paket_wisata p
            ON b.id_paket = p.id_paket

        LEFT JOIN pembayaran pb
            ON pb.id_pembayaran = (
                SELECT MAX(pb2.id_pembayaran)
                FROM pembayaran pb2
                WHERE pb2.id_booking = b.id_booking
            )

        WHERE b.id_user = ?";

// Tambahkan filter status jika dipilih
if ($filter_status !== '') {
    $sql .= " AND b.status_booking = ?";
}

$sql .= " ORDER BY b.id_booking DESC";

// ==========================================
// PREPARED STATEMENT
// ==========================================

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query error: " . mysqli_error($conn));
}

if ($filter_status !== '') {
    mysqli_stmt_bind_param($stmt, "is", $id_user, $filter_status);
} else {
    mysqli_stmt_bind_param($stmt, "i", $id_user);
}

mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Booking - ADF Travel</title>

    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

<<<<<<< HEAD
=======
    <!-- CSS DataTables Bootstrap 5 -->
>>>>>>> 3033be54bbd224614777619b30ad2d9b7a67d1f9
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background-color: #e3f2fd;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #0d47a1;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .judul {
            color: #0d47a1;
        }

        .gambar-paket {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Tanda "Proses" berkedip pelan agar terlihat sedang berjalan */
        .badge-proses {
            animation: berkedip 1.6s ease-in-out infinite;
        }

        @keyframes berkedip {
            0%, 100% { opacity: 1; }
            50% { opacity: .55; }
        }

        .catatan-status {
            display: block;
            font-size: .75rem;
            color: #6c757d;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-custom">
        <div class="container">

            <a class="navbar-brand fw-bold" href="dashboard.php">
                ADF Travel
            </a>

            <div>
                <span class="text-white me-3">
                    Halo, <?= htmlspecialchars($_SESSION['nama'] ?? 'User'); ?>
                </span>

                <a href="dashboard.php" class="btn btn-light btn-sm">
                    Dashboard
                </a>
            </div>

        </div>
    </nav>

    <!-- ISI HALAMAN -->
    <div class="container py-5">

        <!-- JUDUL -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

            <div>
                <h2 class="fw-bold judul mb-1">
                    Riwayat Booking
                </h2>

                <p class="text-muted mb-0">
                    Daftar perjalanan yang pernah kamu pesan.
                </p>
            </div>

            <div class="d-flex gap-2">

                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali Dashboard
                </a>

                <a href="paket_wisata/index.php" class="btn btn-primary">
                    + Booking Paket
                </a>

            </div>
        </div>

        <!-- FILTER STATUS -->
        <div class="card shadow-sm mb-4">

            <div class="card-body py-3 d-flex align-items-center flex-wrap gap-2">
                                        </td>

                                        <!-- AKSI BOOKING -->
                                        <td>

                                            <?php if ($booking['status_booking'] === 'Dikonfirmasi'): ?>

                                                <?php if ($statusBayar === 'Terverifikasi'): ?>

                                                    <a
                                                        href="pembayaran.php?id=<?= (int) $booking['id_booking']; ?>"
                                                        class="btn btn-success btn-sm">
                                                        Lihat Pembayaran
                                                    </a>

                                                <?php elseif ($statusBayar === 'Menunggu'): ?>

                                                    <a
                                                        href="pembayaran.php?id=<?= (int) $booking['id_booking']; ?>"
                                                        class="btn btn-info btn-sm">
                                                        Lihat Pembayaran
                                                    </a>

                                                <?php elseif ($statusBayar === 'Ditolak'): ?>

                                                    <a
                                                        href="pembayaran.php?id=<?= (int) $booking['id_booking']; ?>"
                                                        class="btn btn-danger btn-sm">
                                                        Upload Ulang
                                                    </a>

                                                <?php else: ?>

                                                    <a
                                                        href="pembayaran.php?id=<?= (int) $booking['id_booking']; ?>"
                                                        class="btn btn-primary btn-sm">
                                                        Bayar Sekarang
                                                    </a>

                                                <?php endif; ?>

                                            <?php elseif ($booking['status_booking'] === 'Ditolak'): ?>

                                                <a
                                                    href="proses_hapus_booking.php?id=<?= (int) $booking['id_booking']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus riwayat pesanan yang ditolak ini?');">

                                                    Hapus

                                                </a>

                                            <?php else: ?>

                                                <span class="text-muted">-</span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        <?php else: ?>

            <!-- DATA KOSONG -->
            <div class="card shadow-sm">

                <div class="card-body text-center py-5">

                    <h4 class="fw-bold text-secondary">
                        Belum Ada Booking
                    </h4>

                    <p class="text-muted">
                        Kamu belum melakukan pemesanan paket wisata
                        atau tidak ada data pada filter ini.
                    </p>

                    <a href="paket_wisata/index.php" class="btn btn-primary">
                        Lihat Paket Wisata
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <!-- DATATABLES -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#tabelRiwayat').DataTable({
<<<<<<< HEAD

                pageLength: 6,

                lengthMenu: [
                    [6, 10, 25, 50, -1],
                    [6, 10, 25, 50, "Semua"]
                ],

                language: {

                    search: "Cari wisata:",

                    lengthMenu: "Tampilkan _MENU_ data per halaman",

                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

                    infoEmpty: "Tidak ada data yang tersedia",

                    zeroRecords: "Nama paket atau destinasi tidak ditemukan",

                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
=======
                "pageLength": 6,
                "lengthMenu": [ [6, 10, 25, 50, -1], [6, 10, 25, 50, "Semua"] ],
                "language": {
                    "search": "Cari wisata:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data yang tersedia",
                    "zeroRecords": "Nama paket atau destinasi tidak ditemukan",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
>>>>>>> 3033be54bbd224614777619b30ad2d9b7a67d1f9
                    }

                }

            });

        });
    </script>

</body>
</html>