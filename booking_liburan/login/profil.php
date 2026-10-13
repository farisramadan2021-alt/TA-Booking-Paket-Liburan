<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "booking_liburan";

try {
    $koneksi = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$id_user = $_SESSION['id_user'];

// Ambil data user
$stmt = $koneksi->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Data pengguna tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm mx-auto rounded-3 border-0" style="max-width: 480px;">
            <div class="card-body text-center p-4">

                <!-- Alert Notifikasi Sesi (Sukses/Gagal) -->
                <?php if (isset($_SESSION['success'])) : ?>
                    <div class="alert alert-success alert-dismissible fade show text-start py-2 px-3 mb-3 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])) : ?>
                    <div class="alert alert-danger alert-dismissible fade show text-start py-2 px-3 mb-3 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Form Upload Foto Profil -->
                <form action="proses_edit_foto.php" method="POST" enctype="multipart/form-data">
                    <div class="position-relative d-inline-block mb-3">
                        <!-- Foto Profil Saat Ini -->
                        <img src="../assets/img/<?= !empty($user['foto_profil']) ? htmlspecialchars($user['foto_profil']) : 'default-avatar.png'; ?>"
                             alt="Foto Profil"
                             class="rounded-circle border border-3 border-white shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    </div>

                    <!-- Input File dari Galeri -->
                    <div class="mb-3 text-start">
                        <label for="foto_profil" class="form-label small text-muted fw-semibold mb-1">Ganti Foto Profil</label>
                        <input type="file" class="form-control form-control-sm" id="foto_profil" name="foto_profil" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-sm btn-success w-100 mb-4">
                        <i class="bi bi-upload me-1"></i> Unggah Foto Baru
                    </button>
                </form>

                <!-- Informasi Dasar Pengguna -->
                <h4 class="mb-1 fw-bold"><?= htmlspecialchars($user['nama'] ?? 'Pengguna') ?></h4>
                <p class="text-muted small mb-4"><?= htmlspecialchars($user['email'] ?? '-') ?></p>

                <!-- Data Detail Pengguna -->
                <ul class="list-group list-group-flush text-start mb-4 border-top border-bottom">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="fw-semibold text-secondary"><i class="bi bi-telephone me-2"></i>No. HP / WA</span>
                        <span class="fw-medium text-dark"><?= htmlspecialchars($user['no_hp'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="fw-semibold text-secondary"><i class="bi bi-person-badge me-2"></i>Role</span>
                        <span class="badge bg-primary text-capitalize px-3 py-2"><?= htmlspecialchars($user['role'] ?? '-') ?></span>
                    </li>
                    <li class="list-group-item py-3">
                        <span class="fw-semibold text-secondary d-block mb-1"><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                        <span class="text-dark d-block ps-4"><?= htmlspecialchars($user['alamat'] ?? '-') ?></span>
                    </li>
                </ul>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-2">
                    <a href="../index.php" class="btn btn-outline-secondary w-50">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <a href="edit_profil.php" class="btn btn-primary w-50">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profil
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Diperlukan untuk tombol close Alert) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>