<?php
// tambah_kelas.php
include 'includes/cek_session.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fb;
        }

        .main-content {
            padding: 30px;
        }

        /* HEADER */
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

        /* FORM CARD */
        .form-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
        }

        .form-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .form-subtitle {
            color: #6b7280;
            margin-bottom: 25px;
        }

        /* LABEL */
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        /* INPUT */
        .form-control,
        .form-select {
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 13px;
            min-height: 45px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* BUTTON SIMPAN */
        .btn-simpan {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-weight: 600;
        }

        .btn-simpan:hover {
            background: #1d4ed8;
            color: white;
        }

        /* BUTTON KEMBALI */
        .btn-kembali {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-kembali:hover {
            background: #f3f4f6;
            color: #111827;
        }

        /* PEMBATAS */
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
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
                <h2>🏫 Tambah Kelas</h2>
                <p>Tambahkan data kelas baru ke dalam sistem.</p>
            </div>

            <!-- FORM -->
            <div class="form-card">

                <h4 class="form-title">Form Data Kelas</h4>

                <p class="form-subtitle">
                    Isi data kelas dengan lengkap dan benar.
                </p>

                <div class="section-title">
                    📋 Informasi Kelas
                </div>

                <form action="proses_tambah_kelas.php" method="POST">

                    <div class="row">

                        <!-- NAMA KELAS -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kelas</label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Contoh: X IPA 1"
                                required
                            >
                        </div>

                        <!-- TINGKAT -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tingkat</label>

                            <input
                                type="text"
                                name="tingkat"
                                class="form-control"
                                placeholder="Contoh: X"
                                required
                            >
                        </div>

                        <!-- JURUSAN -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan</label>

                            <input
                                type="text"
                                name="jurusan"
                                class="form-control"
                                placeholder="Contoh: IPA"
                                required
                            >
                        </div>

                        <!-- STATUS -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Aktif</label>

                            <select
                                name="status_aktif"
                                class="form-select"
                                required
                            >
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a href="kelola_kelas.php" class="btn-kembali">
                            ← Kembali
                        </a>

                        <button type="submit" class="btn-simpan">
                            💾 Simpan Kelas
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>

</div>

</body>
</html>