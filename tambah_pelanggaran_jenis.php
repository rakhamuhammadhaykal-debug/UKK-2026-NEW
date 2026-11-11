<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$query_kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Jenis Pelanggaran</title>
</head>

<body>

<h1>Tambah Jenis Pelanggaran</h1>

<form action="proses_tambah_pelanggaran_jenis.php" method="POST">

    <p>
        <label>Kategori Pelanggaran</label><br>

        <select name="kategori_id" required>

            <option value="">
                -- Pilih Kategori --
            </option>

            <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>

                <option value="<?php echo $kategori['id']; ?>">
                    <?php echo htmlspecialchars($kategori['nama']); ?>
                </option>

            <?php } ?>

        </select>
    </p>

    <p>
        <label>Jenis Pelanggaran</label><br>

        <input
            type="text"
            name="nama"
            maxlength="100"
            placeholder="Contoh: Terlambat Masuk"
            required
        >
    </p>

    <p>
        <label>Deskripsi</label><br>

        <textarea
            name="deskripsi"
            rows="4"
            placeholder="Deskripsi jenis pelanggaran"
        ></textarea>
    </p>

    <p>
        <label>Status</label><br>

        <select name="status_aktif">

            <option value="1">
                Aktif
            </option>

            <option value="0">
                Tidak Aktif
            </option>

        </select>
    </p>

    <button type="submit">
        Simpan
    </button>

</form>

<br>

<a href="kelola_pelanggaran_jenis.php">
    ← Kembali
</a>

</body>
</html>