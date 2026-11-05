<?php

// kelola_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Kategori Pelanggaran</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css"
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

        /* CARD TABEL */
        .table-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
        }

        /* TABEL */
        #tabelKategori {
            width: 100% !important;
            margin: 0 !important;
            table-layout: fixed;
        }

        #tabelKategori thead th {
            background-color: #212529;
            color: white;
            font-size: 13px;
            font-weight: 600;
            padding: 13px 10px;
            white-space: nowrap;
        }

        #tabelKategori tbody td {
            font-size: 13px;
            padding: 12px 10px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        #tabelKategori tbody tr:hover {
            background-color: #f8fafc;
        }

        /* LEBAR KOLOM */
        #tabelKategori th:nth-child(1),
        #tabelKategori td:nth-child(1) {
            width: 6%;
            text-align: center;
        }

        #tabelKategori th:nth-child(2),
        #tabelKategori td:nth-child(2) {
            width: 15%;
        }

        #tabelKategori th:nth-child(3),
        #tabelKategori td:nth-child(3) {
            width: 22%;
        }

        #tabelKategori th:nth-child(4),
        #tabelKategori td:nth-child(4) {
            width: 11%;
            text-align: center;
        }

        #tabelKategori th:nth-child(5),
        #tabelKategori td:nth-child(5) {
            width: 14%;
            text-align: center;
        }

        #tabelKategori th:nth-child(6),
        #tabelKategori td:nth-child(6) {
            width: 14%;
            text-align: center;
        }

        #tabelKategori th:nth-child(7),
        #tabelKategori td:nth-child(7) {
            width: 18%;
            text-align: center;
        }

        /* STATUS */
        .status-aktif {
            background-color: #dcfce7;
            color: #15803d;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-nonaktif {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* TOMBOL */
        .btn-edit {
            background-color: #f59e0b;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 7px 13px;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            margin-right: 8px;
        }

        .btn-edit:hover {
            background-color: #d97706;
            color: white;
        }

        .btn-delete {
            background-color: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 7px 13px;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-delete:hover {
            background-color: #b91c1c;
            color: white;
        }

        .btn-dashboard {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 9px 15px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-dashboard:hover {
            background-color: #f3f4f6;
            color: #111827;
        }

        .btn-add {
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 9px 15px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-add:hover {
            background-color: #1d4ed8;
            color: white;
        }

        /* DATATABLES */
        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 7px 10px !important;
            margin-left: 8px !important;
        }

        .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            padding: 5px 8px;
        }

        .dataTables_info {
            margin-top: 15px;
        }

        .dataTables_paginate {
            margin-top: 15px;
        }

        /* HILANGKAN SCROLL HORIZONTAL */
        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {

            #tabelKategori {
                font-size: 12px;
            }

            #tabelKategori thead th,
            #tabelKategori tbody td {
                padding: 8px 6px;
            }

            .btn-edit,
            .btn-delete {
                padding: 6px 9px;
                font-size: 12px;
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

                <h2>⚠️ Kelola Kategori Pelanggaran</h2>

                <p>
                    Kelola daftar kategori pelanggaran siswa.
                </p>

            </div>

            <!-- CARD -->
            <div class="table-card">

                <!-- JUDUL + TOMBOL -->
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h5 class="fw-bold mb-1">
                            📋 Data Kategori Pelanggaran
                        </h5>

                        <small class="text-muted">
                            Daftar kategori pelanggaran yang tersedia
                        </small>

                    </div>

                    <div>

                        <a href="dashboard.php"
                           class="btn-dashboard me-2">
                            ← Dashboard
                        </a>

                        <a href="tambah_pelanggaran_kategori.php"
                           class="btn-add">
                            + Tambah Kategori
                        </a>

                    </div>

                </div>

                <!-- TABEL -->
                <div class="table-responsive">

                    <table id="tabelKategori"
                           class="table table-bordered table-hover align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Nama Kategori</th>

                                <th>Deskripsi</th>

                                <th>Status</th>

                                <th>Dibuat</th>

                                <th>Diubah</th>

                                <th>Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars($row['id']); ?>
                                </td>

                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($row['nama']); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['deskripsi'] ?? ''); ?>
                                </td>

                                <td>

                                    <?php if ($row['status_aktif'] == 1) { ?>

                                        <span class="status-aktif">
                                            Aktif
                                        </span>

                                    <?php } else { ?>

                                        <span class="status-nonaktif">
                                            Tidak Aktif
                                        </span>

                                    <?php } ?>

                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['created_at']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['updated_at']); ?>
                                </td>

                                <!-- AKSI -->
                                <td class="text-center">

                                    <a href="edit_pelanggaran_kategori.php?id=<?php echo urlencode($row['id']); ?>"
                                       class="btn-edit">
                                        Edit
                                    </a>

                                    <a href="hapus_pelanggaran_kategori.php?id=<?php echo urlencode($row['id']); ?>"
                                       class="btn-delete"
                                       onclick="return confirm('Yakin ingin menghapus kategori pelanggaran ini?');">
                                        Hapus
                                    </a>

                                </td>

                            </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </main>

    </div>

</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>


<script>

$(document).ready(function () {

    $('#tabelKategori').DataTable({

        paging: true,

        searching: true,

        ordering: true,

        info: true,

        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],

        /* PENTING:
           Jangan pakai scrollX.
           Supaya tidak muncul garis abu-abu untuk geser. */

        autoWidth: false,

        order: [
            [0, 'desc']
        ],

        columnDefs: [

            {
                targets: 6,
                orderable: false,
                searchable: false
            }

        ],

        language: {

            search: "Cari:",

            searchPlaceholder:
                "Cari kategori pelanggaran...",

            lengthMenu:
                "Tampilkan _MENU_ data",

            info:
                "Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori",

            infoEmpty:
                "Menampilkan 0 sampai 0 dari 0 kategori",

            infoFiltered:
                "(disaring dari _MAX_ total kategori)",

            zeroRecords:
                "Kategori pelanggaran tidak ditemukan",

            emptyTable:
                "Belum ada kategori pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        }

    });

});

</script>

</body>
</html>