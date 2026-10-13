<?php
session_start();

// Cek apakah user sudah login
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
    $_SESSION['error'] = "Koneksi database gagal: " . $e->getMessage();
    header("Location: profil.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto_profil']['tmp_name'];
        $fileName = $_FILES['foto_profil']['name'];
        $fileSize = $_FILES['foto_profil']['size'];
        $fileType = $_FILES['foto_profil']['type'];
        
        // Batasan ukuran file (Maksimal 2 MB)
        $maxSize = 2 * 1024 * 1024; 
        if ($fileSize > $maxSize) {
            $_SESSION['error'] = "Ukuran file terlalu besar! Maksimal ukuran file adalah 2 MB.";
            header("Location: profil.php");
            exit();
        }

        // Ambil ekstensi file
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Ekstensi yang diizinkan
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Buat nama file baru yang unik untuk menghindari bentrok nama
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            
            // Tentukan direktori penyimpanan menggunakan path absolut (__DIR__) agar aman dari error folder tidak ditemukan
            // Sesuaikan jumlah '../' jika folder assets berada di luar folder booking_liburan
            $uploadFileDir = __DIR__ . '/../assets/img/'; 

            // Cek dan buat folder secara otomatis jika belum ada
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Ambil foto lama user untuk dihapus (agar penyimpanan server tidak menumpuk)
                $stmt = $koneksi->prepare("SELECT foto_profil FROM user WHERE id_user = ?");
                $stmt->execute([$id_user]);
                $oldUser = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($oldUser && !empty($oldUser['foto_profil'])) {
                    $oldFile = $uploadFileDir . $oldUser['foto_profil'];
                    if (file_exists($oldFile) && $oldUser['foto_profil'] != 'default-avatar.png') {
                        unlink($oldFile); // Hapus foto lama
                    }
                }

                // Update nama file foto di database
                $update = $koneksi->prepare("UPDATE user SET foto_profil = ? WHERE id_user = ?");
                $update->execute([$newFileName, $id_user]);

                $_SESSION['success'] = "Foto profil berhasil diubah!";
                header("Location: profil.php");
                exit();
            } else {
                $_SESSION['error'] = "Terjadi kesalahan saat mengunggah file ke direktori server.";
                header("Location: profil.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Ekstensi file tidak diizinkan. Harap unggah file berformat JPG, JPEG, PNG, atau WEBP.";
            header("Location: profil.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Tidak ada file yang dipilih atau terjadi error saat upload.";
        header("Location: profil.php");
        exit();
    }
} else {
    header("Location: profil.php");
    exit();
}
?>