
<?php
require_once __DIR__ . "/../../config/koneksi.php";

// Ambil data pengguna
$query = mysqli_query($conn, "SELECT * FROM `user` ORDER BY id_user DESC");

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

    <link rel="stylesheet"
        href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">

            <a class="navbar-brand fw-bold" href="../dashboard.php">
                RPBNusa
            </a>

            <div>
                <a href="../dashboard.php" class="btn btn-light btn-sm">
                    Dashboard
                </a>

                <a href="../../logout.php" class="btn btn-light btn-sm">
                    Logout
                </a>
            </div>

        </div>
    </nav>

    <!-- Konten -->
    <div class="container py-5">

        <div class="mb-4">
            <h2 class="fw-bold">Data Pengguna</h2>
            <p class="text-muted">
                Daftar pengguna yang terdaftar di RPBNusa.
            </p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

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

                            if (mysqli_num_rows($query) > 0):
                                while ($user = mysqli_fetch_assoc($query)):
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= htmlspecialchars($user['nama'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($user['email'] ?? '-'); ?>
                                        </td>

                                        <td>
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
                            <?php
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Belum ada data pengguna.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>

                <a href="../dashboard.php" class="btn btn-secondary">
                    Kembali
                </a>

            </div>
        </div>

    </div>

    <script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>