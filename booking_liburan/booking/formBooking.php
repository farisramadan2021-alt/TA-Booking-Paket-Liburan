<?php
session_start();

require_once '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil ID paket dari URL
if (!isset($_GET['id'])) {
    header("Location: ../user/paket_wisata/index.php");
    exit;
}

$id_paket = intval($_GET['id']);

// Ambil data paket dari database
$query = mysqli_query(
    $conn,
    "SELECT * FROM paket_wisata WHERE id_paket = $id_paket"
);

$paket = mysqli_fetch_assoc($query);

$query_jadwal = mysqli_query(
    $conn,
    "SELECT * FROM jadwal_keberangkatan
     WHERE id_paket = $id_paket
     AND status = 'Tersedia'
     AND sisa_kuota > 0
     ORDER BY tanggal_keberangkatan ASC"
);

if (!$paket) {
    echo "<script>
            alert('Paket wisata tidak ditemukan.');
            window.location.href = '../user/paket_wisata/index.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('../uploads/paket_wisata/fotoBackground.jpeg') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            background-size: cover;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }

        .card-header-custom {
            background-color: #0d47a1 !important;
            color: white;
        }

        .btn-custom {
            background-color: #1976d2;
            border: none;
            transition: background-color 0.2s ease;
        }

        .btn-custom:hover {
            background-color: #0d47a1;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #64b5f6;
            box-shadow: 0 0 0 0.25rem rgba(100, 181, 246, 0.25);
        }

        .gambar-paket {
            width: 100%;
            height: auto;
            max-height: 280px;
            object-fit: cover;
            border-radius: 12px;
            display: block;
            margin: 0 auto;
        }
    </style>
    <title>Booking Paket Wisata</title>
</head>

<body class="bg-light">
    <div class="container mt-5 pb-5">

        <!-- Menggunakan col-lg-8 dan justify-content-center agar posisinya di tengah dan tersusun ke bawah -->
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- 1. BAGIAN ATAS: DETAIL PAKET WISATA -->
                <div class="card shadow-lg mb-4">
                    <div class="card-header card-header-custom text-center py-3">
                        <h3 class="mb-0">Detail Paket Wisata</h3>
                    </div>

                    <div class="card-body p-4">
                        <h4 class="text-center fw-bold text-primary mb-2"><?= htmlspecialchars($paket['nama_paket']); ?></h4>

                        <p class="text-muted text-center mb-4">
                            <?= nl2br(htmlspecialchars($paket['deskripsi'])); ?>
                        </p>

                        <div class="text-center mb-4">
                            <img src="../uploads/paket_wisata/<?= htmlspecialchars($paket['gambar']); ?>"
                                class="gambar-paket shadow-sm"
                                alt="<?= htmlspecialchars($paket['nama_paket']); ?>">
                        </div>
                        <!-- Fasilitas Include / Exclude -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <h6 class="fw-bold text-success mb-2">✔ Fasilitas Termasuk:</h6>
                                    <ul class="small text-secondary mb-0 ps-3">
                                        <li>Armada Bus / Elf AC (Penjemputan Malang)</li>
                                        <li>Driver, BBM, Tol & Parkir</li>
                                        <li>Tiket Masuk Objek Wisata</li>
                                        <li>Pemandu Wisata (Tour Guide)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <h6 class="fw-bold text-danger mb-2">✖ Tidak Termasuk:</h6>
                                    <ul class="small text-secondary mb-0 ps-3">
                                        <li>Pengeluaran Pribadi</li>
                                        <li>Tip Driver & Guide (Sukarela)</li>
                                        <li>Obat-obatan / Kebutuhan Pribadi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                       <!-- Itinerary Otomatis Khusus Jarak Jauh (Luar Pulau / Perjalanan Panjang) -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark mb-2">📅 Rencana Perjalanan (Itinerary):</h6>
                            <div class="p-3 bg-light rounded-3 border small text-secondary">
                                <?php 
                                $jumlah_hari = intval($paket['durasi']); 
                                $nama_paket = strtolower($paket['nama_paket']);

                                // Cek apakah ini paket destinasi jauh / luar pulau
                                $is_luar_pulau = (strpos($nama_paket, 'raja ampat') !== false || strpos($nama_paket, 'papua') !== false || strpos($nama_paket, 'bali') !== false || strpos($nama_paket, 'lombok') !== false);

                                if ($is_luar_pulau && $jumlah_hari > 2) {
                                    // Itinerary khusus perjalanan jauh (Luar Pulau / Butuh Transportasi Udara-Darat)
                                    for ($i = 1; $i <= $jumlah_hari; $i++) {
                                        if ($i == 1) {
                                            $kegiatan = "Perjalanan awal dari Malang menuju bandara penberangkatan, proses check-in, penerbangan menuju kota tujuan, disambut Tour Guide lokal, dan menuju penginapan.";
                                        } elseif ($i == 2 && $jumlah_hari > 4) {
                                            $kegiatan = "Adaptasi cuaca/lokasi, orientasi singkat, dan perjalanan darat/laut menuju titik transit utama destinasi wisata.";
                                        } elseif ($i == $jumlah_hari) {
                                            $kegiatan = "Persiapan checkout penginapan, belanja oleh-oleh khas daerah tujuan, perjalanan menuju bandara untuk penerbangan kembali, dan penjemputan pulang ke Malang. Tour selesai.";
                                        } else {
                                            $array_jauh = [
                                                "Full day tour mengeksplorasi spot-spot destinasi wisata utama dan keindahan alam populer.",
                                                "Aktivitas island hopping / eksplorasi budaya lokal didampingi pemandu wisata profesional.",
                                                "Kunjungan ke pusat kerajinan, wisata kuliner khas, dan menikmati pemandangan sunset.",
                                                "Aktivitas bebas (free program) atau menikmati fasilitas eksklusif di sekitar akomodasi."
                                            ];
                                            $kegiatan = $array_jauh[($i - 2) % count($array_jauh)];
                                        }
                                        echo '<p class="mb-2"><strong>Hari ' . $i . ':</strong> ' . $kegiatan . '</p>';
                                    }
                                } else {
                                    // Itinerary standar perjalanan dekat / darat (Contoh: Bromo, Malang sekitarnya)
                                    if ($jumlah_hari == 1) {
                                        echo '<p class="mb-0"><strong>Hari 1 (One Day Tour):</strong> Penjemputan di Malang, perjalanan darat menuju objek wisata, eksplorasi, dan kembali ke Malang.</p>';
                                    } else {
                                        for ($i = 1; $i <= $jumlah_hari; $i++) {
                                            if ($i == 1) {
                                                $kegiatan = "Penjemputan rombongan di Malang dengan armada Bus/Elf, perjalanan darat menuju lokasi wisata, dan check-in.";
                                            } elseif ($i == $jumlah_hari) {
                                                $kegiatan = "Checkout, belanja oleh-oleh khas, perjalanan darat kembali ke Malang. Tour selesai.";
                                            } else {
                                                $kegiatan = "Full day tour menggunakan armada darat mengeksplorasi rangkaian destinasi wisata utama.";
                                            }
                                            echo '<p class="mb-2"><strong>Hari ' . $i . ':</strong> ' . $kegiatan . '</p>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary fw-semibold">Harga Per Orang</span>
                            <strong class="fs-5 text-dark" id="harga-satuan" data-harga="<?= $paket['harga']; ?>">
                                Rp <?= number_format($paket['harga'], 0, ',', '.'); ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- 2. BAGIAN BAWAH: FORMULIR BOOKING -->
                <div class="card shadow-lg">
                    <div class="card-header text-center card-header-custom py-3">
                        <h3 class="mb-0">Formulir Booking</h3>
                        <p class="mb-0 text-light small">Isi data perjalanan Anda dan pesan paket wisata pilihan Anda</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="proses_booking.php" method="POST">

                            <input type="hidden" name="id_paket" value="<?= $id_paket; ?>">

                            <div class="mb-3">
                                <label for="nama" class="form-label fw-semibold text-secondary">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-secondary">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="tlp" class="form-label fw-semibold text-secondary">Nomor Telepon</label>
                                <input type="tel" id="tlp" name="tlp" class="form-control" pattern="[0-9]{10,13}" title="Isi nomor telephone dengan benar (10-13 digit angka)" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label fw-semibold text-secondary">
                                    Tanggal Keberangkatan
                                </label>
                                <input type="date"
                                    id="tanggal"
                                    name="tanggal"
                                    class="form-control"
                                    min="<?= date('Y-m-d'); ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_peserta" class="form-label fw-semibold text-secondary">Jumlah Peserta</label>
                                <input type="number" id="jumlah_peserta" name="jumlah_peserta" min="1" value="1" class="form-control" required>
                            </div>

                            <div class="bg-light p-3 rounded-3 border mb-3">
                                <p class="mb-1 text-muted small">Total Biaya</p>
                                <h3 id="total" class="text-primary fw-bold mb-0">Rp 0</h3>
                                
                            </div>

                            <button type="submit" class="btn btn-custom text-white py-2 fw-bold w-100 mb-2">
                                Booking Sekarang
                            </button>
                            <a href="../user/dashboard.php" class="btn btn-secondary w-100">
                                Kembali ke Paket Wisata
                            </a>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputJumlahPeserta = document.getElementById('jumlah_peserta');
            const elementHargaSatuan = document.getElementById('harga-satuan');
            const elementTotal = document.getElementById('total');

            const hargaPerOrang = parseInt(elementHargaSatuan.getAttribute('data-harga')) || 0;

            function hitungTotal() {
                const jumlahPeserta = parseInt(inputJumlahPeserta.value) || 0;
                const totalBiaya = hargaPerOrang * jumlahPeserta;

                elementTotal.textContent = 'Rp ' + totalBiaya.toLocaleString('id-ID');
            }

            hitungTotal();

            inputJumlahPeserta.addEventListener('input', hitungTotal);
        });
    </script>
</body>

</html>