<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

$user_id = (int)($_SESSION['id'] ?? 0);
$query_guru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = $user_id AND status_aktif = 1
    LIMIT 1
");
$guru = mysqli_fetch_assoc($query_guru);
$guru_id = (int)$guru['id'];

$filter = $_GET['filter'] ?? 'semua';

// HITUNG TOTAL
$total_pelanggaran = 0;
$total_tindakan = 0;

// HITUNG JUMLAH PELANGGARAN
$q_pelanggaran = mysqli_query($koneksi, "
    SELECT COUNT(*) AS jumlah
    FROM t_pelanggaran_siswa
    WHERE guru_id = $guru_id
");
if ($q_pelanggaran) {
    $r = mysqli_fetch_assoc($q_pelanggaran);
    $total_pelanggaran = (int)$r['jumlah'];
}

// HITUNG JUMLAH TINDAKAN
$q_tindakan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS jumlah
    FROM t_tindakan
    WHERE guru_id = $guru_id
");
if ($q_tindakan) {
    $r = mysqli_fetch_assoc($q_tindakan);
    $total_tindakan = (int)$r['jumlah'];
}
$total_riwayat = $total_pelanggaran + $total_tindakan;

function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Aktivitas</title>

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

/* KONTEN */
.main {
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

/* PANEL */
.panel {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 22px;
    min-width: 0;
}

.panel h3 {
    font-size: 18px;
    font-weight: bold;
    margin: 0 0 20px;
}

/* KOTAK STATISTIK */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 22px;
}

.stat-box {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
}

.stat-box .label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 8px;
}

.stat-box .angka {
    font-size: 32px;
    font-weight: bold;
    color: #2563eb;
}

/* FILTER TOMBOL */
.filter-group {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-group .btn {
    border-radius: 6px;
    font-size: 13px;
}

/* TABEL */
.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    min-width: 800px;
    margin-bottom: 0;
    font-size: 13px;
    vertical-align: middle;
}

.table thead th {
    background: #f9fafb;
    color: #374151;
    font-weight: bold;
    white-space: nowrap;
    padding: 13px 12px;
    border-bottom: 1px solid #e5e7eb;
}

.table tbody td {
    padding: 12px;
    border-color: #e5e7eb;
}

.table tbody tr:hover {
    background: #f9fafb;
}

.empty-box {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
    font-size: 13px;
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

    .main {
        margin-left: 0;
        padding: 16px;
    }

    .stats-row {
        grid-template-columns: 1fr;
    }

    .panel {
        padding: 17px;
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
        <li><a href="dashboard_guru.php">Dashboard</a></li>
        <li><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
        <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
        <li><a href="rekap_poin.php">Rekap Poin</a></li>
        <li><a href="tindakan.php">Tindakan</a></li>
        <li><a href="riwayat.php" class="active">Riwayat</a></li>
    </ul>

    <a href="../logout.php" class="logout-btn">Logout</a>
</div>

<!-- KONTEN UTAMA -->
<div class="main">

    <div class="page-title">
        <h2>Riwayat Aktivitas Guru</h2>
        <p>Riwayat pencatatan pelanggaran dan tindakan siswa. Guru: <?= e($guru['nama']) ?></p>
    </div>

    <!-- STATISTIK -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="label">Total Riwayat</div>
            <div class="angka"><?= $total_riwayat ?></div>
        </div>
        <div class="stat-box">
            <div class="label">Riwayat Pelanggaran</div>
            <div class="angka"><?= $total_pelanggaran ?></div>
        </div>
        <div class="stat-box">
            <div class="label">Riwayat Tindakan</div>
            <div class="angka"><?= $total_tindakan ?></div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="panel">
        <h3>Filter Riwayat</h3>
        <div class="filter-group">
            <a href="riwayat.php?filter=semua" class="btn <?= $filter === 'semua' ? 'btn-primary' : 'btn-outline-primary' ?>">Semua Riwayat</a>
            <a href="riwayat.php?filter=pelanggaran" class="btn <?= $filter === 'pelanggaran' ? 'btn-primary' : 'btn-outline-primary' ?>">Pelanggaran</a>
            <a href="riwayat.php?filter=tindakan" class="btn <?= $filter === 'tindakan' ? 'btn-primary' : 'btn-outline-primary' ?>">Tindakan</a>
        </div>
    </div>

    <!-- RIWAYAT PELANGGARAN -->
    <?php if ($filter === 'semua' || $filter === 'pelanggaran'): ?>
    <div class="panel">
        <h3>Riwayat Pelanggaran Siswa</h3>
        <?php if ($total_pelanggaran > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query_p = mysqli_query($koneksi, "
                            SELECT
                                ps.id,
                                ps.tanggal,
                                ps.keterangan,
                                s.nama AS nama_siswa,
                                k.nama AS nama_kelas,
                                pk.nama AS nama_kategori,
                                pj.nama AS nama_pelanggaran,
                                pj.poin
                            FROM t_pelanggaran_siswa ps
                            INNER JOIN t_siswa s ON ps.siswa_id = s.id
                            LEFT JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_kategori_id = pk.id
                            LEFT JOIN t_pelanggaran_jenis pj ON ps.jenis_id = pj.id
                            LEFT JOIN t_kelas_siswa ks ON ps.siswa_id = ks.siswa_id AND ks.status_aktif = 1
                            LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                            WHERE ps.guru_id = $guru_id
                            ORDER BY ps.tanggal DESC, ps.id DESC
                        ");
                        while ($row = mysqli_fetch_assoc($query_p)):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-' ?></td>
                            <td><strong><?= e($row['nama_siswa']) ?></strong></td>
                            <td><?= e($row['nama_kelas'] ?? '-') ?></td>
                            <td><?= e($row['nama_kategori'] ?? '-') ?></td>
                            <td><?= e($row['nama_pelanggaran'] ?? '-') ?></td>
                            <td><strong><?= (int)$row['poin'] ?></strong></td>
                            <td><?= e($row['keterangan'] ?? '-') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-box">
                Belum ada data riwayat pelanggaran.<br>
                <span style="font-size: 12px;">Menampilkan catatan pelanggaran yang dibuat oleh akun guru ini.</span>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- RIWAYAT TINDAKAN -->
    <?php if ($filter === 'semua' || $filter === 'tindakan'): ?>
    <div class="panel">
        <h3>Riwayat Tindakan Siswa</h3>
        <?php if ($total_tindakan > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tindakan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query_t = mysqli_query($koneksi, "
                            SELECT
                                tt.id,
                                tt.tanggal,
                                tt.nama_tindakan,
                                tt.keterangan,
                                s.nama AS nama_siswa,
                                k.nama AS nama_kelas
                            FROM t_tindakan tt
                            INNER JOIN t_siswa s ON tt.siswa_id = s.id
                            LEFT JOIN t_kelas_siswa ks ON tt.siswa_id = ks.siswa_id AND ks.status_aktif = 1
                            LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                            WHERE tt.guru_id = $guru_id
                            ORDER BY tt.tanggal DESC, tt.id DESC
                        ");
                        while ($row = mysqli_fetch_assoc($query_t)):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-' ?></td>
                            <td><strong><?= e($row['nama_siswa']) ?></strong></td>
                            <td><?= e($row['nama_kelas'] ?? '-') ?></td>
                            <td><?= e($row['nama_tindakan']) ?></td>
                            <td><?= e($row['keterangan'] ?? '-') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-box">
                Belum ada data riwayat tindakan.<br>
                <span style="font-size: 12px;">Menampilkan tindakan yang diberikan oleh akun guru ini.</span>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
