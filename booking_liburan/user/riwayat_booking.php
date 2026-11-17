<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Ambil filter status dari URL jika ada
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Susun Query dengan Prepared Statement menyesuaikan filter status
$sql = "SELECT
        b.id_booking,
        b.tanggal_booking,
        b.tanggal_keberangkatan,
        b.jumlah_peserta,
        b.total_harga,
        b.status_booking,
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
                <div class="btn-group" role="group">
                    <a href="riwayat_booking.php" class="btn btn-sm <?= ($filter_status == '') ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
                    <a href="riwayat_booking.php?status=Menunggu" class="btn btn-sm <?= ($filter_status == 'Menunggu') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?>">Menunggu</a>
                    <a href="riwayat_booking.php?status=Dikonfirmasi" class="btn btn-sm <?= ($filter_status == 'Dikonfirmasi') ? 'btn-success' : 'btn-outline-success'; ?>">Dikonfirmasi</a>
                    <a href="riwayat_booking.php?status=Ditolak" class="btn btn-sm <?= ($filter_status == 'Ditolak') ? 'btn-danger' : 'btn-outline-danger'; ?>">Ditolak</a>
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
                                        <td>
                                            <?php if ($booking['status_booking'] === 'Menunggu'): ?>
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                            <?php elseif ($booking['status_booking'] === 'Dikonfirmasi'): ?>
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            <?php elseif ($booking['status_booking'] === 'Ditolak'): ?>
                                                <span class="badge bg-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($booking['status_booking']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($booking['status_booking'] === 'Dikonfirmasi'): ?>
                                                <a href="pembayaran.php?id=<?= (int) $booking['id_booking']; ?>" class="btn btn-primary btn-sm">
                                                    Bayar Sekarang
                                                </a>
                                            <?php elseif ($booking['status_booking'] === 'Ditolak'): ?>
                                                <a href="proses_hapus_booking.php?id=<?= (int) $booking['id_booking']; ?>" 
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