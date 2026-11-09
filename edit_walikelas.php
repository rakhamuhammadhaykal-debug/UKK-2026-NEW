<?php
// edit_walikelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('ID wali kelas tidak valid.');
}

/* Ambil data wali kelas */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    exit('Data wali kelas tidak ditemukan.');
}

/* Ambil tahun ajaran */
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     ORDER BY id DESC"
);

/* Ambil kelas */
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     ORDER BY nama ASC"
);

/* Ambil guru */
$guru = mysqli_query(
    $koneksi,
    "SELECT id, nip, nama
     FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Wali Kelas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background: #f4f7fb;
        }

        .main-content {
            padding: 30px;
        }

        .page-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            border-radius: 18px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
        }

        .page-header h2 {
            font-weight: 700;
            margin: 0 0 5px;
        }

        .page-header p {
            margin: 0;
            opacity: .9;
        }

        .form-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 30px;
            max-width: 900px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .07);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 2px solid #d9e2f2;
            border-radius: 10px;
            padding: 11px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .btn-update {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-weight: 600;
        }

        .btn-update:hover {
            background: #1d4ed8;
            color: white;
        }

        .btn-back {
            background: white;
            color: #374151;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 18px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-back:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 main-content">

            <!-- HEADER -->
            <div class="page-header">

                <h2>👨‍🏫 Edit Wali Kelas</h2>

                <p>
                    Perbarui data wali kelas pada sistem.
                </p>

            </div>

            <!-- FORM -->
            <div class="form-card">

                <div class="mb-4">

                    <h4 class="fw-bold">
                        📝 Form Edit Wali Kelas
                    </h4>

                    <small class="text-muted">
                        Ubah data wali kelas sesuai kebutuhan.
                    </small>

                </div>

                <div class="info-box">

                    <strong>💡 Informasi</strong>

                    <br>

                    <small class="text-muted">
                        Pastikan data tahun ajaran, kelas, guru,
                        dan tanggal sudah benar.
                    </small>

                </div>

                <form
                    action="proses_edit_walikelas.php"
                    method="POST"
                >

                    <!-- ID -->
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo htmlspecialchars($data['id']); ?>"
                    >

                    <!-- TAHUN AJARAN -->
                    <div class="mb-3">

                        <label class="form-label">
                            Tahun Ajaran
                        </label>

                        <select
                            name="tahun_ajaran_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Tahun Ajaran --
                            </option>

                            <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>

                                <option
                                    value="<?php echo $ta['id']; ?>"
                                    <?php
                                    echo ($data['tahun_ajaran_id'] == $ta['id'])
                                        ? 'selected'
                                        : '';
                                    ?>
                                >

                                    <?php echo htmlspecialchars($ta['nama']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- KELAS -->
                    <div class="mb-3">

                        <label class="form-label">
                            Kelas
                        </label>

                        <select
                            name="kelas_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Kelas --
                            </option>

                            <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                <option
                                    value="<?php echo $k['id']; ?>"
                                    <?php
                                    echo ($data['kelas_id'] == $k['id'])
                                        ? 'selected'
                                        : '';
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $k['nama'] .
                                        ' - Tingkat ' .
                                        $k['tingkat'] .
                                        ' - ' .
                                        $k['jurusan']
                                    );
                                    ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- GURU -->
                    <div class="mb-3">

                        <label class="form-label">
                            Guru / Wali Kelas
                        </label>

                        <select
                            name="guru_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Guru --
                            </option>

                            <?php while ($g = mysqli_fetch_assoc($guru)) { ?>

                                <option
                                    value="<?php echo $g['id']; ?>"
                                    <?php
                                    echo ($data['guru_id'] == $g['id'])
                                        ? 'selected'
                                        : '';
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $g['nip'] .
                                        ' - ' .
                                        $g['nama']
                                    );
                                    ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- TANGGAL -->
                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['tanggal_mulai']); ?>"
                                required
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>"
                            >

                        </div>

                    </div>

                    <!-- STATUS -->
                    <div class="mb-4">

                        <label class="form-label">
                            Status Aktif
                        </label>

                        <select
                            name="status_aktif"
                            class="form-select"
                            required
                        >

                            <option
                                value="1"
                                <?php
                                echo ($data['status_aktif'] == 1)
                                    ? 'selected'
                                    : '';
                                ?>
                            >
                                Aktif
                            </option>

                            <option
                                value="0"
                                <?php
                                echo ($data['status_aktif'] == 0)
                                    ? 'selected'
                                    : '';
                                ?>
                            >
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn-update"
                        >
                            💾 Update Wali Kelas
                        </button>

                        <a
                            href="kelola_walikelas.php"
                            class="btn-back"
                        >
                            ← Kembali
                        </a>

                    </div>

                </form>

            </div>

        </main>

    </div>

</div>

</body>
</html>