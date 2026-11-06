<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

// Periksa akses admin
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../login/login.php");
    exit;
}

// Ambil data pembayaran
$query = mysqli_query($conn, "
    SELECT
        p.*,
        b.id_user,
        u.nama AS nama_pengguna,
        w.nama_paket
    FROM pembayaran p
    LEFT JOIN booking b ON p.id_booking = b.id_booking
    LEFT JOIN `user` u ON b.id_user = u.id_user
    LEFT JOIN paket_wisata w ON b.id_paket = w.id_paket
    ORDER BY p.id_pembayaran DESC
");

if (!$query) {
    die("Gagal mengambil data pembayaran: " . mysqli_error($conn));
}

// Pengaturan layout
$halamanAktif = 'pembayaran';
$judulHalaman = 'Data Pembayaran';
$namaAdmin = $_SESSION['nama'] ?? 'Admin';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-4">
    <h1 class="page-title">Data Pembayaran</h1>

    <p class="page-description">
        Periksa bukti pembayaran dan status transaksi pelanggan.
    </p>
</div>

<!-- TABEL PEMBAYARAN -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-md-4">

        <div class="table-responsive">
            <table id="tabelPembayaran"
                   class="table table-bordered table-hover table-striped align-middle w-100">

                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Pengguna</th>
                        <th>Paket Wisata</th>
                        <th>Tanggal Bayar</th>
                        <th>Metode</th>
                        <th>Jumlah Bayar</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $no = 1;

                if (mysqli_num_rows($query) > 0):
                    while ($p = mysqli_fetch_assoc($query)):

                        $status = $p['status_pembayaran'] ?? 'Menunggu';

                        if ($status === 'Terverifikasi') {
                            $badge = 'bg-success';
                        } elseif ($status === 'Ditolak') {
                            $badge = 'bg-danger';
                        } else {
                            $badge = 'bg-warning text-dark';
                        }
                ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td>
                            <?= htmlspecialchars($p['nama_pengguna'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($p['nama_paket'] ?? '-'); ?>
                        </td>

                        <td class="text-nowrap">
                            <?= htmlspecialchars($p['tanggal_bayar'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($p['metode_pembayaran'] ?? '-'); ?>
                        </td>

                        <td class="text-nowrap"
                            data-order="<?= (float) $p['jumlah_bayar']; ?>">
                            Rp <?= number_format(
                                (float) $p['jumlah_bayar'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td class="text-nowrap">
                            <?php if (!empty($p['bukti_pembayaran'])): ?>

                                <a href="../../uploads/<?= rawurlencode(basename($p['bukti_pembayaran'])); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-info btn-sm">
                                    Lihat Bukti
                                </a>

                            <?php else: ?>

                                <span class="text-muted">
                                    Belum ada bukti
                                </span>

                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="badge <?= $badge; ?>">
                                <?= htmlspecialchars($status); ?>
                            </span>
                        </td>

                        <td class="text-nowrap">
                            <?php if ($status === 'Menunggu'): ?>

                                <a href="verifikasi.php?id=<?= (int) $p['id_pembayaran']; ?>"
                                   class="btn btn-success btn-sm mb-1"
                                   onclick="return confirm('Verifikasi pembayaran ini?')">
                                    Verifikasi
                                </a>

                                <a href="tolak.php?id=<?= (int) $p['id_pembayaran']; ?>"
                                   class="btn btn-danger btn-sm mb-1"
                                   onclick="return confirm('Tolak pembayaran ini?')">
                                    Tolak
                                </a>

                            <?php else: ?>

                                <span class="text-muted">Selesai</span>

                            <?php endif; ?>
                        </td>
                    </tr>

                <?php
                    endwhile;
                endif;
                ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<div class="mt-3">
    <a href="../dashboard.php" class="btn btn-secondary">
        Kembali ke Dashboard
    </a>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPembayaran').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        order: [],
        columnDefs: [
            {
                targets: [0, 6, 8],
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data pembayaran tidak ditemukan",
            emptyTable: "Belum ada data pembayaran",
            paginate: {
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
