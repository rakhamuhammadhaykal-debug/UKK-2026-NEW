
<?php
// kelola_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_siswa ORDER BY nama ASC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

    <style>
        .dataTables_wrapper {
            width: 100%;
        }

        table.dataTable thead th {
            white-space: nowrap;
        }

        table.dataTable tbody td {
            vertical-align: middle;
        }

        .btn-sm {
            margin: 2px;
        }

        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-3">Kelola Siswa</h1>

            <p>
                <a href="dashboard.php" class="btn btn-secondary btn-sm">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_siswa.php" class="btn btn-primary btn-sm">
                    + Tambah Siswa
                </a>
            </p>

            <div class="table-container">

                <h5 class="mb-3">Data Siswa</h5>

                <div class="table-responsive">

                    <table id="tabelSiswa"
                        class="table table-striped table-bordered table-hover align-middle"
                        style="width:100%">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Status Aktif</th>
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
                                    <?php echo htmlspecialchars($row['nis']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['nisn']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                </td>

                                <td>
                                    <?php
                                    if ($row['jenis_kelamin'] == 'L') {
                                        echo "Laki-laki";
                                    } elseif ($row['jenis_kelamin'] == 'P') {
                                        echo "Perempuan";
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['tanggal_lahir']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['alamat']); ?>
                                </td>

                                <td>
                                    <?php
                                    if ($row['status_aktif'] == 1) {
                                        echo '<span class="badge bg-success">Aktif</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Tidak Aktif</span>';
                                    }
                                    ?>
                                </td>

                                <td class="text-nowrap">

                                    <a href="edit_siswa.php?id=<?php echo urlencode($row['id']); ?>"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <a href="hapus_siswa.php?id=<?php echo urlencode($row['id']); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus data siswa ini?');">
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

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelSiswa').DataTable({

        paging: true,
        searching: true,
        ordering: true,
        info: true,

        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],

        scrollX: true,

        order: [[3, 'asc']],

        columnDefs: [
            {
                targets: 8,
                orderable: false,
                searchable: false
            }
        ],

        language: {
            search: "Cari Siswa:",
            searchPlaceholder: "Masukkan nama atau NIS...",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",

            infoEmpty: "Menampilkan 0 sampai 0 dari 0 siswa",

            infoFiltered: "(disaring dari _MAX_ total siswa)",

            zeroRecords: "Data siswa tidak ditemukan",

            emptyTable: "Belum ada data siswa",

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
