<?php
if (!isset($halamanAktif)) {
    $halamanAktif = 'dashboard';
}

if (!isset($judulHalaman)) {
    $judulHalaman = 'Dashboard Admin';
}

if (!isset($namaAdmin)) {
    $namaAdmin = $_SESSION['nama'] ?? 'Admin';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($judulHalaman); ?> - ADF Travel</title>

    <link rel="stylesheet"
        href="/booking_liburan/bo/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-width: 255px;
            --sidebar-bg: #173f70;
            --sidebar-active: #24558f;
            --primary: #0866f5;
            --page-bg: #f1f5fa;
            --text-main: #183153;
            --text-muted: #6b7b90;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--page-bg);
            color: var(--text-main);
            font-family: Arial, Helvetica, sans-serif;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            overflow-y: auto;
            transition: transform 0.25s ease;
        }

        .brand {
            color: white;
            text-decoration: none;
            font-size: 25px;
            font-weight: 800;
            padding: 0 12px;
            margin-bottom: 4px;
        }

        .brand:hover {
            color: white;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #d5e5fa;
            padding: 0 12px;
        }

        .sidebar-heading {
            font-size: 11px;
            letter-spacing: 1.5px;
            font-weight: bold;
            color: #c1d6ef;
            margin: 34px 12px 12px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin-bottom: 5px;
            color: #fff;
            text-decoration: none;
            border-radius: 9px;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--sidebar-active);
            color: white;
        }

        .sidebar-icon {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .sidebar-bottom {
            border-top: 1px solid rgba(255, 255, 255, .2);
            margin-top: auto;
            padding-top: 18px;
        }

        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        .topbar {
            min-height: 76px;
            background: white;
            border-bottom: 1px solid #e7edf5;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: bold;
            margin: 0;
        }

        .admin-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dceaff;
            color: #0755c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .content-area {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 7px;
            color: #173f70;
        }

        .page-description {
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 7px;
        }

        .sidebar-overlay,
        .menu-toggle {
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar-overlay.show {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .4);
                z-index: 1030;
            }

            .content-area {
                padding: 24px;
            }

            .topbar {
                padding: 15px 24px;
            }
        }

        @media (max-width: 575.98px) {
            .content-area {
                padding: 20px 15px;
            }

            .topbar {
                padding: 13px 15px;
            }

            .page-title {
                font-size: 24px;
            }

            .admin-name {
                display: none;
            }
        }

        /* Memulihkan tampilan kartu statistik dashboard */
        .stat-card {
            border: none !important;
            border-radius: 16px !important;
            background: #fff;
            box-shadow: 0 5px 18px rgba(25, 55, 90, 0.06);
            min-height: 212px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(25, 55, 90, 0.10);
        }

        .stat-card .card-body {
            padding: 22px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .icon-blue {
            background: #dce9ff;
        }

        .icon-green {
            background: #d8f3e8;
        }

        .icon-yellow {
            background: #fff0c7;
        }

        .icon-cyan {
            background: #d8f5fb;
        }

        .stat-label {
            color: #6b7b90;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-number {
            color: #173f70;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .footer-text {
            color: #6b7b90;
            font-size: 13px;
            padding-bottom: 20px;
        }

        .table-responsive {
            width: 100%;
        }

        @media (max-width: 575.98px) {
            .stat-card {
                min-height: auto;
            }
        }
    </style>
    <link rel="stylesheet"
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
</head>

<body>

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">

        <nav class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button"
                    class="btn btn-outline-primary menu-toggle"
                    id="menuToggle"
                    aria-label="Buka menu">
                    ☰
                </button>

                <p class="topbar-title">
                    <?= htmlspecialchars($judulHalaman); ?>
                </p>
            </div>

            <div class="admin-label">
                <div class="admin-avatar">
                    <?= htmlspecialchars(strtoupper(substr($namaAdmin, 0, 1))); ?>
                </div>

                <span class="admin-name">
                    <?= htmlspecialchars($namaAdmin); ?>
                </span>

                <a href="/booking_liburan/logout.php"
                    class="btn btn-outline-secondary btn-sm"
                    onclick="return confirm('Yakin ingin logout?')">
                    Logout
                </a>
            </div>
        </nav>

        <div class="content-area">