
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

// Ambil data booking
$query = mysqli_query($conn, "
    SELECT
        b.*,
        u.nama AS nama_pengguna,
        p.nama_paket
    FROM booking b
    LEFT JOIN `user` u ON b.id_user = u.id_user
    LEFT JOIN paket_wisata p ON b.id_paket = p.id_paket
    ORDER BY b.id_booking DESC
");

if (!$query) {
    die("Gagal mengambil data booking: " . mysqli_error($conn));
}

// Pengaturan layout
$halamanAktif = 'booking';
$judulHalaman = 'Data Booking';
$namaAdmin = $_SESSION['nama'] ?? 'Admin';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-4">
    <h1 class="page-title">Data Booking</h1>

    <p class="page-description">
        Kelola pemesanan paket liburan pelanggan.
    </p>
</div>

<!-- TABEL BOOKING -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-md-4">

        <div class="table-responsive">
            <table id="tabelBooking"
                   class="table table-bordered table-hover align-middle w-100">

                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Pengguna</th>
                        <th>Paket Wisata</th>
                        <th>Tanggal Booking</th>
                        <th>Tanggal Keberangkatan</th>
                        <th>Jumlah Peserta</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $no = 1;

                if (mysqli_num_rows($query) > 0):
                    while ($b = mysqli_fetch_assoc($query)):

                        $status = $b['status_booking'] ?? 'Menunggu';

                        $badge = 'bg-warning text-dark';

                        if ($status === 'Dikonfirmasi') {
                            $badge = 'bg-success';
                        } elseif (
                            $status === 'Ditolak' ||
                            $status === 'Dibatalkan'
                        ) {
                            $badge = 'bg-danger';
                        }
                ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td>
                            <?= htmlspecialchars($b['nama_pengguna'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($b['nama_paket'] ?? '-'); ?>
                        </td>

                        <td class="text-nowrap">
                            <?= htmlspecialchars($b['tanggal_booking'] ?? '-'); ?>
                        </td>

                        <td class="text-nowrap">
                            <?= htmlspecialchars($b['tanggal_keberangkatan'] ?? '-'); ?>
                        </td>

                        <td class="text-nowrap">
                            <?= (int) ($b['jumlah_peserta'] ?? 0); ?> orang
                        </td>

                        <td class="text-nowrap"
                            data-order="<?= (float) $b['total_harga']; ?>">
                            Rp <?= number_format(
                                (float) $b['total_harga'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            <span class="badge <?= $badge; ?>">
                                <?= htmlspecialchars($status); ?>
                            </span>
                        </td>

                        <td class="text-nowrap">
                            <?php if ($status === 'Menunggu'): ?>

                                <a href="konfirmasi.php?id=<?= (int) $b['id_booking']; ?>"
                                   class="btn btn-success btn-sm mb-1"
                                   onclick="return confirm('Konfirmasi booking ini?')">
                                    Konfirmasi
                                </a>

                                <a href="tolak.php?id=<?= (int) $b['id_booking']; ?>"
                                   class="btn btn-danger btn-sm mb-1"
                                   onclick="return confirm('Tolak booking ini?')">
                                    Tolak
                                </a>

                            <?php else: ?>

                                <span class="text-muted">Tidak ada aksi</span>

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
    $('#tabelBooking').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        order: [],
        columnDefs: [
            {
                targets: [0, 8],
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
            zeroRecords: "Data booking tidak ditemukan",
            emptyTable: "Belum ada data booking",
            paginate: {
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
