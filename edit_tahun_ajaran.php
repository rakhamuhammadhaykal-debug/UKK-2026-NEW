<?php
// edit_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('ID tahun ajaran tidak valid.');
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    exit('Data tahun ajaran tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Tahun Ajaran - Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fb;
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
            margin-bottom: 5px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .form-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
            max-width: 850px;
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
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
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

        .status-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
        }
    </style>
</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 main-content">

            <!-- HEADER -->
            <div class="page-header">

                <h2>📅 Edit Tahun Ajaran</h2>

                <p>
                    Perbarui informasi tahun ajaran pada sistem.
                </p>

            </div>

            <!-- FORM -->
            <div class="form-card">

                <div class="mb-4">
                    <h4 class="fw-bold mb-1">
                        📝 Form Edit Tahun Ajaran
                    </h4>

                    <small class="text-muted">
                        Silakan ubah data yang diperlukan.
                    </small>
                </div>

                <form action="proses_edit_tahun_ajaran.php" method="POST">

                    <!-- ID -->
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo htmlspecialchars($data['id']); ?>"
                    >

                    <!-- NAMA TAHUN AJARAN -->
                    <div class="mb-3">

                        <label class="form-label">
                            Nama Tahun Ajaran
                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="<?php echo htmlspecialchars($data['nama']); ?>"
                            placeholder="Contoh: 2026/2027"
                            required
                        >

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
                                value="<?php echo htmlspecialchars($data['tanggal_selesai']); ?>"
                                required
                            >

                        </div>

                    </div>

                    <!-- STATUS -->
                    <div class="mb-4">

                        <label class="form-label">
                            Status Aktif
                        </label>

                        <div class="status-box">

                            <select
                                name="status_aktif"
                                class="form-select"
                                required
                            >

                                <option
                                    value="1"
                                    <?php
                                    if ($data['status_aktif'] == 1) {
                                        echo 'selected';
                                    }
                                    ?>
                                >
                                    Aktif
                                </option>

                                <option
                                    value="0"
                                    <?php
                                    if ($data['status_aktif'] == 0) {
                                        echo 'selected';
                                    }
                                    ?>
                                >
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-update"
                        >
                            💾 Update Tahun Ajaran
                        </button>

                        <a
                            href="kelola_tahun_ajaran.php"
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