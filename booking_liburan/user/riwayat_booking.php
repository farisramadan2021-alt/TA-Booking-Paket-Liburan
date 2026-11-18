<?php
session_start();
require_once __DIR__ . "/../config/koneksi.php";

// Cek login pelanggan
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// Ambil riwayat booking dan pembayaran terakhir
$query = mysqli_query($conn, "
    SELECT
        b.*,
        w.nama_paket,
        w.destinasi,
        w.gambar,
        pb.id_pembayaran,
        pb.status_pembayaran
    FROM booking b
    LEFT JOIN paket_wisata w
        ON b.id_paket = w.id_paket
    LEFT JOIN pembayaran pb
        ON pb.id_pembayaran = (
            SELECT MAX(p2.id_pembayaran)
            FROM pembayaran p2
            WHERE p2.id_booking = b.id_booking
        )
    WHERE b.id_user = $id_user
    ORDER BY b.id_booking DESC
");

if (!$query) {
    die("Gagal mengambil riwayat booking: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - RPBNusa</title>

    <link rel="stylesheet"
        href="../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- CSS DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background-color: #e3f2fd;
            font-family: Arial, sans-serif;
        }

        .judul {
            color: #0751a8;
            font-weight: bold;
        }

        .card-riwayat {
            border: none;
            border-radius: 14px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }

        .gambar-paket {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }

        .harga {
            color: #0066ff;
            font-weight: bold;
            white-space: nowrap;
        }

        .table th {
            white-space: nowrap;
        }

        /* ===== Perapihan DataTables ===== */
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
            font-size: 14px;
            color: #495057;
            white-space: nowrap;
        }

        .dataTables_wrapper .dataTables_filter label {
            justify-content: flex-end;
        }

        .dataTables_wrapper .dataTables_length select {
            width: auto;
            min-width: 70px;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 240px;
            margin-left: 0 !important;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 14px;
            color: #6c757d;
            padding-top: 0;
        }

        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            justify-content: flex-end;
        }

        .dataTables_wrapper .dataTables_paginate .pagination {
            margin-bottom: 0;
        }

        @media (max-width: 767px) {
            .dataTables_wrapper .dataTables_filter label {
                justify-content: flex-start;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }

            .dataTables_wrapper .dataTables_info {
                text-align: center;
                margin-bottom: 8px;
            }

            .dataTables_wrapper .dataTables_paginate {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h2 class="judul mb-1">Riwayat Booking</h2>
            <p class="text-muted mb-0">
                Daftar perjalanan yang pernah kamu pesan.
            </p>
        </div>

        <div>
            <a href="dashboard.php" class="btn btn-secondary">
                Kembali Dashboard
            </a>

            <a href="paket_wisata/index.php" class="btn btn-primary">
                + Booking Paket
            </a>
        </div>

    </div>

    <div class="card card-riwayat">
        <div class="card-body p-3">

            <!-- Filter status untuk DataTables -->
            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                <span class="fw-semibold text-secondary me-1">Filter Status:</span>
                <div class="btn-group flex-wrap" role="group" id="filterStatus">
                    <button type="button" class="btn btn-sm btn-outline-primary active" data-status="">Semua</button>
                    <button type="button" class="btn btn-sm btn-outline-warning text-dark" data-status="Menunggu">Menunggu</button>
                    <button type="button" class="btn btn-sm btn-outline-success" data-status="Dikonfirmasi">Dikonfirmasi</button>
                    <button type="button" class="btn btn-sm btn-outline-info text-dark" data-status="Proses">Proses</button>
                    <button type="button" class="btn btn-sm btn-outline-success" data-status="Lunas" data-col="6">Lunas</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-status="Ditolak">Ditolak</button>
                </div>
            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Paket Wisata</th>
                            <th>Keberangkatan</th>
                            <th>Peserta</th>
                            <th>Total Harga</th>
                            <th>Status Booking</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    if (mysqli_num_rows($query) > 0):
                        while ($data = mysqli_fetch_assoc($query)):

                            $status = $data['status_booking'] ?? 'Menunggu';
                            $status_bayar = $data['status_pembayaran'] ?? '';

                            $gambar = !empty($data['gambar'])
                                ? "../uploads/paket_wisata/" . basename($data['gambar'])
                                : "../uploads/paket_wisata/default.jpg";
                    ?>

                        <tr>

                            <td><?= $no++; ?></td>

                            <td>
                                <div class="d-flex align-items-center gap-3">

                                    <img
                                        src="<?= htmlspecialchars($gambar); ?>"
                                        class="gambar-paket"
                                        alt="Foto Paket Wisata"
                                        onerror="this.style.display='none';">

                                    <div>
                                        <strong>
                                            <?= htmlspecialchars($data['nama_paket'] ?? '-'); ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($data['destinasi'] ?? '-'); ?>
                                        </small>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <?= !empty($data['tanggal_keberangkatan'])
                                    ? date('d-m-Y', strtotime($data['tanggal_keberangkatan']))
                                    : '-'; ?>
                            </td>

                            <td>
                                <?= (int) $data['jumlah_peserta']; ?> orang
                            </td>

                            <td class="harga">
                                Rp <?= number_format(
                                    (float) $data['total_harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td>
                                <?php if ($status === 'Dikonfirmasi'): ?>
                                    <span class="badge bg-success">
                                        Dikonfirmasi
                                    </span>
                                <?php elseif ($status === 'Ditolak'): ?>
                                    <span class="badge bg-danger">
                                        Ditolak
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">
                                        <?= htmlspecialchars($status); ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($status !== 'Dikonfirmasi'): ?>

                                    <span class="badge bg-secondary">
                                        Belum tersedia
                                    </span>

                                <?php elseif ($status_bayar === 'Terverifikasi'): ?>

                                    <span class="badge bg-success">
                                        Lunas
                                    </span>

                                <?php elseif ($status_bayar === 'Menunggu'): ?>

                                    <span class="badge bg-warning text-dark">
                                        Menunggu Verifikasi
                                    </span>

                                <?php elseif ($status_bayar === 'Ditolak'): ?>

                                    <span class="badge bg-danger">
                                        Ditolak
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        Belum Dibayar
                                    </span>

                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($status === 'Dikonfirmasi'): ?>

                                    <?php if ($status_bayar === 'Terverifikasi'): ?>

                                        <a
                                            href="struk_booking.php?id=<?= (int) $data['id_booking']; ?>"
                                            target="_blank"
                                            class="btn btn-success btn-sm">
                                            Lihat Struk
                                        </a>

                                    <?php elseif ($status_bayar === 'Menunggu'): ?>

                                        <span class="text-warning">
                                            Menunggu verifikasi
                                        </span>

                                    <?php else: ?>

                                        <a
                                            href="Pembayaran.php?id_booking=<?= (int) $data['id_booking']; ?>"
                                            class="btn btn-primary btn-sm">

                                            <?= $status_bayar === 'Ditolak'
                                                ? 'Bayar Ulang'
                                                : 'Bayar Sekarang'; ?>

                                        </a>

                                    <?php endif; ?>

                                <?php elseif ($status === 'Ditolak'): ?>

                                    <span class="text-muted">
                                        Booking ditolak
                                    </span>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Menunggu konfirmasi
                                    </span>

                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php
                        endwhile;
                    else:
                    ?>

                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada riwayat booking.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<script src="../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('.table').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            paging: true,
            pagingType: 'simple_numbers',
            searching: true,
            ordering: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            dom: "<'row align-items-center mb-2'<'col-md-6 col-12'l><'col-md-6 col-12'f>>" +
                 "<'row'<'col-12'tr>>" +
                 "<'row align-items-center mt-2'<'col-md-6 col-12'i><'col-md-6 col-12'p>>",
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                search: 'Cari:',
                searchPlaceholder: 'Cari riwayat booking...',
                zeroRecords: 'Data tidak ditemukan',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data yang ditampilkan',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }
            }
        });

        // Filter berdasarkan kolom Status Booking (kolom ke-6, indeks 5)
        $('#filterStatus button').on('click', function () {
            $('#filterStatus button').removeClass('active');
            $(this).addClass('active');

            var status = $(this).data('status');
            var kolom = $(this).data('col') || 5; // Lunas membaca kolom Status Pembayaran (indeks 6)

            table.columns().search('');

            if (status !== '') {
                table.column(kolom).search('^\\s*' + status + '\\s*$', true, false);
            }

            table.draw();
        });
    });
</script>

</body>
</html>