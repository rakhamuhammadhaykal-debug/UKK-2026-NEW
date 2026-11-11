<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$query = mysqli_query(
    $koneksi,
    "SELECT
        j.id,
        j.nama,
        j.deskripsi,
        j.status_aktif,
        k.nama AS kategori
     FROM t_pelanggaran_jenis j
     INNER JOIN t_pelanggaran_kategori k
        ON j.kategori_id = k.id
     ORDER BY k.nama ASC, j.nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Jenis Pelanggaran</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 30px;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2563eb;
            color: white;
        }

        .aktif {
            color: green;
            font-weight: bold;
        }

        .nonaktif {
            color: red;
            font-weight: bold;
        }

        .aksi a {
            margin-right: 8px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Jenis Pelanggaran</h1>

    <a href="tambah_pelanggaran_jenis.php" class="btn">
        + Tambah Jenis Pelanggaran
    </a>

    <table>

        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Jenis Pelanggaran</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        while ($data = mysqli_fetch_assoc($query)) {
        ?>

        <tr>

            <td><?php echo $no++; ?></td>

            <td>
                <?php echo htmlspecialchars($data['kategori']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($data['nama']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($data['deskripsi'] ?? '-'); ?>
            </td>

            <td>

                <?php if ($data['status_aktif'] == 1) { ?>

                    <span class="aktif">Aktif</span>

                <?php } else { ?>

                    <span class="nonaktif">Tidak Aktif</span>

                <?php } ?>

            </td>

            <td class="aksi">

                <a href="edit_pelanggaran_jenis.php?id=<?php echo $data['id']; ?>">
                    Edit
                </a>

                <a
                    href="hapus_pelanggaran_jenis.php?id=<?php echo $data['id']; ?>"
                    onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')"
                >
                    Hapus
                </a>

            </td>

        </tr>

        <?php } ?>

    </table>

    <br>

    <a href="kelola_pelanggaran_kategori.php">
        ← Kembali ke Kategori Pelanggaran
    </a>

</div>

</body>
</html>