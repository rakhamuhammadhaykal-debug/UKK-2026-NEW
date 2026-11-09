<?php
// edit_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Kategori Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

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
            max-width: 900px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
        }

        .form-control,
        .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 11px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        textarea.form-control {
            min-height: 130px;
            resize: vertical;
        }

        .btn-update {
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-weight: 600;
        }

        .btn-update:hover {
            background-color: #1d4ed8;
            color: white;
        }

        .btn-back {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #f3f4f6;
            color: #111827;
        }

        .info-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 12px;
            padding: 14px 16px;
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
                <h2>✏️ Edit Kategori Pelanggaran</h2>
                <p>Perbarui informasi kategori pelanggaran.</p>
            </div>

            <!-- FORM -->
            <div class="form-card">

                <div class="info-box">
                    <strong>ℹ️ Edit Data</strong><br>
                    Silakan ubah data kategori pelanggaran sesuai kebutuhan.
                </div>

                <form action="proses_edit_pelanggaran_kategori.php" method="POST">

                    <!-- ID -->
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo htmlspecialchars($data['id']); ?>"
                    >

                    <!-- NAMA -->
                    <div class="mb-4">
                        <label class="form-label">
                            Nama Kategori
                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="<?php echo htmlspecialchars($data['nama']); ?>"
                            placeholder="Masukkan nama kategori"
                            required
                        >
                    </div>

                    <!-- DESKRIPSI -->
                    <div class="mb-4">
                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            class="form-control"
                            placeholder="Masukkan deskripsi kategori"
                            required
                        ><?php echo htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
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

                    <!-- BUTTON -->
                    <div class="d-flex gap-2">

                        <a
                            href="kelola_pelanggaran_kategori.php"
                            class="btn-back"
                        >
                            ← Kembali
                        </a>

                        <button
                            type="submit"
                            class="btn-update"
                        >
                            💾 Update Kategori
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>
</div>

</body>
</html>