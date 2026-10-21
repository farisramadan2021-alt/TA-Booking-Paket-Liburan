<?php
require_once __DIR__ . "/../../config/koneksi.php";

$query = mysqli_query(
    $conn,
    "SELECT * FROM paket_wisata ORDER BY id_paket ASC"
);

if (!$query) {
    die("Gagal mengambil data paket wisata: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket Wisata - RPBNusa</title>

    <link rel="stylesheet"
        href="../../bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- CSS DataTables untuk Bootstrap 5 -->
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

        .gambar-paket {
            width: 90px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
        }

        .deskripsi-paket {
            min-width: 200px;
            max-width: 300px;
        }

        .dataTables_wrapper {
            padding-top: 5px;
        }
    </style>
</head>

<body>

<div class="container-fluid px-3 px-md-4 py-4">

    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola Paket Wisata</h2>
            <p class="text-muted mb-0">
                Cari, urutkan, dan kelola data paket wisata.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="../dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>

            <a href="tambah.php" class="btn btn-primary">
                + Tambah Paket Wisata
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-3 p-md-4">

            <div class="table-responsive">
                <table id="tabelPaket"
                    class="table table-bordered table-hover table-striped
                           align-middle w-100">

                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Gambar</th>
                            <th>Nama Paket</th>
                            <th>Destinasi</th>
                            <th>Durasi</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;

                    while ($data = mysqli_fetch_assoc($query)):
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?php if (!empty($data['gambar'])): ?>
                                    <img
                                        src="../../uploads/paket_wisata/<?= htmlspecialchars($data['gambar']); ?>"
                                        alt="<?= htmlspecialchars($data['nama_paket']); ?>"
                                        class="gambar-paket">
                                <?php else: ?>
                                    <span class="text-muted">
                                        Tidak ada gambar
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="fw-semibold">
                                <?= htmlspecialchars($data['nama_paket']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($data['destinasi']); ?>
                            </td>

                            <td class="text-nowrap">
                                <?= (int) $data['durasi']; ?> Hari
                            </td>

                            <td class="text-nowrap">
                                Rp <?= number_format(
                                    (float) $data['harga'],
                                    0, ',', '.'
                                ); ?>
                            </td>

                            <td class="deskripsi-paket">
                                <?= htmlspecialchars($data['deskripsi']); ?>
                            </td>

                            <td>
                                <?php if ($data['status'] === 'Aktif'): ?>
                                    <span class="badge text-bg-success">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-nowrap">
                                <a
                                    href="edit.php?id=<?= (int) $data['id_paket']; ?>"
                                    class="btn btn-warning btn-sm mb-1">
                                    Edit
                                </a>

                                <a
                                    href="hapus.php?id=<?= (int) $data['id_paket']; ?>"
                                    class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus paket wisata ini?');">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<script src="../../bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- JavaScript DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPaket').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        order: [],
        columnDefs: [
            {
                targets: [1, 8],
                orderable: false
            }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data yang dicari tidak ditemukan",
            emptyTable: "Belum ada data paket wisata",
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