<?php
// edit_guru.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: kelola_guru.php");
    exit;
}

// Ambil data guru
$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_guru WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    echo "<script>
            alert('Data guru tidak ditemukan.');
            window.location.href='kelola_guru.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Guru - Pelanggaran Siswa</title>

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

        /* BUTTON */
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

        /* USER ID */
        .user-id-info {
            color: #6b7280;
            font-size: 13px;
            margin-top: 6px;
        }

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

                <h2>✏️ Edit Guru</h2>

                <p>
                    Perbarui informasi data guru yang sudah terdaftar.
                </p>

            </div>


            <!-- FORM -->
            <div class="form-card">

                <h4 class="form-title">
                    Data Guru
                </h4>

                <p class="form-description">
                    Silakan ubah data guru sesuai kebutuhan.
                </p>


                <div class="info-box">

                    <strong>ID Guru:</strong>
                    <?php echo htmlspecialchars($data['id']); ?>

                </div>


                <form action="proses_edit_guru.php" method="POST">

                    <!-- ID -->
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo htmlspecialchars($data['id']); ?>"
                    >


                    <div class="row">

                        <!-- NIP -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                NIP
                            </label>

                            <input
                                type="text"
                                name="nip"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['nip']); ?>"
                                required
                            >

                        </div>


                        <!-- NAMA -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Nama Guru
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['nama']); ?>"
                                placeholder="Masukkan nama guru"
                                required
                            >

                        </div>

                    </div>


                    <!-- EMAIL -->
                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?php echo htmlspecialchars($data['email']); ?>"
                            placeholder="contoh@email.com"
                            required
                        >

                    </div>


                    <div class="row">

                        <!-- STATUS -->
                        <div class="col-md-6 mb-3">

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


                        <!-- USER ID -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                User ID
                            </label>

                            <input
                                type="number"
                                name="user_id"
                                class="form-control"
                                value="<?php echo htmlspecialchars($data['user_id'] ?? ''); ?>"
                            >

                            <div class="user-id-info">
                                ID pengguna yang terhubung dengan guru.
                            </div>

                        </div>

                    </div>


                    <!-- BUTTON -->
                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="kelola_guru.php"
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