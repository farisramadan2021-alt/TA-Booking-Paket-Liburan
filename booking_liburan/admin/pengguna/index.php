<?php
require_once __DIR__ . "/../../config/koneksi.php";

$query = mysqli_query(
    $conn,
    "SELECT * FROM `user` ORDER BY id_user DESC"
);

if (!$query) {
    die("Gagal mengambil data pengguna: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Pengguna - RPBNusa</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet"
        href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet"
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background: #f4f7fb;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .table thead th {
            white-space: nowrap;
            vertical-align: middle;
        }

        .table tbody td {
            vertical-align: middle;
        }

        .alamat-pengguna {
            min-width: 180px;
        }

        .dataTables_wrapper {
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">

            <a class="navbar-brand fw-bold" href="../dashboard.php">
                RPBNusa
            </a>

            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-light btn-sm">
                    Dashboard
                </a>

                <a href="../../logout.php" class="btn btn-light btn-sm">
                    Logout
                </a>
            </div>

        </div>
    </nav>

    <!-- Isi -->
    <div class="container-fluid px-3 px-md-4 py-4">

        <div class="mb-4">
            <h2 class="fw-bold mb-1">Data Pengguna</h2>

            <p class="text-muted mb-0">
                Lihat dan cari data pengguna yang terdaftar di RPBNusa.
            </p>
        </div>

        <!-- Tabel pengguna -->
        <div class="card shadow-sm">
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

                                    <td class="alamat-pengguna">
                                        <?= htmlspecialchars($user['alamat'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php if (($user['role'] ?? '') === 'admin'): ?>
                                            <span class="badge bg-danger">
                                                Admin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">
                                                Pelanggan
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

        <div class="mt-3">
            <a href="../dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
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
                        first: "1",
                        last: "..",
                        next: ">>",
                        previous: "<<"
                    }
                }
            });
        });
    </script>

</body>
</html>