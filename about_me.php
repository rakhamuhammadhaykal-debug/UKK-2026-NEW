<?php
// about_me.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// DATA DIRI
$nama = "Rakha Muhammad Haykal";
$kelas = "XII RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Mancing";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tentang Saya - Pelanggaran Siswa</title>

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
        .about-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
        }

        .about-header h2 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .about-header p {
            margin: 0;
            opacity: 0.9;
        }

        /* CARD DATA DIRI */
        .profile-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
            margin-bottom: 20px;
        }

        .profile-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #e7efff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            margin-bottom: 15px;
        }

        .profile-name {
            font-size: 26px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .profile-role {
            color: #6b7280;
            margin-bottom: 25px;
        }

        /* DATA */
        .data-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px 18px;
            margin-bottom: 12px;
        }

        .data-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .data-value {
            color: #111827;
            font-size: 16px;
            font-weight: 600;
        }

        /* DESKRIPSI */
        .description-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 25px 30px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
        }

        .description-card h4 {
            font-weight: 700;
            margin-bottom: 15px;
        }

        .description-card p {
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 0;
        }

        /* TOMBOL */
        .btn-dashboard {
            display: inline-block;
            margin-top: 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-dashboard:hover {
            background: #1d4ed8;
            color: white;
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
            <div class="about-header">
                <h2>👨‍💻 Tentang Saya</h2>
                <p>Informasi pribadi dan profil pembuat aplikasi.</p>
            </div>

            <!-- DATA DIRI -->
            <div class="profile-card">

                <div class="profile-icon">
                    👨‍💻
                </div>

                <div class="profile-name">
                    <?php echo htmlspecialchars($nama); ?>
                </div>

                <div class="profile-role">
                    Siswa Rekayasa Perangkat Lunak
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="data-box">
                            <div class="data-label">Nama</div>
                            <div class="data-value">
                                <?php echo htmlspecialchars($nama); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="data-box">
                            <div class="data-label">Kelas</div>
                            <div class="data-value">
                                <?php echo htmlspecialchars($kelas); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="data-box">
                            <div class="data-label">Sekolah</div>
                            <div class="data-value">
                                <?php echo htmlspecialchars($sekolah); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="data-box">
                            <div class="data-label">Jurusan</div>
                            <div class="data-value">
                                <?php echo htmlspecialchars($jurusan); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="data-box">
                            <div class="data-label">Hobi</div>
                            <div class="data-value">
                                🎣 <?php echo htmlspecialchars($hobi); ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- DESKRIPSI -->
            <div class="description-card">

                <h4>📖 Deskripsi Diri</h4>

                <p>
                    Saya adalah seorang pelajar yang sedang belajar
                    Rekayasa Perangkat Lunak. Saya senang mempelajari
                    teknologi dan membuat website. Saya ingin terus
                    belajar agar bisa menjadi Web Developer.
                </p>

                <a href="dashboard.php" class="btn-dashboard">
                    ← Kembali ke Dashboard
                </a>

            </div>

        </main>

    </div>
</div>

</body>
</html>