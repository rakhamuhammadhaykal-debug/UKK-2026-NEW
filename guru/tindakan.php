<?php
session_start();
require_once '../config/koneksi.php';

if (empty($_SESSION['login']) || ($_SESSION['role'] ?? '') !== 'guru') {
    header('Location: ../login.php');
    exit;
}

$user_id = (int)($_SESSION['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "
    SELECT id, nama FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$guru) exit("Data guru tidak ditemukan.");
$guru_id = (int)$guru['id'];

$error = "";
if (empty($_SESSION['csrf_tindakan'])) {
    $_SESSION['csrf_tindakan'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_tindakan'], $token)) {
        $error = "Permintaan tidak valid.";
    } else {
        $siswa_id = (int)($_POST['siswa_id'] ?? 0);
        $nama_tindakan = trim($_POST['nama_tindakan'] ?? '');
        $tanggal = $_POST['tanggal_tindakan'] ?? '';
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $jenis_valid = ['Teguran Lisan','Teguran Tertulis','Pembinaan','Pemanggilan Orang Tua','Skorsing'];
        $tanggal_valid = DateTime::createFromFormat('!Y-m-d', $tanggal);
        
        if ($siswa_id <= 0 || !in_array($nama_tindakan, $jenis_valid, true) || !$tanggal_valid || $tanggal_valid->format('Y-m-d') !== $tanggal) {
            $error = "Mohon lengkapi data tindakan dengan benar.";
        } else {
            $cek = mysqli_prepare($koneksi, "
                SELECT siswa_id, nama_siswa, nama_kelas, SUM(poin) AS total_poin
                FROM t_pelanggaran_siswa WHERE siswa_id = ? AND guru_id = ?
                GROUP BY siswa_id, nama_siswa, nama_kelas LIMIT 1
            ");
            mysqli_stmt_bind_param($cek, "ii", $siswa_id, $guru_id);
            mysqli_stmt_execute($cek);
            $siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
            
            if (!$siswa) {
                $error = "Siswa tidak ditemukan dalam catatan pelanggaran guru.";
            } else {
                $nama_siswa = $siswa['nama_siswa'];
                $nama_kelas = $siswa['nama_kelas'];
                $total_poin = (int)$siswa['total_poin'];
                
                $insert = mysqli_prepare($koneksi, "
                    INSERT INTO t_tindakan (guru_id, siswa_id, nama_siswa, nama_kelas, nama_tindakan, tanggal_tindakan, total_poin, deskripsi)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                mysqli_stmt_bind_param($insert, "iissssis", $guru_id, $siswa_id, $nama_siswa, $nama_kelas, $nama_tindakan, $tanggal, $total_poin, $deskripsi);
                
                if (mysqli_stmt_execute($insert)) {
                    header("Location: tindakan.php?berhasil=1");
                    exit;
                } else {
                    $error = "Gagal menyimpan tindakan: " . mysqli_stmt_error($insert);
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_tindakan'], $token)) {
        $error = "Permintaan tidak valid.";
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $hapus = mysqli_prepare($koneksi, "DELETE FROM t_tindakan WHERE id = ? AND guru_id = ?");
        mysqli_stmt_bind_param($hapus, "ii", $id, $guru_id);
        mysqli_stmt_execute($hapus);
        header("Location: tindakan.php?dihapus=1");
        exit;
    }
}

$sql_siswa = "
    SELECT siswa_id, nama_siswa, nama_kelas, SUM(poin) AS total_poin
    FROM t_pelanggaran_siswa WHERE guru_id = ?
    GROUP BY siswa_id, nama_siswa, nama_kelas ORDER BY nama_siswa ASC
";
$stmt_siswa = mysqli_prepare($koneksi, $sql_siswa);
mysqli_stmt_bind_param($stmt_siswa, "i", $guru_id);
mysqli_stmt_execute($stmt_siswa);
$siswa_list = mysqli_stmt_get_result($stmt_siswa);

$riwayat = mysqli_prepare($koneksi, "
    SELECT * FROM t_tindakan WHERE guru_id = ? ORDER BY tanggal_tindakan DESC, id DESC
");
mysqli_stmt_bind_param($riwayat, "i", $guru_id);
mysqli_stmt_execute($riwayat);
$data_riwayat = mysqli_stmt_get_result($riwayat);
$total_tindakan = mysqli_num_rows($data_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tindakan Pelanggaran Siswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    background: #f3f4f6;
    font-family: Arial, sans-serif;
    color: #1f2937;
}

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

.sidebar-menu li { margin: 5px 0; }

.sidebar-menu a {
    display: block;
    padding: 11px 13px;
    color: #cbd5e1;
    text-decoration: none;
    border-radius: 8px;
    font-size: 13px;
    transition: 0.2s;
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
    color: #f87171 !important;
    text-decoration: none;
    font-size: 13px;
    border-radius: 8px;
    transition: 0.2s;
}

.logout-btn:hover {
    background: #7f1d1d;
    color: white !important;
}

.main {
    margin-left: 235px;
    padding: 30px;
    min-width: 0;
}

.page-title { margin-bottom: 22px; }
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

.panel {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 22px;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 22px;
    height: 100%;
}
.stat-card .label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 8px;
}
.stat-number {
    font-size: 28px;
    font-weight: bold;
    color: #2563eb;
}

.form-label {
    font-weight: 600;
    font-size: 14px;
}
.form-control, .form-select {
    border-radius: 8px;
    padding: 10px 12px;
}

.table { margin-bottom: 0; }
.table th {
    background: #f9fafb;
    color: #374151;
    white-space: nowrap;
    font-size: 13px;
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}
.table td {
    font-size: 13px;
    padding: 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
}
.table tbody tr:hover { background: #f9fafb; }

.badge { font-size: 12px; padding: 4px 10px; }

@media (max-width: 760px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding: 18px;
    }
    .sidebar-menu {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 4px;
    }
    .main {
        margin-left: 0;
        padding: 16px;
    }
    .stat-number { font-size: 25px; }
}
</style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">Pelanggaran<br>Siswa</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard_guru.php">Dashboard</a></li>
        <li><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
        <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
        <li><a href="rekap_poin.php">Rekap Poin</a></li>
        <li><a class="active" href="tindakan.php">Tindakan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li><a href="../logout.php" class="logout-btn">Logout</a></li>
    </ul>
</aside>

<main class="main">
    <div class="page-title">
        <h2>Tindakan Pelanggaran</h2>
        <p>Kelola tindakan dan pembinaan siswa. Guru: <strong><?= htmlspecialchars($guru['nama']) ?></strong></p>
    </div>

    <?php if (isset($_GET['berhasil'])): ?>
        <div class="alert alert-success">Tindakan berhasil disimpan!</div>
    <?php endif; ?>
    <?php if (isset($_GET['dihapus'])): ?>
        <div class="alert alert-success">Tindakan berhasil dihapus!</div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="label">Total Tindakan Saya</div>
                <div class="stat-number"><?= $total_tindakan ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="label">Status Akun</div>
                <div class="stat-number">Aktif</div>
                <small class="text-secondary">Hak akses Guru</small>
            </div>
        </div>
    </div>

    <div class="panel">
        <h5 class="fw-bold mb-3">Tambah Tindakan Siswa</h5>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_tindakan']) ?>">
            
            <div class="mb-3">
                <label class="form-label">Pilih Siswa</label>
                <select name="siswa_id" class="form-select" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                        <option value="<?= (int)$s['siswa_id'] ?>">
                            <?= htmlspecialchars($s['nama_siswa']) ?> - <?= htmlspecialchars($s['nama_kelas']) ?> (<?= (int)$s['total_poin'] ?> poin)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Tindakan</label>
                    <select name="nama_tindakan" class="form-select" required>
                        <option value="">-- Pilih Tindakan --</option>
                        <option value="Teguran Lisan">Teguran Lisan</option>
                        <option value="Teguran Tertulis">Teguran Tertulis</option>
                        <option value="Pembinaan">Pembinaan</option>
                        <option value="Pemanggilan Orang Tua">Pemanggilan Orang Tua</option>
                        <option value="Skorsing">Skorsing</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Tindakan</label>
                    <input type="date" name="tanggal_tindakan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan / Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tuliskan alasan dan keterangan tindakan..."></textarea>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">Simpan Tindakan</button>
        </form>
    </div>

    <div class="panel">
        <h5 class="fw-bold mb-3">Riwayat Tindakan</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Total Poin</th>
                        <th>Jenis Tindakan</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_tindakan > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($r = mysqli_fetch_assoc($data_riwayat)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= !empty($r['tanggal_tindakan']) ? date('d-m-Y', strtotime($r['tanggal_tindakan'])) : '-' ?></td>
                                <td><strong><?= htmlspecialchars($r['nama_siswa'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
                                <td><?= (int)($r['total_poin'] ?? 0) ?> poin</td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($r['nama_tindakan']) ?></span></td>
                                <td><?= htmlspecialchars($r['deskripsi'] ?? '-') ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus tindakan ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_tindakan']) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                        <button type="submit" name="hapus" value="1" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">Belum ada tindakan yang diberikan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
