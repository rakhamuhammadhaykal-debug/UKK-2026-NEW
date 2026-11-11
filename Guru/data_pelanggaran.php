<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

// Ambil data guru yang sedang login
$user_id = (int)($_SESSION['id'] ?? 0);
$query_guru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = $user_id AND status_aktif = 1
    LIMIT 1
");
$guru = mysqli_fetch_assoc($query_guru);
$guru_id = (int)$guru['id'];

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// PROSES HAPUS DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        header('Location: data_pelanggaran.php?error=token');
        exit;
    }
    $id = (int)($_POST['id'] ?? 0);
    $hapus = mysqli_query($koneksi, "
        DELETE FROM t_pelanggaran_siswa
        WHERE id = $id AND guru_id = $guru_id
    ");
    if ($hapus && mysqli_affected_rows($koneksi) > 0) {
        header('Location: data_pelanggaran.php?terhapus=1');
    } else {
        header('Location: data_pelanggaran.php?error=hapus');
    }
    exit;
}

// PENCARIAN & FILTER
$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');

// QUERY DATA PELANGGARAN
$where = ["ps.guru_id = $guru_id"];
if ($q !== '') {
    $q_esc = mysqli_real_escape_string($koneksi, $q);
    $where[] = "(
        s.nama LIKE '%$q_esc%'
        OR k.nama LIKE '%$q_esc%'
        OR pj.nama LIKE '%$q_esc%'
    )";
}
if ($status !== '') {
    $status_esc = mysqli_real_escape_string($koneksi, $status);
    $where[] = "ps.status = '$status_esc'";
}
$where_sql = implode(' AND ', $where);

$query_data = mysqli_query($koneksi, "
    SELECT
        ps.id,
        ps.tanggal,
        ps.keterangan,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        pk.nama AS nama_kategori,
        pj.nama AS nama_pelanggaran,
        pj.poin,
        ps.status
    FROM t_pelanggaran_siswa ps
    INNER JOIN t_siswa s ON ps.siswa_id = s.id
    LEFT JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_kategori_id = pk.id
    LEFT JOIN t_pelanggaran_jenis pj ON ps.jenis_id = pj.id
    LEFT JOIN t_kelas_siswa ks ON ps.siswa_id = ks.siswa_id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
    WHERE $where_sql
    ORDER BY ps.tanggal DESC, ps.id DESC
");

if (!$query_data) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

$jumlah = mysqli_num_rows($query_data);
$total_poin = 0;
$data_list = [];
while ($row = mysqli_fetch_assoc($query_data)) {
    $data_list[] = $row;
    $total_poin += (int)$row['poin'];
}

function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Pelanggaran</title>

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
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 15px;
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

/* STATISTIK */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.stat-item .label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 5px;
}

.stat-item .value {
    font-size: 24px;
    font-weight: bold;
    color: #111827;
}

/* PENCARIAN */
.search-bar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.search-bar .form-control,
.search-bar .form-select {
    flex: 1;
    min-width: 220px;
}

/* TABEL */
.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    min-width: 900px;
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

.status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.status-Proses {
    background: #dbeafe;
    color: #1e40af;
}

.status-Ditindaklanjuti {
    background: #fef3c7;
    color: #92400e;
}

.status-Selesai {
    background: #dcfce7;
    color: #166534;
}

.status-tercatat {
    background: #f3f4f6;
    color: #374151;
}

.btn-sm {
    padding: 4px 10px;
    font-size: 12px;
    border-radius: 6px;
}

.btn-outline-danger {
    border-color: #fca5a5;
    color: #dc2626;
}

.btn-outline-danger:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #dc2626;
}

.alert {
    font-size: 13px;
    border-radius: 8px;
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

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .search-bar {
        flex-direction: column;
        align-items: stretch;
    }

    .search-bar .form-control,
    .search-bar .form-select {
        width: 100%;
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
        <li><a href="data_pelanggaran.php" class="active">Data Pelanggaran</a></li>
        <li><a href="rekap_poin.php">Rekap Poin</a></li>
        <li><a href="tindakan.php">Tindakan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
    </ul>

    <a href="../logout.php" class="logout-btn">Logout</a>
</div>

<!-- KONTEN UTAMA -->
<div class="main">

    <div class="page-title">
        <div>
            <h2>Data Pelanggaran Saya</h2>
            <p>Data yang dicatat oleh <?= e($guru['nama']) ?></p>
        </div>
        <a href="catat_pelanggaran.php" class="btn btn-primary">+ Catat Pelanggaran</a>
    </div>

    <!-- NOTIFIKASI -->
    <?php if (isset($_GET['terhapus'])): ?>
        <div class="alert alert-success">
            Data pelanggaran berhasil dihapus.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-warning">
            Permintaan tidak dapat diproses.
        </div>
    <?php endif; ?>

    <!-- RINGKASAN DATA -->
    <div class="panel">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="label">Jumlah Pelanggaran Dicatat</div>
                <div class="value"><?= $jumlah ?></div>
            </div>
            <div class="stat-item">
                <div class="label">Total Poin dari Catatan Ini</div>
                <div class="value"><?= $total_poin ?> poin</div>
            </div>
        </div>
    </div>

    <!-- TABEL DATA -->
    <div class="panel">
        <h3>Daftar Catatan Pelanggaran</h3>

        <!-- PENCARIAN & FILTER -->
        <form class="search-bar" method="GET">
            <input type="text" name="q" class="form-control" placeholder="Cari nama siswa, kelas, atau pelanggaran..." value="<?= e($q) ?>">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <?php foreach (['Proses', 'Ditindaklanjuti', 'Selesai', 'tercatat'] as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
                        <?= e($s) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="data_pelanggaran.php" class="btn btn-light">Reset</a>
        </form>

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
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_list)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Belum ada data pelanggaran yang dicatat oleh akun guru ini.<br>
                                Silakan gunakan menu <strong>Catat Pelanggaran</strong>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($data_list as $row): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?= !empty($row['tanggal'])
                                        ? date('d-m-Y', strtotime($row['tanggal']))
                                        : '-'; ?>
                                </td>
                                <td><?= e($row['nama_siswa']) ?></td>
                                <td><?= e($row['nama_kelas'] ?? '-') ?></td>
                                <td><?= e($row['nama_kategori'] ?? '-') ?></td>
                                <td><?= e($row['nama_pelanggaran'] ?? '-') ?></td>
                                <td><strong><?= (int)$row['poin'] ?></strong></td>
                                <td>
                                    <?php $s = $row['status'] ?? 'tercatat'; ?>
                                    <span class="status status-<?= e($s) ?>">
                                        <?= e($s) ?>
                                    </span>
                                </td>
                                <td><?= e($row['keterangan'] ?? '-') ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" name="hapus" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
