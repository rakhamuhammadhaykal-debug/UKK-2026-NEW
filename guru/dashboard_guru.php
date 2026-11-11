
<?php
// guru/dashboard_guru.php

session_start();

// Cek apakah user sudah login dan rolenya guru
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['pesan_error'] = 'Silakan login terlebih dahulu sebagai guru.';
    header('Location: ../login.php');
    exit;
}

$nama_guru = $_SESSION['name'];

// Koneksi database
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            font-family: Arial, sans-serif;
            color: #1f2937;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 215px;
            padding: 25px 17px;
            background: #111827;
            color: white;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-brand {
            font-size: 18px;
            font-weight: bold;
            line-height: 1.4;
            margin-bottom: 30px;
            color: white;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 5px 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 11px 13px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            transition: .2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout-btn {
            display: block;
            margin-top: 25px;
            padding: 11px 13px;
            color: #f87171;
            text-decoration: none;
            font-size: 13px;
            border-radius: 8px;
        }

        .logout-btn:hover {
            background: #7f1d1d;
            color: white;
        }

        /* KONTEN UTAMA */
        .main-content {
            margin-left: 235px;
            padding: 30px;
            min-width: 0;
        }

        .page-title {
            margin-bottom: 22px;
        }

        .page-title h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 7px;
        }

        .page-title p {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
        }

        /* BANNER */
        .welcome-banner {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 22px;
        }

        .welcome-banner h2 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
            color: #eff6ff;
        }

        /* KARTU */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 22px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: none;
            min-width: 0;
        }

        .card-title {
            font-size: 13px;
            font-weight: bold;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .card-value {
            font-size: 28px;
            font-weight: bold;
            color: #111827;
        }

        .card-desc {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 7px;
        }

        /* RESPONSIVE */
        @media (max-width: 760px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 18px;
            }

            .sidebar-brand {
                margin-bottom: 15px;
            }

            .sidebar-menu {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 4px;
            }

            .logout-btn {
                margin-top: 10px;
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .welcome-banner {
                padding: 20px;
            }

            .welcome-banner h2 {
                font-size: 19px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-brand">
            Pelanggaran<br>Siswa
        </div>

        <ul class="sidebar-menu">
            <li><a href="dashboard_guru.php" class="active">Dashboard</a></li>
            <li><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
            <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
            <li><a href="rekap_poin.php">Rekap Poin</a></li>
            <li><a href="tindakan.php">Tindakan</a></li>
            <li><a href="riwayat.php">Riwayat</a></li>
        </ul>

        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content">

        <div class="page-title">
            <h2>Dashboard Guru</h2>
            <p>Ringkasan aplikasi pelanggaran siswa.</p>
        </div>

        <div class="welcome-banner">
            <h2>👋 Selamat Datang, <?php echo htmlspecialchars($nama_guru); ?>!</h2>

            <p>
                Selamat datang di Dashboard Guru Aplikasi Pelanggaran Siswa.
                Kelola dan catat pelanggaran kedisiplinan siswa dengan mudah.
            </p>
        </div>

        <div class="card-grid">

            <div class="card">
                <div class="card-title">Pencatatan Saya</div>

                <div class="card-value">0</div>

                <div class="card-desc">
                    Total pelanggaran yang dicatat
                </div>
            </div>

            <div class="card">
                <div class="card-title">Status Akun</div>

                <div class="card-value" style="font-size: 20px; color: #2563eb; padding-top: 5px;">
                    Aktif (Guru)
                </div>

                <div class="card-desc">
                    Hak akses level guru
                </div>
            </div>

        </div>

    </div>

</body>
</html>
