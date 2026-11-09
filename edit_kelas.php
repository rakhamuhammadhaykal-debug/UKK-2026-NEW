<?php
// edit_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: kelola_kelas.php");
    exit;
}

// Ambil data kelas
$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_kelas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    echo "<script>
            alert('Data kelas tidak ditemukan.');
            window.location.href='kelola_kelas.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Kelas - Pelanggaran Siswa</title>

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

        /* CARD */
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

        .form-description {
            color: #6b7280;
            margin-bottom: 25px;
        }

        /* LABEL */
        .form-label {
            font-weight: 600;
            color: #374151;
        }

        /* INPUT */
        .form-control,
        .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 13px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* INFO */
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        /* BUTTON UPDATE */
        .btn-update {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-update:hover {
            background: #1d4ed8;
            color: white;
        }

        /* BUTTON BACK */
        .btn-back {
            background: white;
            color: #374151;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #f3f4f6;
            color: #111827;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            .main-content {
                padding: 15px;
            }

            .form-card {
                padding: 20px;
            }

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

                <h2>🏫 Edit Kelas</h2>

                <p>
                    Perbarui informasi kelas yang sudah terdaftar.
                </p>

            </div>


            <!-- FORM CARD -->
            <div class="form-card">

                <h4 class="form-title">
                    Data Kelas
                </h4>

                <p class="form-description">
                    Silakan ubah data kelas sesuai kebutuhan.
                </p>


                <!-- INFO ID -->
                <div class="info-box">

                    <strong>ID Kelas:</strong>

                    <?php
                    echo htmlspecialchars($data['id']);
                    ?>

                </div>


                <form action="proses_edit_kelas.php" method="POST">


                    <!-- ID -->
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo htmlspecialchars($data['id']); ?>"
                    >


                    <div class="row">


                        <!-- NAMA KELAS -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Nama Kelas
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['nama']); ?>"
                                placeholder="Contoh: RPL 1"
                                required
                            >

                        </div>


                        <!-- TINGKAT -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Tingkat
                            </label>

                            <input
                                type="text"
                                name="tingkat"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['tingkat']); ?>"
                                placeholder="Contoh: XI"
                                required
                            >

                        </div>

                    </div>


                    <!-- JURUSAN -->
                    <div class="mb-3">

                        <label class="form-label">
                            Jurusan
                        </label>

                        <input
                            type="text"
                            name="jurusan"
                            class="form-control"
                            value="<?php echo htmlspecialchars($data['jurusan']); ?>"
                            placeholder="Contoh: RPL"
                            required
                        >

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


                    <!-- BUTTON -->
                    <div class="d-flex gap-2">

                        <a
                            href="kelola_kelas.php"
                            class="btn-back"
                        >
                            ← Kembali
                        </a>

                        <button
                            type="submit"
                            class="btn-update"
                        >
                            💾 Simpan Perubahan
                        </button>

                    </div>


                </form>

            </div>

        </main>

    </div>

</div>

</body>
</html>