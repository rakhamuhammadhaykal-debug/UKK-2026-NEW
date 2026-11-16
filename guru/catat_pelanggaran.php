
<?php
// guru/catat_pelanggaran.php

session_start();
include '../config/koneksi.php';

// ===============================
// CEK LOGIN GURU
// ===============================
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['pesan_error'] = 'Silakan login terlebih dahulu sebagai guru.';
    header('Location: ../login.php');
    exit;
}

$nama_guru = $_SESSION['name'] ?? 'Guru';

$pesan = '';
$error = '';


// ======================================================
// PROSES SIMPAN PELANGGARAN
// ======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $pelanggaran_kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $jenis_id = (int) ($_POST['jenis_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $poin = (int) ($_POST['poin'] ?? 0);
    $tindakan = trim($_POST['tindakan'] ?? '');

    // VALIDASI
    if ($siswa_id <= 0) {
        $error = 'Silakan pilih siswa.';
    } elseif ($pelanggaran_kategori_id <= 0) {
        $error = 'Silakan pilih kategori pelanggaran.';
    } elseif ($jenis_id <= 0) {
        $error = 'Silakan pilih jenis pelanggaran.';
    } else {

        // CEK JENIS SESUAI KATEGORI
        $stmt = $koneksi->prepare("
            SELECT id
            FROM t_pelanggaran_jenis
            WHERE id = ?
            AND kategori_id = ?
            AND status_aktif = 1
            LIMIT 1
        ");

        $stmt->bind_param("ii", $jenis_id, $pelanggaran_kategori_id);
        $stmt->execute();
        $cekJenis = $stmt->get_result();

        if ($cekJenis->num_rows === 0) {
            $error = 'Jenis pelanggaran tidak sesuai dengan kategori.';
        }

        $stmt->close();

        // AMBIL DATA SISWA
        if ($error === '') {
            $stmt = $koneksi->prepare("
                SELECT nama
                FROM t_siswa
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->bind_param("i", $siswa_id);
            $stmt->execute();
            $hasilSiswa = $stmt->get_result();

            if ($hasilSiswa->num_rows === 0) {
                $error = 'Data siswa tidak ditemukan.';
            } else {
                $dataSiswa = $hasilSiswa->fetch_assoc();
                $nama_siswa = $dataSiswa['nama'];
            }

            $stmt->close();
        }

        // AMBIL NAMA KATEGORI
        if ($error === '') {
            $stmt = $koneksi->prepare("
                SELECT nama
                FROM t_pelanggaran_kategori
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->bind_param("i", $pelanggaran_kategori_id);
            $stmt->execute();
            $hasilKategori = $stmt->get_result();

            if ($hasilKategori->num_rows === 0) {
                $error = 'Kategori pelanggaran tidak ditemukan.';
            } else {
                $dataKategori = $hasilKategori->fetch_assoc();
                $nama_pelanggaran = $dataKategori['nama'];
            }

            $stmt->close();
        }

        // AMBIL KELAS DAN TAHUN AJARAN SISWA
        if ($error === '') {
            $stmt = $koneksi->prepare("
                SELECT
                    ks.kelas_id,
                    ks.tahun_ajaran_id,
                    k.nama AS nama_kelas
                FROM t_kelas_siswa ks
                LEFT JOIN t_kelas k
                    ON k.id = ks.kelas_id
                WHERE ks.siswa_id = ?
                AND ks.status_aktif = 1
                ORDER BY ks.id DESC
                LIMIT 1
            ");

            $stmt->bind_param("i", $siswa_id);
            $stmt->execute();
            $hasilKelas = $stmt->get_result();

            if ($hasilKelas->num_rows === 0) {
                $error = 'Siswa belum memiliki kelas aktif.';
            } else {
                $dataKelas = $hasilKelas->fetch_assoc();

                $kelas_id = (int) $dataKelas['kelas_id'];
                $tahun_ajaran_id = (int) $dataKelas['tahun_ajaran_id'];
                $nama_kelas = $dataKelas['nama_kelas'];
            }

            $stmt->close();
        }

        // SIMPAN DATA PELANGGARAN
        if ($error === '') {
            $stmt = $koneksi->prepare("
                INSERT INTO t_pelanggaran_siswa
                (
                    tahun_ajaran_id,
                    siswa_id,
                    nama_siswa,
                    kelas_id,
                    nama_kelas,
                    pelanggaran_kategori_id,
                    jenis_id,
                    nama_pelanggaran,
                    tanggal,
                    keterangan,
                    poin,
                    tindakan
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iisisiisssis",
                $tahun_ajaran_id,
                $siswa_id,
                $nama_siswa,
                $kelas_id,
                $nama_kelas,
                $pelanggaran_kategori_id,
                $jenis_id,
                $nama_pelanggaran,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan
            );

            if ($stmt->execute()) {
                $pesan = 'Pelanggaran berhasil disimpan.';
            } else {
                $error = 'Gagal menyimpan pelanggaran: ' . $stmt->error;
            }

            $stmt->close();
        }
    }
}


// ======================================================
// DATA KELAS
// ======================================================
$kelas = [];

$queryKelas = $koneksi->query("
    SELECT
        id,
        nama,
        tingkat,
        jurusan
    FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY tingkat ASC, nama ASC
");

if ($queryKelas) {
    while ($row = $queryKelas->fetch_assoc()) {
        $kelas[] = $row;
    }
}


// ======================================================
// DATA SISWA + KELAS
// PERBAIKAN: AMBIL SEMUA SISWA DAN KELAS TERBARU
// ======================================================
$siswa = [];

$querySiswa = $koneksi->query("
    SELECT
        s.id,
        s.nis,
        s.nama,
        ks.kelas_id
    FROM t_siswa s
    LEFT JOIN t_kelas_siswa ks
        ON ks.id = (
            SELECT ks2.id
            FROM t_kelas_siswa ks2
            WHERE ks2.siswa_id = s.id
            ORDER BY
                (ks2.status_aktif = 1) DESC,
                ks2.id DESC
            LIMIT 1
        )
    ORDER BY s.nama ASC
");

if (!$querySiswa) {
    die("Gagal mengambil data siswa: " . $koneksi->error);
}

while ($row = $querySiswa->fetch_assoc()) {
    $siswa[] = $row;
}


// ======================================================
// DATA KATEGORI
// ======================================================
$kategori = [];

$queryKategori = $koneksi->query("
    SELECT
        id,
        nama
    FROM t_pelanggaran_kategori
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if ($queryKategori) {
    while ($row = $queryKategori->fetch_assoc()) {
        $kategori[] = $row;
    }
}


// ======================================================
// DATA JENIS
// ======================================================
$jenis = [];

$queryJenis = $koneksi->query("
    SELECT
        id,
        kategori_id,
        nama
    FROM t_pelanggaran_jenis
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if ($queryJenis) {
    while ($row = $queryJenis->fetch_assoc()) {
        $jenis[] = $row;
    }
}


// ======================================================
// REKAP PELANGGARAN
// ======================================================
$rekap = [];

$queryRekap = $koneksi->query("
    SELECT
        p.*,
        k.nama AS nama_kategori,
        j.nama AS nama_jenis
    FROM t_pelanggaran_siswa p
    LEFT JOIN t_pelanggaran_kategori k
        ON k.id = p.pelanggaran_kategori_id
    LEFT JOIN t_pelanggaran_jenis j
        ON j.id = p.jenis_id
    ORDER BY p.id DESC
");

if ($queryRekap) {
    while ($row = $queryRekap->fetch_assoc()) {
        $rekap[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Catat Pelanggaran - Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

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

        /* CARD */

        .card {
            background: white;
            padding: 22px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: none;
            min-width: 0;
            margin-bottom: 22px;
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 18px;
        }

        /* FORM */

        .form-label {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-control,
        .form-select {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .10);
        }

        textarea.form-control {
            min-height: 78px;
            resize: vertical;
        }

        .btn-simpan {
            width: 100%;
            background: #2563eb;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        .btn-simpan:hover {
            background: #1d4ed8;
        }

        /* ALERT */

        .alert {
            font-size: 13px;
            border-radius: 8px;
        }

        /* TABEL */

        .table-wrapper {
            overflow-x: auto;
        }

        .table {
            margin: 0;
            font-size: 12px;
            white-space: nowrap;
        }

        .table thead th {
            background: #f9fafb;
            color: #374151;
            font-weight: bold;
            border-bottom: 1px solid #e5e7eb;
            padding: 11px 10px;
        }

        .table tbody td {
            padding: 11px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
        }

        .poin-badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 20px;
            background: #fee2e2;
            color: #dc2626;
            font-size: 11px;
            font-weight: bold;
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

            .form-row {
                display: block !important;
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

            <li>
                <a href="dashboard_guru.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a
                    href="catat_pelanggaran.php"
                    class="active"
                >
                    Catat Pelanggaran
                </a>
            </li>

            <li>
                <a href="data_pelanggaran.php">
                    Data Pelanggaran
                </a>
            </li>

            <li>
                <a href="rekap_poin.php">
                    Rekap Poin
                </a>
            </li>

            <li>
                <a href="tindakan.php">
                    Tindakan
                </a>
            </li>

            <li>
                <a href="riwayat.php">
                    Riwayat
                </a>
            </li>

        </ul>

        <a
            href="../logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </div>

    <!-- KONTEN UTAMA -->

    <div class="main-content">

        <!-- JUDUL -->

        <div class="page-title">

            <h2>
                Catat Pelanggaran
            </h2>

            <p>
                Catat pelanggaran siswa berdasarkan kelas, kategori, dan jenis pelanggaran.
            </p>

        </div>

        <!-- PESAN BERHASIL -->

        <?php if ($pesan !== ''): ?>

            <div class="alert alert-success">
                <?= htmlspecialchars($pesan); ?>
            </div>

        <?php endif; ?>

        <!-- PESAN ERROR -->

        <?php if ($error !== ''): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <!-- FORM PENCATATAN -->

        <div class="card">

            <div class="card-title">
                Form Pencatatan Pelanggaran
            </div>

            <form method="POST">

                <div class="row g-3">

                    <!-- KELAS -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Kelas
                        </label>

                        <select
                            name="kelas_filter"
                            id="kelas_filter"
                            class="form-select"
                        >

                            <option value="">
                                -- Pilih Kelas --
                            </option>

                            <?php foreach ($kelas as $k): ?>

                                <option value="<?= $k['id']; ?>">

                                    <?= htmlspecialchars($k['nama']); ?>

                                    <?php if (!empty($k['tingkat'])): ?>
                                        - <?= htmlspecialchars($k['tingkat']); ?>
                                    <?php endif; ?>

                                    <?php if (!empty($k['jurusan'])): ?>
                                        <?= htmlspecialchars($k['jurusan']); ?>
                                    <?php endif; ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- SISWA -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Siswa
                        </label>

                        <select
                            name="siswa_id"
                            id="siswa_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Siswa --
                            </option>

                            <?php foreach ($siswa as $s): ?>

                                <option
                                    value="<?= $s['id']; ?>"
                                    data-kelas="<?= $s['kelas_id']; ?>"
                                >

                                    <?= htmlspecialchars($s['nama']); ?>

                                    <?php if (!empty($s['nis'])): ?>
                                        - <?= htmlspecialchars($s['nis']); ?>
                                    <?php endif; ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- KATEGORI -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Kategori Pelanggaran
                        </label>

                        <select
                            name="pelanggaran_kategori_id"
                            id="pelanggaran_kategori_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            <?php foreach ($kategori as $k): ?>

                                <option value="<?= $k['id']; ?>">
                                    <?= htmlspecialchars($k['nama']); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- JENIS -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Jenis Pelanggaran
                        </label>

                        <select
                            name="jenis_id"
                            id="jenis_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori Terlebih Dahulu --
                            </option>

                            <?php foreach ($jenis as $j): ?>

                                <option
                                    value="<?= $j['id']; ?>"
                                    data-kategori="<?= $j['kategori_id']; ?>"
                                >
                                    <?= htmlspecialchars($j['nama']); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- TANGGAL -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= date('Y-m-d'); ?>"
                            required
                        >

                    </div>

                    <!-- POIN -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Poin Pelanggaran
                        </label>

                        <input
                            type="number"
                            name="poin"
                            class="form-control"
                            value="0"
                            min="0"
                            required
                        >

                    </div>

                    <!-- KETERANGAN -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="form-control"
                            placeholder="Masukkan keterangan pelanggaran..."
                        ></textarea>

                    </div>

                    <!-- TINDAKAN -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Tindakan
                        </label>

                        <textarea
                            name="tindakan"
                            class="form-control"
                            placeholder="Masukkan tindakan yang diberikan..."
                        ></textarea>

                    </div>

                    <!-- SIMPAN -->

                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn-simpan"
                        >
                            + Simpan Pelanggaran
                        </button>

                    </div>

                </div>

            </form>

        </div>

        <!-- REKAP PELANGGARAN -->

        <div class="card">

            <div class="card-title">
                Rekap Pelanggaran
            </div>

            <div class="table-wrapper">

                <table class="table">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Poin</th>
                            <th>Tindakan</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($rekap) > 0): ?>

                            <?php $no = 1; ?>

                            <?php foreach ($rekap as $r): ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($r['tanggal']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($r['nama_siswa']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $r['nama_kelas'] ?? '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $r['nama_kategori']
                                            ?? $r['nama_pelanggaran']
                                            ?? '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $r['nama_jenis']
                                            ?? '-'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $r['keterangan']
                                            ?? '-'
                                        ); ?>
                                    </td>

                                    <td>

                                        <span class="poin-badge">
                                            <?= (int) $r['poin']; ?> poin
                                        </span>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $r['tindakan']
                                            ?? '-'
                                        ); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted"
                                >
                                    Belum ada data pelanggaran.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- JAVASCRIPT -->

    <script>

        // ==================================================
        // FILTER SISWA BERDASARKAN KELAS
        // ==================================================

        const kelasFilter =
            document.getElementById('kelas_filter');

        const siswaSelect =
            document.getElementById('siswa_id');

        function filterSiswa() {

            const kelasId = kelasFilter.value;

            siswaSelect.value = '';

            const options =
                siswaSelect.querySelectorAll('option');

            options.forEach(function (option) {

                if (option.value === '') {
                    option.hidden = false;
                    return;
                }

                if (kelasId === '') {
                    option.hidden = false;
                } else {
                    option.hidden = (
                        option.dataset.kelas !== kelasId
                    );
                }

            });
        }

        kelasFilter.addEventListener('change', filterSiswa);
        filterSiswa();


        // ==================================================
        // FILTER JENIS BERDASARKAN KATEGORI
        // ==================================================

        const kategoriSelect =
            document.getElementById(
                'pelanggaran_kategori_id'
            );

        const jenisSelect =
            document.getElementById('jenis_id');

        kategoriSelect.addEventListener('change', function () {

            const kategoriId = this.value;

            jenisSelect.value = '';

            const options =
                jenisSelect.querySelectorAll('option');

            options.forEach(function (option) {

                if (option.value === '') {

                    if (kategoriId === '') {
                        option.textContent =
                            '-- Pilih Kategori Terlebih Dahulu --';
                    } else {
                        option.textContent =
                            '-- Pilih Jenis Pelanggaran --';
                    }

                    option.style.display = '';
                    return;
                }

                if (kategoriId === '') {
                    option.style.display = 'none';
                } else {

                    if (
                        option.dataset.kategori === kategoriId
                    ) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }

                }

            });

        });

    </script>

</body>

</html>
