
<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

$batas_poin = 150;
$pesan_error = "";

// SIMPAN PELANGGARAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $kategori_id = (int)($_POST['pelanggaran_kategori_id'] ?? 0);
    $jenis_id = (int)($_POST['jenis_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($siswa_id <= 0 || $kategori_id <= 0 || $jenis_id <= 0) {
        $pesan_error = "Mohon lengkapi semua pilihan.";
    } elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
        !checkdate(
            (int)substr($tanggal, 5, 2),
            (int)substr($tanggal, 8, 2),
            (int)substr($tanggal, 0, 4)
        )
    ) {
        $pesan_error = "Tanggal tidak valid.";
    } else {
        $tanggal_db = mysqli_real_escape_string($koneksi, $tanggal);
        $keterangan_db = mysqli_real_escape_string($koneksi, $keterangan);

        $cek_jenis = mysqli_query($koneksi, "
            SELECT id, poin
            FROM t_pelanggaran_jenis
            WHERE id = $jenis_id
            AND kategori_id = $kategori_id
            AND status_aktif = 1
            LIMIT 1
        ");

        if (!$cek_jenis) {
            $pesan_error = "Gagal memeriksa jenis pelanggaran: " . mysqli_error($koneksi);
        } elseif (mysqli_num_rows($cek_jenis) == 0) {
            $pesan_error = "Jenis pelanggaran tidak ditemukan atau tidak sesuai kategori.";
        } else {
            $cek_siswa = mysqli_query($koneksi, "
                SELECT siswa_id
                FROM t_kelas_siswa
                WHERE siswa_id = $siswa_id
                AND status_aktif = 1
                LIMIT 1
            ");

            if (!$cek_siswa || mysqli_num_rows($cek_siswa) == 0) {
                $pesan_error = "Siswa tidak ditemukan atau tidak aktif.";
            } else {
                $query_simpan = mysqli_query($koneksi, "
                    INSERT INTO t_pelanggaran_siswa
                    (
                        siswa_id,
                        pelanggaran_kategori_id,
                        jenis_id,
                        tanggal,
                        keterangan
                    )
                    VALUES
                    (
                        $siswa_id,
                        $kategori_id,
                        $jenis_id,
                        '$tanggal_db',
                        '$keterangan_db'
                    )
                ");

                if ($query_simpan) {
                    header("Location: catat_pelanggaran.php?berhasil=1");
                    exit;
                } else {
                    $pesan_error = "Gagal menyimpan: " . mysqli_error($koneksi);
                }
            }
        }
    }
}

// DATA KELAS
$query_kelas = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if (!$query_kelas) {
    die("Gagal mengambil data kelas: " . mysqli_error($koneksi));
}

// DATA SISWA
$query_siswa = mysqli_query($koneksi, "
    SELECT
        ks.siswa_id,
        s.nama,
        ks.kelas_id
    FROM t_kelas_siswa ks
    INNER JOIN t_siswa s ON ks.siswa_id = s.id
    WHERE ks.status_aktif = 1
    ORDER BY s.nama ASC
");

if (!$query_siswa) {
    die("Gagal mengambil data siswa: " . mysqli_error($koneksi));
}

// DATA KATEGORI
$query_kategori = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_pelanggaran_kategori
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if (!$query_kategori) {
    die("Gagal mengambil kategori: " . mysqli_error($koneksi));
}

// DATA JENIS PELANGGARAN
$query_jenis = mysqli_query($koneksi, "
    SELECT id, kategori_id, nama, poin
    FROM t_pelanggaran_jenis
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if (!$query_jenis) {
    die("Gagal mengambil jenis pelanggaran: " . mysqli_error($koneksi));
}

$jenis_data = [];

while ($jenis = mysqli_fetch_assoc($query_jenis)) {
    $jenis_data[] = $jenis;
}

// JUMLAH SISWA YANG MENCAPAI 150 POIN
$query_evaluasi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS jumlah
    FROM (
        SELECT ps.siswa_id
        FROM t_pelanggaran_siswa ps
        INNER JOIN t_pelanggaran_jenis pj ON ps.jenis_id = pj.id
        GROUP BY ps.siswa_id
        HAVING SUM(pj.poin) >= $batas_poin
    ) AS evaluasi
");

if (!$query_evaluasi) {
    die("Gagal menghitung evaluasi: " . mysqli_error($koneksi));
}

$data_evaluasi = mysqli_fetch_assoc($query_evaluasi);
$jumlah_evaluasi = (int)$data_evaluasi['jumlah'];

// RIWAYAT PELANGGARAN
$query_riwayat = mysqli_query($koneksi, "
    SELECT
        ps.id,
        ps.siswa_id,
        ps.tanggal,
        ps.keterangan,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        pk.nama AS nama_kategori,
        pj.nama AS nama_jenis,
        pj.poin AS poin,
        (
            SELECT COALESCE(SUM(pj2.poin), 0)
            FROM t_pelanggaran_siswa ps2
            INNER JOIN t_pelanggaran_jenis pj2 ON ps2.jenis_id = pj2.id
            WHERE ps2.siswa_id = ps.siswa_id
        ) AS total_poin
    FROM t_pelanggaran_siswa ps
    INNER JOIN t_siswa s ON ps.siswa_id = s.id
    LEFT JOIN t_pelanggaran_kategori pk
        ON ps.pelanggaran_kategori_id = pk.id
    LEFT JOIN t_pelanggaran_jenis pj
        ON ps.jenis_id = pj.id
    LEFT JOIN (
        SELECT siswa_id, MAX(kelas_id) AS kelas_id
        FROM t_kelas_siswa
        WHERE status_aktif = 1
        GROUP BY siswa_id
    ) ks ON ks.siswa_id = ps.siswa_id
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
    ORDER BY ps.id DESC
");

if (!$query_riwayat) {
    die("Gagal mengambil riwayat: " . mysqli_error($koneksi));
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
<title>Catat Pelanggaran</title>

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

/* EVALUASI */
.evaluasi {
    border-left: 5px solid #dc2626;
}

.evaluasi .angka {
    font-size: 27px;
    font-weight: bold;
    color: #dc2626;
}

.evaluasi p {
    color: #6b7280;
    font-size: 13px;
    margin: 5px 0 0;
}

/* FORM */
.form-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.form-group {
    min-width: 0;
}

.form-group.full {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-control,
.form-select {
    width: 100%;
    min-height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
}

textarea.form-control {
    min-height: 85px;
}

.btn-primary {
    background: #2563eb;
    border-color: #2563eb;
    border-radius: 8px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: bold;
}

.btn-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

/* TABEL */
.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    min-width: 950px;
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

.alert {
    font-size: 13px;
    border-radius: 8px;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .form-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

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

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
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
        <li><a href="catat_pelanggaran.php" class="active">Catat Pelanggaran</a></li>
        <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
        <li><a href="rekap_poin.php">Rekap Poin</a></li>
        <li><a href="tindakan.php">Tindakan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
    </ul>

    <a href="../logout.php" class="logout-btn">Logout</a>
</div>

<!-- KONTEN UTAMA -->
<div class="main">

    <div class="page-title">
        <h2>Catat Pelanggaran</h2>
        <p>Catat dan pantau pelanggaran siswa.</p>
    </div>

    <!-- EVALUASI -->
    <div class="panel evaluasi">
        <h3 class="mb-2">Siswa Mencapai Batas Poin</h3>
        <div class="angka"><?= $jumlah_evaluasi; ?> Siswa</div>
        <p>Batas evaluasi sanksi akhir: <?= $batas_poin; ?> poin.</p>
    </div>

    <!-- FORM -->
    <div class="panel">
        <h3>Formulir Catat Pelanggaran</h3>

        <?php if (isset($_GET['berhasil'])): ?>
            <div class="alert alert-success">
                Pelanggaran berhasil disimpan.
            </div>
        <?php endif; ?>

        <?php if ($pesan_error !== ""): ?>
            <div class="alert alert-danger">
                <?= e($pesan_error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="formPelanggaran">
            <div class="form-grid">

                <div class="form-group">
                    <label for="kelas_id">Kelas</label>
                    <select id="kelas_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($kelas = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= (int)$kelas['id']; ?>">
                                <?= e($kelas['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="siswa_id">Siswa</label>
                    <select name="siswa_id" id="siswa_id" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($siswa = mysqli_fetch_assoc($query_siswa)): ?>
                            <option
                                value="<?= (int)$siswa['siswa_id']; ?>"
                                data-kelas="<?= (int)$siswa['kelas_id']; ?>"
                            >
                                <?= e($siswa['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori Pelanggaran</label>
                    <select name="pelanggaran_kategori_id" id="kategori_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($kategori = mysqli_fetch_assoc($query_kategori)): ?>
                            <option value="<?= (int)$kategori['id']; ?>">
                                <?= e($kategori['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_id">Jenis Pelanggaran</label>
                    <select name="jenis_id" id="jenis_id" class="form-select" required disabled>
                        <option value="">-- Pilih Kategori Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        class="form-control"
                        value="<?= date('Y-m-d'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea
                        name="keterangan"
                        id="keterangan"
                        class="form-control"
                        placeholder="Masukkan keterangan pelanggaran"
                    ></textarea>
                </div>

                <div class="form-group full">
                    <button type="submit" name="simpan" value="1" class="btn btn-primary">
                        Simpan Pelanggaran
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- RIWAYAT -->
    <div class="panel">
        <h3>Riwayat Pelanggaran Siswa</h3>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Kategori</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Poin</th>
                        <th>Total Poin</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($query_riwayat) > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($query_riwayat)): ?>

                        <?php
                        $total_poin = (int)$row['total_poin'];

                        if ($total_poin >= 150) {
                            $status = "Evaluasi Sanksi Akhir";
                            $kelas_status = "status-evaluasi";
                        } elseif ($total_poin >= 100) {
                            $status = "Pembinaan Lanjutan";
                            $kelas_status = "status-pembinaan";
                        } elseif ($total_poin >= 50) {
                            $status = "Peringatan";
                            $kelas_status = "status-peringatan";
                        } else {
                            $status = "Aman";
                            $kelas_status = "status-aman";
                        }
                        ?>

                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= e($row['nama_siswa']); ?></td>
                            <td><?= e($row['nama_kelas'] ?? '-'); ?></td>
                            <td><?= e($row['nama_kategori'] ?? '-'); ?></td>
                            <td><?= e($row['nama_jenis'] ?? '-'); ?></td>
                            <td><strong>+<?= (int)$row['poin']; ?> poin</strong></td>
                            <td><strong><?= $total_poin; ?> / <?= $batas_poin; ?></strong></td>
                            <td>
                                <span class="status <?= $kelas_status; ?>">
                                    <?= e($status); ?>
                                </span>
                            </td>
                            <td>
                                <?= !empty($row['tanggal'])
                                    ? date('d-m-Y', strtotime($row['tanggal']))
                                    : '-'; ?>
                            </td>
                            <td><?= e($row['keterangan'] ?? '-'); ?></td>
                        </tr>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            Belum ada data pelanggaran.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const semuaJenis = <?= json_encode($jenis_data, JSON_UNESCAPED_UNICODE); ?>;

// FILTER SISWA BERDASARKAN KELAS
const kelasSelect = document.getElementById('kelas_id');
const siswaSelect = document.getElementById('siswa_id');

kelasSelect.addEventListener('change', function () {
    const kelasId = this.value;

    siswaSelect.value = '';

    Array.from(siswaSelect.options).forEach(function (option) {
        if (option.value === '') {
            option.hidden = false;
        } else {
            option.hidden = option.dataset.kelas !== kelasId;
        }
    });
});

// FILTER JENIS BERDASARKAN KATEGORI
const kategoriSelect = document.getElementById('kategori_id');
const jenisSelect = document.getElementById('jenis_id');

kategoriSelect.addEventListener('change', function () {
    const kategoriId = this.value;

    jenisSelect.innerHTML = '';

    if (kategoriId === '') {
        jenisSelect.disabled = true;
        jenisSelect.innerHTML =
            '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
        return;
    }

    const jenisSesuai = semuaJenis.filter(function (jenis) {
        return String(jenis.kategori_id) === String(kategoriId);
    });

    if (jenisSesuai.length === 0) {
        jenisSelect.disabled = true;
        jenisSelect.innerHTML =
            '<option value="">-- Belum ada jenis pelanggaran --</option>';
        return;
    }

    jenisSelect.disabled = false;
    jenisSelect.innerHTML =
        '<option value="">-- Pilih Jenis Pelanggaran --</option>';

    jenisSesuai.forEach(function (jenis) {
        const option = document.createElement('option');
        option.value = jenis.id;
        option.textContent = jenis.nama + ' (' + jenis.poin + ' poin)';
        jenisSelect.appendChild(option);
    });
});
</script>

</body>
</html>
