<?php
session_start();

// 1. Cek Login
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

// 2. Pemrosesan Form Edit Profil (Method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = htmlspecialchars($_POST['nama'] ?? '');
    $no_hp  = htmlspecialchars($_POST['no_hp'] ?? '');
    $alamat = htmlspecialchars($_POST['alamat'] ?? '');

    // UPDATE pada tabel 'user' (bukan 'users') dan primary key 'id_user'
    $sql = "UPDATE user SET nama = ?, no_hp = ?, alamat = ? WHERE id_user = ?";
    $stmt = $koneksi->prepare($sql);
    
    if ($stmt->execute([$nama, $no_hp, $alamat, $id_user])) {
        $_SESSION['nama'] = $nama; // Update session nama
        header("Location: profil.php?status=updated");
        exit();
    } else {
        $error = "Gagal memperbarui profil.";
    }
}

// 3. Ambil data user untuk ditampilkan di form
$stmt = $koneksi->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$id_user]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die("Data pengguna tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4 fw-bold">Edit Profil</h4>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger p-2 mb-3"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($userData['nama'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email (Tidak dapat diubah)</label>
                    <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" readonly disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor HP / WhatsApp</label>
                    <input type="tel" name="no_hp" class="form-control" value="<?= htmlspecialchars($userData['no_hp'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($userData['alamat'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between pt-2">
                    <a href="profil.php" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>