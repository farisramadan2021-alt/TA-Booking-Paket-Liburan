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

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id_user = $_SESSION['id_user'];

// 2. LOGIKA PEMROSES (Jalan hanya saat form di-submit / method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap  = htmlspecialchars($_POST['nama_lengkap'] ?? '');
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin'] ?? '');
    $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir'] ?? '');
    $no_hp         = htmlspecialchars($_POST['no_hp'] ?? '');
    $alamat        = htmlspecialchars($_POST['alamat'] ?? '');

    $sql = "UPDATE users SET nama_lengkap = ?, jenis_kelamin = ?, tanggal_lahir = ?, no_hp = ?, alamat = ? WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    
    if ($stmt->execute([$nama_lengkap, $jenis_kelamin, $tanggal_lahir, $no_hp, $alamat, $user_id])) {
        // Redirect ke index atau refresh halaman agar tidak double-submit saat di-refresh
        header("Location: index.php?status=profil_updated");
        exit();
    } else {
        $error = "Gagal memperbarui profil.";
    }
}

// 3. AMBIL DATA USER (Untuk menampilkan data di input)
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil Pengguna</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .container { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-submit { background-color: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 3px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Profil</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <!-- action dikosongkan ("") agar mengirim data ke file ini sendiri -->
    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="L" <?= ($user['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= ($user['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Nomor HP / WhatsApp</label>
            <input type="tel" name="no_hp" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" rows="4" required><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-submit">Simpan Profil</button>
        <a href="index.php" style="margin-left: 10px;">Batal</a>
    </form>
</div>

</body>
</html>