<?php
// Mulai sesi & koneksi
session_start();
include '../config/koneksi.php';

// Cek apakah yang masuk adalah guru
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

$nama_guru = $_SESSION['nama'] ?? 'Guru';

// Ambil data ringkasan (contoh kueri, sesuaikan dengan nama tabel kamu)
$jml_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM siswa"))['total'];
$jml_mapel = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pelajaran"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f4f8; }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background: linear-gradient(180deg, #0d6efd, #0b5ed7);
            color: white;
            padding-top: 20px;
        }
        .sidebar h4 { font-weight: 700; }
        .sidebar a {
            color: white;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid #ffc107;
        }
        .content { margin-left: 250px; padding: 30px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .stat-number { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fas fa-chalkboard-teacher"></i> GURU</h4>
    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#"><i class="fas fa-user-graduate"></i> Data Siswa</a>
    <a href="#"><i class="fas fa-book"></i> Mata Pelajaran</a>
    <a href="#"><i class="fas fa-clipboard-list"></i> Input Nilai</a>
    <a href="#"><i class="fas fa-history"></i> Riwayat Nilai</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h2 class="mb-1">Selamat Datang, <strong><?= htmlspecialchars($nama_guru) ?></strong> 👋</h2>
    <p class="text-muted mb-4">Berikut ringkasan kegiatan Anda</p>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card p-4">
                <h5 class="text-primary"><i class="fas fa-users"></i> Jumlah Siswa</h5>
                <p class="stat-number text-dark mt-2"><?= $jml_siswa ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <h5 class="text-success"><i class="fas fa-book-open"></i> Mata Pelajaran</h5>
                <p class="stat-number text-dark mt-2"><?= $jml_mapel ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <h5 class="text-warning"><i class="fas fa-clipboard-check"></i> Sudah Dinilai</h5>
                <p class="stat-number text-dark mt-2">85%</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <h5 class="text-danger"><i class="fas fa-clock"></i> Belum Dinilai</h5>
                <p class="stat-number text-dark mt-2">15%</p>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h5 class="mb-3">Aktivitas Terbaru</h5>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Input Nilai Matematika - Kelas X-A</td>
                    <td>09 Nov 2026</td>
                    <td><span class="badge bg-success">Selesai</span></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Perbarui Data Materi</td>
                    <td>08 Nov 2026</td>
                    <td><span class="badge bg-success">Selesai</span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Input Nilai Fisika - Kelas XI-B</td>
                    <td>10 Nov 2026</td>
                    <td><span class="badge bg-warning text-dark">Menunggu</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
