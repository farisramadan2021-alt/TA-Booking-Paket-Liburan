
<?php
session_start();

require_once __DIR__ . "/../../config/koneksi.php";

// Memuat Dompdf
$autoloadPath = __DIR__ . "/../../../vendor/autoload.php";

if (!file_exists($autoloadPath)) {
    die("File Dompdf tidak ditemukan. Periksa lokasi folder vendor.");
}

require_once $autoloadPath;

use Dompdf\Dompdf;
use Dompdf\Options;

// ==========================================
// 1. CEK LOGIN ADMIN
// ==========================================
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../../login/login.php");
    exit;
}

// ==========================================
// 2. VALIDASI FILTER TANGGAL
// ==========================================
function validasiTanggal($tanggal)
{
    if ($tanggal === '') {
        return '';
    }

    $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

    if (!$date || $date->format('Y-m-d') !== $tanggal) {
        return '';
    }

    return $tanggal;
}

$tanggalMulai = validasiTanggal(
    $_GET['tanggal_mulai'] ?? ''
);

$tanggalSelesai = validasiTanggal(
    $_GET['tanggal_selesai'] ?? ''
);

// Periksa rentang tanggal
if (
    $tanggalMulai !== '' &&
    $tanggalSelesai !== '' &&
    $tanggalMulai > $tanggalSelesai
) {
    die("Tanggal mulai tidak boleh melebihi tanggal selesai.");
}

// ==========================================
// 3. KONDISI FILTER SQL
// ==========================================
$where = [];
$params = [];
$types = '';

if ($tanggalMulai !== '') {
    $where[] = "DATE(b.tanggal_booking) >= ?";
    $params[] = $tanggalMulai;
    $types .= 's';
}

if ($tanggalSelesai !== '') {
    $where[] = "DATE(b.tanggal_booking) <= ?";
    $params[] = $tanggalSelesai;
    $types .= 's';
}

$whereSQL = '';

if (!empty($where)) {
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

// ==========================================
// 4. FUNGSI EKSEKUSI QUERY FILTER
// ==========================================
function jalankanQuery($conn, $sql, $types, $params)
{
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Gagal menyiapkan query: " . mysqli_error($conn));
    }

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        die("Gagal menjalankan query: " . mysqli_stmt_error($stmt));
    }

    return $stmt;
}

// ==========================================
// 5. AMBIL DATA DETAIL BOOKING
// ==========================================
$sql = "
    SELECT
        b.id_booking,
        b.tanggal_booking,
        b.jumlah_peserta,
        b.total_harga,
        b.status_booking,

        u.nama AS nama_pengguna,

        p.nama_paket,

        ul.rating,
        ul.komentar

    FROM booking b

    LEFT JOIN `user` u
        ON b.id_user = u.id_user

    LEFT JOIN paket_wisata p
        ON b.id_paket = p.id_paket

    LEFT JOIN ulasan ul
        ON b.id_booking = ul.id_booking

    $whereSQL

    ORDER BY b.tanggal_booking DESC
";

$stmt = jalankanQuery(
    $conn,
    $sql,
    $types,
    $params
);

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Gagal mengambil data booking.");
}

// ==========================================
// 6. HITUNG RINGKASAN LAPORAN
// ==========================================
$sqlRingkasan = "
    SELECT

        COUNT(*) AS total_booking,

        COALESCE(
            SUM(status_booking = 'Menunggu'), 0
        ) AS menunggu,

        COALESCE(
            SUM(status_booking = 'Dikonfirmasi'), 0
        ) AS booking_dikonfirmasi,

        COALESCE(
            SUM(status_booking = 'Selesai'), 0
        ) AS selesai,

        COALESCE(
            SUM(status_booking = 'Ditolak'), 0
        ) AS ditolak,

        COALESCE(
            SUM(status_booking = 'Dibatalkan'), 0
        ) AS dibatalkan,

        COALESCE(
            SUM(
                CASE
                    WHEN status_booking IN (
                        'Dikonfirmasi',
                        'Selesai'
                    )
                    THEN total_harga
                    ELSE 0
                END
            ), 0
        ) AS total_pendapatan

    FROM booking b

    $whereSQL
";

$stmtRingkasan = jalankanQuery(
    $conn,
    $sqlRingkasan,
    $types,
    $params
);

$dataRingkasan = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmtRingkasan)
);

// ==========================================
// 7. FUNGSI FORMAT RUPIAH
// ==========================================
function rupiah($angka)
{
    return "Rp " . number_format(
        (float) $angka,
        0,
        ',',
        '.'
    );
}

// ==========================================
// 8. DATA GRAFIK STATUS BOOKING
// ==========================================
$dataGrafik = [
    'Menunggu' => (int) $dataRingkasan['menunggu'],
    'Dikonfirmasi' => (int) $dataRingkasan['booking_dikonfirmasi'],
    'Selesai' => (int) $dataRingkasan['selesai'],
    'Ditolak' => (int) $dataRingkasan['ditolak'],
    'Dibatalkan' => (int) $dataRingkasan['dibatalkan']
];

$warnaGrafik = [
    'Menunggu' => '#f0ad4e',
    'Dikonfirmasi' => '#198754',
    'Selesai' => '#0d6efd',
    'Ditolak' => '#dc3545',
    'Dibatalkan' => '#6c757d'
];

$nilaiMaksimal = max(1, max($dataGrafik));

// ==========================================
// 9. MEMBUAT HTML GRAFIK BATANG
// ==========================================
$chartHtml = '
<h3 class="judul-section">Statistik Status Booking</h3>

<table class="tabel-grafik">
';

foreach ($dataGrafik as $label => $jumlah) {

    $persentase = ($jumlah / $nilaiMaksimal) * 100;

    $warna = $warnaGrafik[$label];

    $chartHtml .= '
        <tr>
            <td class="label-grafik">
                ' . htmlspecialchars($label) . '
            </td>

            <td class="kolom-grafik">
                <table class="bar-background">
                    <tr>
                        <td style="
                            width:' . $persentase . '%;
                            background-color:' . $warna . ';
                            height:12px;
                            padding:0;
                            border:none;
                        "></td>

                        <td style="
                            width:' . (100 - $persentase) . '%;
                            background-color:#eeeeee;
                            height:12px;
                            padding:0;
                            border:none;
                        "></td>
                    </tr>
                </table>
            </td>

            <td class="jumlah-grafik">
                ' . $jumlah . '
            </td>
        </tr>
    ';
}

$chartHtml .= '</table>';

// ==========================================
// 10. INFORMASI PERIODE LAPORAN
// ==========================================
$periodeMulai = $tanggalMulai !== ''
    ? date('d-m-Y', strtotime($tanggalMulai))
    : 'Semua tanggal';

$periodeSelesai = $tanggalSelesai !== ''
    ? date('d-m-Y', strtotime($tanggalSelesai))
    : 'Semua tanggal';

// ==========================================
// 11. MEMBUAT HTML PDF
// ==========================================
$html = '
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Booking Wisata</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
        }

        h2 {
            text-align: center;
            font-size: 17px;
            margin-bottom: 5px;
            color: #173f70;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 15px;
        }

        .periode {
            margin-bottom: 15px;
            font-size: 10px;
        }

        .judul-section {
            font-size: 12px;
            color: #173f70;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-ringkasan th {
            background-color: #dce8f5;
            padding: 8px;
            border: 1px solid #999;
        }

        .tabel-ringkasan td {
            padding: 9px;
            border: 1px solid #999;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .pendapatan {
            color: #198754;
            font-size: 13px !important;
        }

        .tabel-grafik {
            width: 100%;
            margin-bottom: 15px;
        }

        .tabel-grafik td {
            border: none;
            padding: 3px;
        }

        .label-grafik {
            width: 100px;
            text-align: left;
        }

        .kolom-grafik {
            width: 75%;
        }

        .jumlah-grafik {
            width: 40px;
            text-align: center;
            font-weight: bold;
        }

        .bar-background {
            width: 100%;
            border-collapse: collapse;
        }

        .bar-background td {
            padding: 0;
            border: none;
        }

        .tabel-detail th {
            background-color: #dce8f5;
            font-weight: bold;
        }

        .tabel-detail th,
        .tabel-detail td {
            border: 1px solid #999;
            padding: 6px;
            text-align: center;
        }

        .tabel-detail td.nama,
        .tabel-detail td.paket,
        .tabel-detail td.komentar {
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }

    </style>
</head>

<body>

    <h2>LAPORAN BOOKING PAKET WISATA</h2>

    <div class="subtitle">
        Laporan Data Pemesanan Paket Wisata RPBNusa
    </div>

    <div class="periode">
        <strong>Periode:</strong>
        ' . htmlspecialchars($periodeMulai) . '
        sampai
        ' . htmlspecialchars($periodeSelesai) . '
    </div>

    <!-- RINGKASAN LAPORAN -->

    <h3 class="judul-section">
        Ringkasan Laporan
    </h3>

    <table class="tabel-ringkasan">

        <tr>
            <th>Total Booking</th>
            <th>Booking Dikonfirmasi</th>
            <th>Booking Selesai</th>
            <th>Total Pendapatan</th>
        </tr>

        <tr>

            <td>
                ' . (int) $dataRingkasan['total_booking'] . '
            </td>

            <td>
                ' . (int) $dataRingkasan['booking_dikonfirmasi'] . '
            </td>

            <td>
                ' . (int) $dataRingkasan['selesai'] . '
            </td>

            <td class="pendapatan">
                ' . rupiah($dataRingkasan['total_pendapatan']) . '
            </td>

        </tr>

    </table>

    <!-- GRAFIK STATUS BOOKING -->

    ' . $chartHtml . '

    <!-- TABEL DETAIL BOOKING -->

    <h3 class="judul-section">
        Detail Laporan Booking
    </h3>

    <table class="tabel-detail">

        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Pengguna</th>
                <th>Paket Wisata</th>
                <th>Tanggal Booking</th>
                <th>Jumlah Peserta</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Rating</th>
                <th>Komentar</th>
            </tr>
        </thead>

        <tbody>
';

// ==========================================
// 12. MENAMPILKAN DATA DETAIL BOOKING
// ==========================================
$no = 1;

while ($b = mysqli_fetch_assoc($result)) {

    $nama = htmlspecialchars(
        $b['nama_pengguna'] ?? '-',
        ENT_QUOTES,
        'UTF-8'
    );

    $paket = htmlspecialchars(
        $b['nama_paket'] ?? '-',
        ENT_QUOTES,
        'UTF-8'
    );

    $tanggal = !empty($b['tanggal_booking'])
        ? date('d-m-Y', strtotime($b['tanggal_booking']))
        : '-';

    $jumlahPeserta = (int) $b['jumlah_peserta'];

    $harga = rupiah($b['total_harga']);

    $status = htmlspecialchars(
        $b['status_booking'] ?? '-',
        ENT_QUOTES,
        'UTF-8'
    );

    $rating = $b['rating'] !== null
        ? (int) $b['rating'] . ' / 5'
        : 'Belum ada';

    $komentar = $b['komentar'] !== null
        ? htmlspecialchars(
            $b['komentar'],
            ENT_QUOTES,
            'UTF-8'
        )
        : 'Belum ada ulasan';

    $html .= '
        <tr>

            <td>' . $no++ . '</td>

            <td class="nama">
                ' . $nama . '
            </td>

            <td class="paket">
                ' . $paket . '
            </td>

            <td>
                ' . $tanggal . '
            </td>

            <td>
                ' . $jumlahPeserta . ' orang
            </td>

            <td>
                ' . $harga . '
            </td>

            <td>
                ' . $status . '
            </td>

            <td>
                ' . $rating . '
            </td>

            <td class="komentar">
                ' . $komentar . '
            </td>

        </tr>
    ';
}

// ==========================================
// 13. PENUTUP HTML
// ==========================================
$html .= '
        </tbody>

    </table>

    <div class="footer">
        Dicetak pada:
        ' . date('d-m-Y H:i:s') . '
    </div>

</body>
</html>
';

// ==========================================
// 14. GENERATE PDF DOMPDF
// ==========================================
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmtRingkasan);

$options = new Options();

$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html, 'UTF-8');

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream(
    "Laporan-Booking-RPBNusa.pdf",
    [
        "Attachment" => true
    ]
);

exit;