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

// Opsional: Hubungkan ke database jika ingin menampilkan statistik dinamis (jumlah siswa, dll)
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pelanggaran Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Hitam ala Admin */
        .sidebar {
            width: 260px;
            background: #111827;
            color: white;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 35px;
            line-height: 1.4;
            color: white;
        }

        .sidebar-menu {
            list-style: none;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            color: #9ca3af;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 8px;
            transition: 0.2s;
            font-size: 14px;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout-btn {
            color: #ef4444;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 30px;
        }

        /* Banner Selamat Datang */
        .welcome-banner {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .welcome-banner h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.5;
        }

        /* Cards Grid */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 13px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 28px;
            font-weight: bold;
            color: #111827;
        }

        .card-desc {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                Pelanggaran<br>Siswa
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard_guru.php" class="active">Dashboard</a></li>
                <li><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
                <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
            </ul>
        </div>
        <div>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="welcome-banner">
            <h2>👋 Selamat Datang, <?php echo htmlspecialchars($nama_guru); ?>!</h2>
            <p>Selamat datang di Dashboard Guru Aplikasi Pelanggaran Siswa. Kelola dan catat pelanggaran kedisiplinan siswa dengan mudah.</p>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-title">Pencatatan Saya</div>
                <div class="card-value">0</div>
                <div class="card-desc">Total pelanggaran yang dicatat</div>
            </div>
            <div class="card">
                <div class="card-title">Status Akun</div>
                <div class="card-value" style="font-size: 20px; color: #2563eb; padding-top: 5px;">Aktif (Guru)</div>
                <div class="card-desc">Hak akses level guru</div>
            </div>
        </div>
    </div>

</body>
</html>