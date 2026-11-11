<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

$batas_poin = 150;

// PENCARIAN
$q = trim($_GET['q'] ?? '');

// QUERY REKAP POIN SISWA
$where = [];
if ($q !== '') {
    $q_esc = mysqli_real_escape_string($koneksi, $q);
    $where[] = "s.nama LIKE '%$q_esc%'";
}

$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$query_rekap = mysqli_query($koneksi, "
    SELECT
        s.id AS siswa_id,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        COALESCE(SUM(pj.poin), 0) AS total_poin,
        COUNT(ps.id) AS jumlah_pelanggaran
    FROM t_siswa s
    LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id AND ks.status_aktif = 1
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
    LEFT JOIN t_pelanggaran_siswa ps ON s.id = ps.siswa_id
    LEFT JOIN t_pelanggaran_jenis pj ON ps.jenis_id = pj.id
    $where_sql
    GROUP BY s.id, s.nama, k.nama
    ORDER BY total_poin DESC, s.nama ASC
");

if (!$query_rekap) {
    die("Gagal mengambil data rekap: " . mysqli_error($koneksi));
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
<title>Rekap Poin</title>

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

/* PENCARIAN */
.search-bar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.search-bar .form-control {
    flex: 1;
    min-width: 280px;
}

/* TABEL */
.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    min-width: 750px;
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

/* STATUS POIN */
.poin-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 4px;
}

.poin-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s;
}

.status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.status-aman {
    background: #dcfce7;
    color: #166534;
}

.status-peringatan {
    background: #fef3c7;
    color: #92400e;
}

.status-pembinaan {
    background: #ffedd5;
    color: #9a3412;
}

.status-evaluasi {
    background: #fee2e2;
    color: #991b1b;
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

    .search-bar {
        flex-direction: column;
        align-items: stretch;
    }

    .search-bar .form-control {
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
        <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
        <li><a href="rekap_poin.php" class="active">Rekap Poin</a></li>
        <li><a href="tindakan.php">Tindakan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
    </ul>

    <a href="../logout.php" class="logout-btn">Logout</a>
</div>

<!-- KONTEN UTAMA -->
<div class="main">

    <div class="page-title">
        <h2>Rekap Poin Siswa</h2>
        <p>Ringkasan total poin pelanggaran setiap siswa. Batas evaluasi sanksi akhir: <?= $batas_poin ?> poin.</p>
    </div>

    <!-- TABEL REKAP -->
    <div class="panel">
        <h3>Daftar Rekap Poin</h3>

        <!-- PENCARIAN -->
        <form class="search-bar" method="GET">
            <input type="text" name="q" class="form-control" placeholder="Cari nama siswa..." value="<?= e($q) ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="rekap_poin.php" class="btn btn-light">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jumlah Pelanggaran</th>
                        <th>Total Poin</th>
                        <th>Progres</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_rekap) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($query_rekap)): ?>
                            <?php
                            $total_poin = (int)$row['total_poin'];
                            $persen = min(($total_poin / $batas_poin) * 100, 100);

                            if ($total_poin >= $batas_poin) {
                                $status = "Evaluasi Sanksi Akhir";
                                $kelas_status = "status-evaluasi";
                                $warna_bar = "#dc2626";
                            } elseif ($total_poin >= 100) {
                                $status = "Pembinaan Lanjutan";
                                $kelas_status = "status-pembinaan";
                                $warna_bar = "#ea580c";
                            } elseif ($total_poin >= 50) {
                                $status = "Peringatan";
                                $kelas_status = "status-peringatan";
                                $warna_bar = "#eab308";
                            } else {
                                $status = "Aman";
                                $kelas_status = "status-aman";
                                $warna_bar = "#22c55e";
                            }
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong><?= e($row['nama_siswa']) ?></strong></td>
                                <td><?= e($row['nama_kelas'] ?? '-') ?></td>
                                <td><?= (int)$row['jumlah_pelanggaran'] ?> kali</td>
                                <td><strong><?= $total_poin ?> / <?= $batas_poin ?></strong></td>
                                <td style="min-width: 120px;">
                                    <div><?= round($persen, 1) ?>%</div>
                                    <div class="poin-bar">
                                        <div class="poin-fill" style="width: <?= $persen ?>%; background: <?= $warna_bar ?>;"></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status <?= $kelas_status ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
