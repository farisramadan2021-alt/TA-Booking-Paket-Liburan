
<?php
$halamanAktif = $halamanAktif ?? 'dashboard';
?>

<aside class="sidebar" id="sidebar">
    <a href="/booking_liburan/admin/dashboard.php" class="brand">
        <span class="brand-icon">
            <i class="bi bi-airplane-fill"></i>
        </span>
        <span>ADF Travel</span>
    </a>

    <div class="brand-subtitle">Admin Panel</div>

    <div class="sidebar-heading">MENU UTAMA</div>

    <a href="/booking_liburan/admin/dashboard.php"
       class="sidebar-link <?= $halamanAktif === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill sidebar-icon"></i>
        <span>Dashboard</span>
    </a>

    <a href="/booking_liburan/admin/paket_wisata/index.php"
       class="sidebar-link <?= $halamanAktif === 'paket' ? 'active' : '' ?>">
        <i class="bi bi-map-fill sidebar-icon"></i>
        <span>Paket Wisata</span>
    </a>

    <a href="/booking_liburan/admin/pengguna/index.php"
       class="sidebar-link <?= $halamanAktif === 'pengguna' ? 'active' : '' ?>">
        <i class="bi bi-people-fill sidebar-icon"></i>
        <span>Data Pengguna</span>
    </a>

    <a href="/booking_liburan/admin/booking/index.php"
       class="sidebar-link <?= $halamanAktif === 'booking' ? 'active' : '' ?>">
        <i class="bi bi-calendar2-check-fill sidebar-icon"></i>
        <span>Data Booking</span>
    </a>

    <a href="/booking_liburan/admin/pembayaran/index.php"
       class="sidebar-link <?= $halamanAktif === 'pembayaran' ? 'active' : '' ?>">
        <i class="bi bi-credit-card-2-front-fill sidebar-icon"></i>
        <span>Pembayaran</span>
    </a>

    <div class="sidebar-heading">LAPORAN DAN LAINNYA</div>

    <a href="/booking_liburan/admin/laporan/index.php"
       class="sidebar-link <?= $halamanAktif === 'laporan' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-bar-graph-fill sidebar-icon"></i>
        <span>Laporan Booking</span>
    </a>
    </div>
</aside>
