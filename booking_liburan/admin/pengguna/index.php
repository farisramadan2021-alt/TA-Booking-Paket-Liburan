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

// Ambil data pengguna
$query = mysqli_query(
    $conn,
    "SELECT * FROM `user` ORDER BY id_user DESC"
);

if (!$query) {
    die("Gagal mengambil data pengguna: " . mysqli_error($conn));
}

// Pengaturan layout bersama
$halamanAktif = 'pengguna';
$judulHalaman = 'Data Pengguna';
$namaAdmin = $_SESSION['nama'] ?? 'Admin';

include __DIR__ . '/../partials/header.php';
?>

<div class="mb-4">
    <h1 class="page-title">Data Pengguna</h1>

    <p class="page-description">
        Lihat dan cari data pengguna yang terdaftar di RPBNusa.
    </p>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-md-4">

        <div class="table-responsive">
            <table id="tabelPengguna"
                   class="table table-bordered table-hover table-striped
                          align-middle w-100">

                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Role</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $no = 1;

                while ($user = mysqli_fetch_assoc($query)):
                ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td class="fw-semibold">
                            <?= htmlspecialchars($user['nama'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user['email'] ?? '-'); ?>
                        </td>

                        <td class="text-nowrap">
                            <?= htmlspecialchars($user['no_hp'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user['alamat'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Pelanggan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
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

<!-- CSS DataTables -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">

<!-- jQuery dan DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPengguna').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        order: [],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data pengguna tidak ditemukan",
            emptyTable: "Belum ada data pengguna",
            paginate: {
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
