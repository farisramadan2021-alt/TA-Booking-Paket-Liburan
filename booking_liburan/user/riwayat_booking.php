<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

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

// Susun Query dengan Prepared Statement menyesuaikan filter status
$sql = "SELECT
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

if ($filter_status !== '') {
    $sql .= " AND b.status_booking = ?";
}

$sql .= " ORDER BY b.id_booking DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query error: " . mysqli_error($conn));
}

// Bind parameter dinamis tergantung ada tidaknya filter
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

    <title>Riwayat Booking</title>

    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- CSS DataTables Bootstrap 5 -->
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

    <!-- Navbar -->
    <nav class="navbar navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                RPBNusa
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

    <!-- Isi Halaman -->
    <div class="container py-5">

        <!-- Judul dan Tombol -->
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

        <!-- TOMBOL FILTER STATUS -->
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3 d-flex align-items-center flex-wrap gap-2">
                <span class="fw-semibold text-secondary me-2">Filter Status:</span>
                <div class="btn-group flex-wrap" role="group">
                    <a href="riwayat_booking.php"
                       class="btn btn-sm <?= ($filter_status === '') ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
                    <?php foreach ($daftar_status as $nama_status => $gaya): ?>
                        <a href="riwayat_booking.php?status=<?= urlencode($nama_status); ?>"
                           class="btn btn-sm <?= ($filter_status === $nama_status) ? 'btn-' . $gaya['btn'] : 'btn-outline-' . $gaya['btn']; ?>">
                            <?= htmlspecialchars($nama_status); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Booking -->
        <?php if (mysqli_num_rows($query) > 0): ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">

                        <!-- Tabel dengan ID DataTable -->
                        <table id="tabelRiwayat" class="table table-hover align-middle w-100">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pemesanan</th>
                                    <th>Paket Wisata</th>
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
                                while ($booking = mysqli_fetch_assoc($query)):
                                    $status = $booking['status_booking'];
                                    $id_booking = (int) $booking['id_booking'];
                                ?>
                                    <tr>
                                        <td>
                                            <?= $no++; ?>
                                        </td>
                                        <td>
                                            <?= date('d-m-Y', strtotime($booking['tanggal_booking'])); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($booking['gambar'])): ?>
                                                    <img src="../uploads/paket_wisata/<?= htmlspecialchars($booking['gambar']); ?>"
                                                        class="gambar-paket me-3"
                                                        alt="<?= htmlspecialchars($booking['nama_paket']); ?>">
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?= htmlspecialchars($booking['nama_paket']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($booking['destinasi']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= date('d-m-Y', strtotime($booking['tanggal_keberangkatan'])); ?>
                                        </td>
                                        <td>
                                            <?= (int) $booking['jumlah_peserta']; ?> orang
                                        </td>
                                        <td class="fw-bold text-primary">
                                            Rp <?= number_format($booking['total_harga'], 0, ',', '.'); ?>
                                        </td>

                                        <!-- KOLOM STATUS -->
                                        <td>
                                            <?php if ($status === 'Menunggu'): ?>
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                                <span class="catatan-status">Menunggu konfirmasi admin</span>

                                            <?php elseif ($status === 'Dikonfirmasi'): ?>
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                                <span class="catatan-status">Silakan lakukan pembayaran</span>

                                            <?php elseif ($status === 'Proses'): ?>
                                                <span class="badge bg-info text-dark badge-proses">Proses</span>
                                                <span class="catatan-status">Lengkapi validasi data &amp; kirim bukti bayar</span>

                                            <?php elseif ($status === 'Menunggu Verifikasi'): ?>
                                                <span class="badge bg-primary">Menunggu Verifikasi</span>
                                                <span class="catatan-status">Bukti bayar sedang dicek admin</span>

                                            <?php elseif ($status === 'Lunas'): ?>
                                                <span class="badge bg-success">Lunas</span>
                                                <span class="catatan-status">Pembayaran sudah diverifikasi</span>

                                            <?php elseif ($status === 'Ditolak'): ?>
                                                <span class="badge bg-danger">Ditolak</span>

                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($status); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- KOLOM AKSI -->
                                        <td>
                                            <?php if ($status === 'Dikonfirmasi'): ?>
                                                <a href="pembayaran.php?id=<?= $id_booking; ?>" class="btn btn-primary btn-sm">
                                                    Bayar Sekarang
                                                </a>

                                            <?php elseif ($status === 'Proses'): ?>
                                                <a href="validasi_booking.php?id=<?= $id_booking; ?>" class="btn btn-info btn-sm text-dark">
                                                    Validasi &amp; Kirim Bukti
                                                </a>

                                            <?php elseif ($status === 'Menunggu Verifikasi'): ?>
                                                <?php if (!empty($booking['bukti_pembayaran'])): ?>
                                                    <a href="../uploads/bukti_pembayaran/<?= htmlspecialchars($booking['bukti_pembayaran']); ?>"
                                                       target="_blank" class="btn btn-outline-primary btn-sm">
                                                        Lihat Bukti Saya
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>

                                            <?php elseif ($status === 'Lunas'): ?>
                                                <a href="struk_booking.php?id=<?= $id_booking; ?>" target="_blank" class="btn btn-success btn-sm">
                                                    Lihat Bon / Struk
                                                </a>

                                            <?php elseif ($status === 'Ditolak'): ?>
                                                <a href="proses_hapus_booking.php?id=<?= $id_booking; ?>"
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

            <!-- Jika Data Kosong Sama Sekali -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h4 class="fw-bold text-secondary">
                        Belum Ada Booking
                    </h4>
                    <p class="text-muted">
                        Kamu belum melakukan pemesanan paket wisata atau tidak ada data pada filter ini.
                    </p>
                    <a href="paket_wisata/index.php" class="btn btn-primary">
                        Lihat Paket Wisata
                    </a>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Script Inisialisasi DataTable dengan Batas 6 Baris -->
    <script>
        $(document).ready(function() {
            $('#tabelRiwayat').DataTable({
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
                    }
                }
            });
        });
    </script>

</body>
</html>