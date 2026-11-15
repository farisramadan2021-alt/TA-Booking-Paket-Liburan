
<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

// ===============================
// CEK HAK AKSES ADMIN
// ===============================
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../login/login.php");
    exit;
}

// ===============================
// AMBIL ID PAKET
// ===============================
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// ===============================
// AMBIL DETAIL PAKET WISATA
// ===============================
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM paket_wisata WHERE id_paket = ?"
);

if (!$stmt) {
    die("Gagal menyiapkan query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Jika paket tidak ditemukan
if (!$data) {
    header("Location: index.php");
    exit;
}

// ===============================
// PENGATURAN LAYOUT ADMIN
// ===============================
$halamanAktif = 'paket';
$judulHalaman = 'Detail Paket Wisata';
$namaAdmin = $_SESSION['nama'] ?? 'Admin';

include __DIR__ . '/../partials/header.php';
?>

<style>
    .detail-gambar {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        border-radius: 12px;
    }

    .detail-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .detail-value {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .detail-card {
        border: none;
        border-radius: 12px;
    }
</style>

<div class="container-fluid">

    <!-- JUDUL HALAMAN -->
    <div class="d-flex justify-content-between
                align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Detail Paket Wisata
            </h2>

            <p class="text-muted mb-0">
                Informasi lengkap paket wisata.
            </p>
        </div>

    </div>

    <!-- DETAIL PAKET -->
    <div class="card shadow-sm detail-card">

        <div class="card-body p-4">

            <div class="row g-4">

                <!-- GAMBAR PAKET -->
                <div class="col-md-5">

                    <?php if (!empty($data['gambar'])): ?>

                        <img
                            src="../../uploads/paket_wisata/<?= htmlspecialchars($data['gambar'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?= htmlspecialchars($data['nama_paket'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="detail-gambar"
                        >

                    <?php else: ?>

                        <div class="bg-light rounded p-5 text-center">
                            <i class="bi bi-image fs-1 text-muted"></i>
                            <p class="text-muted mb-0 mt-2">
                                Tidak ada gambar
                            </p>
                        </div>

                    <?php endif; ?>

                </div>

                <!-- INFORMASI PAKET -->
                <div class="col-md-7">

                    <h3 class="fw-bold mb-4">
                        <?= htmlspecialchars($data['nama_paket']); ?>
                    </h3>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="detail-label">
                                Destinasi
                            </div>

                            <div class="detail-value">
                                <i class="bi bi-geo-alt text-primary me-1"></i>
                                <?= htmlspecialchars($data['destinasi']); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-label">
                                Durasi Wisata
                            </div>

                            <div class="detail-value">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                <?= (int) $data['durasi']; ?> Hari
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-label">
                                Harga Paket
                            </div>

                            <div class="detail-value text-primary">
                                Rp <?= number_format(
                                    (float) $data['harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-label">
                                Status Paket
                            </div>

                            <div class="detail-value">

                                <?php if ($data['status'] === 'Aktif'): ?>

                                    <span class="badge text-bg-success">
                                        Aktif
                                    </span>

                                <?php else: ?>

                                    <span class="badge text-bg-secondary">
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>

                                <?php endif; ?>

                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="detail-label">
                        Deskripsi Paket Wisata
                    </div>

                    <div class="detail-value fw-normal"
                         style="line-height: 1.8;">

                        <?= nl2br(htmlspecialchars($data['deskripsi'])); ?>

                    </div>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="edit.php?id=<?= (int) $data['id_paket']; ?>"
                            class="btn btn-warning"
                        >
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Paket
                        </a>

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Kembali
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
