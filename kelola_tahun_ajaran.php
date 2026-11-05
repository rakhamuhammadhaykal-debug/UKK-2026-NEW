<?php
// kelola_tahun_ajaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_tahun_ajaran ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Tahun Ajaran</title>

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

        .table-card {
            background: white;
            border: 2px solid #d9e2f2;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
        }

        /* Jangan sampai muncul scroll horizontal */
        .table-responsive {
            overflow-x: hidden !important;
        }

        #tabelTahunAjaran {
            width: 100% !important;
            table-layout: fixed;
        }

        #tabelTahunAjaran th,
        #tabelTahunAjaran td {
            vertical-align: middle;
            padding: 12px 10px;
            word-wrap: break-word;
        }

        #tabelTahunAjaran th {
            background-color: #212529;
            color: white;
            white-space: nowrap;
        }

        /* Lebar kolom */
        #tabelTahunAjaran th:nth-child(1),
        #tabelTahunAjaran td:nth-child(1) {
            width: 8%;
            text-align: center;
        }

        #tabelTahunAjaran th:nth-child(2),
        #tabelTahunAjaran td:nth-child(2) {
            width: 22%;
        }

        #tabelTahunAjaran th:nth-child(3),
        #tabelTahunAjaran td:nth-child(3) {
            width: 18%;
        }

        #tabelTahunAjaran th:nth-child(4),
        #tabelTahunAjaran td:nth-child(4) {
            width: 18%;
        }

        #tabelTahunAjaran th:nth-child(5),
        #tabelTahunAjaran td:nth-child(5) {
            width: 14%;
            text-align: center;
        }

        #tabelTahunAjaran th:nth-child(6),
        #tabelTahunAjaran td:nth-child(6) {
            width: 20%;
            text-align: center;
        }

        .status-aktif {
            background: #dcfce7;
            color: #15803d;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #b91c1c;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 7px 14px;
            margin-right: 8px;
        }

        .btn-edit:hover {
            background: #d97706;
            color: white;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 7px 14px;
        }

        .btn-delete:hover {
            background: #b91c1c;
            color: white;
        }

        .btn-dashboard {
            border: 1px solid #d1d5db;
            color: #374151;
            border-radius: 10px;
            padding: 9px 15px;
            text-decoration: none;
            font-weight: 600;
            background: white;
        }

        .btn-dashboard:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .btn-add {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 9px 15px;
            font-weight: 600;
        }

        .btn-add:hover {
            background: #1d4ed8;
            color: white;
        }

        /* DataTables */
        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            margin-left: 8px;
        }

        .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 10px;
        }

        .dataTables_info {
            margin-top: 15px;
        }

        .pagination {
            margin-top: 15px !important;
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

                <h2>📅 Kelola Tahun Ajaran</h2>

                <p>
                    Kelola data tahun ajaran yang digunakan dalam sistem.
                </p>

            </div>

            <!-- CARD TABEL -->
            <div class="table-card">

                <!-- JUDUL + TOMBOL -->
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h5 class="fw-bold mb-1">
                            Data Tahun Ajaran
                        </h5>

                        <small class="text-muted">
                            Daftar seluruh tahun ajaran
                        </small>
                    </div>

                    <div>

                        <a href="dashboard.php"
                           class="btn-dashboard me-2">
                            ← Dashboard
                        </a>

                        <a href="tambah_tahun_ajaran.php"
                           class="btn btn-add">
                            + Tambah Tahun Ajaran
                        </a>

                    </div>

                </div>

                <!-- TABEL -->
                <div class="table-responsive">

                    <table id="tabelTahunAjaran"
                           class="table table-bordered table-hover align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Nama Tahun Ajaran</th>

                                <th>Tanggal Mulai</th>

                                <th>Tanggal Selesai</th>

                                <th>Status</th>

                                <th>Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                            <tr>

                                <td>
                                    <?php
                                    echo htmlspecialchars($row['id']);
                                    ?>
                                </td>

                                <td>
                                    <strong>
                                        <?php
                                        echo htmlspecialchars($row['nama']);
                                        ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['tanggal_mulai']
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['tanggal_selesai']
                                    );
                                    ?>
                                </td>

                                <td>

                                    <?php

                                    if ($row['status_aktif'] == 1) {

                                        echo '<span class="status-aktif">
                                                Aktif
                                              </span>';

                                    } else {

                                        echo '<span class="status-nonaktif">
                                                Tidak Aktif
                                              </span>';
                                    }

                                    ?>

                                </td>

                                <td>

                                    <a href="edit_tahun_ajaran.php?id=<?php echo urlencode($row['id']); ?>"
                                       class="btn btn-edit">

                                        Edit

                                    </a>

                                    <a href="hapus_tahun_ajaran.php?id=<?php echo urlencode($row['id']); ?>"
                                       class="btn btn-delete"
                                       onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?');">

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

    $('#tabelTahunAjaran').DataTable({

        paging: true,

        searching: true,

        ordering: true,

        info: true,

        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],

        scrollX: false,

        autoWidth: false,

        order: [[0, 'desc']],

        columnDefs: [

            {
                targets: 5,
                orderable: false,
                searchable: false
            }

        ],

        language: {

            search: "Cari Tahun Ajaran:",

            searchPlaceholder:
                "Cari tahun ajaran...",

            lengthMenu:
                "Tampilkan _MENU_ data",

            info:
                "Menampilkan _START_ sampai _END_ dari _TOTAL_ tahun ajaran",

            infoEmpty:
                "Menampilkan 0 sampai 0 dari 0 tahun ajaran",

            infoFiltered:
                "(disaring dari _MAX_ total tahun ajaran)",

            zeroRecords:
                "Data tahun ajaran tidak ditemukan",

            emptyTable:
                "Belum ada data tahun ajaran",

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