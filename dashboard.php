<?php
// dashboard.php
include 'includes/cek_session.php';
?>

<!DOCTYPE html>
<html>
<head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <title>Dashboard - Pelanggaran Siswa</title>

    <style>

        body {
            background-color: #f4f7fb;
        }

        /* Card bisa diklik */
        .dashboard-card {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .dashboard-card:hover {
            color: inherit;
        }

        .dashboard-card .card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .dashboard-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <?php
        include "layout/sidebar.php";
        ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- HEADER -->
            <div class="p-4 mb-4 rounded-4 text-white"
                 style="background: linear-gradient(135deg, #2563eb, #1e40af);">

                <h2 class="fw-bold">
                    👋 Selamat Datang!
                </h2>

                <p class="mb-0">
                    Selamat datang di Dashboard Aplikasi Pelanggaran Siswa.
                    Kelola data siswa, guru, kelas, dan pelanggaran dengan mudah.
                </p>

            </div>


            <!-- CARD -->
            <div class="row">


                <!-- DATA SISWA -->
                <div class="col-md-4 mb-3">

                    <a href="kelola_siswa.php"
                       class="dashboard-card">

                        <div class="card border-0 shadow-sm rounded-4">

                            <div class="card-body p-4">

                                <div class="mb-3"
                                     style="
                                     width:48px;
                                     height:48px;
                                     background:#2563eb;
                                     border-radius:12px;
                                     display:flex;
                                     align-items:center;
                                     justify-content:center;
                                     font-size:25px;
                                     ">

                                    👨‍🎓

                                </div>

                                <h6 class="text-uppercase text-secondary fw-bold">
                                    Data Siswa
                                </h6>

                                <h2 class="fw-bold">
                                    120
                                </h2>

                                <p class="text-secondary mb-0">
                                    Total siswa terdaftar
                                </p>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- DATA GURU -->
                <div class="col-md-4 mb-3">

                    <a href="kelola_guru.php"
                       class="dashboard-card">

                        <div class="card border-0 shadow-sm rounded-4">

                            <div class="card-body p-4">

                                <div class="mb-3"
                                     style="
                                     width:48px;
                                     height:48px;
                                     background:#059669;
                                     border-radius:12px;
                                     display:flex;
                                     align-items:center;
                                     justify-content:center;
                                     font-size:25px;
                                     ">

                                    👨‍🏫

                                </div>

                                <h6 class="text-uppercase text-secondary fw-bold">
                                    Data Guru
                                </h6>

                                <h2 class="fw-bold">
                                    25
                                </h2>

                                <p class="text-secondary mb-0">
                                    Total guru terdaftar
                                </p>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- DATA KELAS -->
                <div class="col-md-4 mb-3">

                    <a href="kelola_kelas.php"
                       class="dashboard-card">

                        <div class="card border-0 shadow-sm rounded-4">

                            <div class="card-body p-4">

                                <div class="mb-3"
                                     style="
                                     width:48px;
                                     height:48px;
                                     background:#f59e0b;
                                     border-radius:12px;
                                     display:flex;
                                     align-items:center;
                                     justify-content:center;
                                     font-size:25px;
                                     ">

                                    🏫

                                </div>

                                <h6 class="text-uppercase text-secondary fw-bold">
                                    Data Kelas
                                </h6>

                                <h2 class="fw-bold">
                                    12
                                </h2>

                                <p class="text-secondary mb-0">
                                    Total kelas tersedia
                                </p>

                            </div>

                        </div>

                    </a>

                </div>

            </div>


            <!-- RINGKASAN -->
            <div class="card border-0 shadow-sm rounded-4 mt-2">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        📊 Ringkasan Sistem
                    </h5>


                    <!-- SISWA -->
                    <a href="kelola_siswa.php"
                       class="text-decoration-none text-dark">

                        <div class="p-3 mb-3 rounded-3"
                             style="background:#f8fafc;">

                            <div class="d-flex align-items-center">

                                <div class="me-3"
                                     style="
                                     width:40px;
                                     height:40px;
                                     background:#e0e7ff;
                                     border-radius:10px;
                                     display:flex;
                                     align-items:center;
                                     justify-content:center;
                                     ">

                                    👨‍🎓

                                </div>

                                <div>

                                    <strong>
                                        Data Siswa
                                    </strong>

                                    <div class="text-secondary small">
                                        120 siswa terdaftar dalam sistem
                                    </div>

                                </div>

                            </div>

                        </div>

                    </a>


                    <!-- GURU -->
                    <a href="kelola_guru.php"
                       class="text-decoration-none text-dark">

                        <div class="p-3 rounded-3"
                             style="background:#f8fafc;">

                            <div class="d-flex align-items-center">

                                <div class="me-3"
                                     style="
                                     width:40px;
                                     height:40px;
                                     background:#e0e7ff;
                                     border-radius:10px;
                                     display:flex;
                                     align-items:center;
                                     justify-content:center;
                                     ">

                                    👨‍🏫

                                </div>

                                <div>

                                    <strong>
                                        Data Guru
                                    </strong>

                                    <div class="text-secondary small">
                                        25 guru terdaftar dalam sistem
                                    </div>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>

            </div>

        </main>

    </div>

</div>

</body>
</html>
