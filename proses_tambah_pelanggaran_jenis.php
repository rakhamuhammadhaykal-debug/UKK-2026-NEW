<?php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$kategori_id = $_POST['kategori_id'];
$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$status_aktif = $_POST['status_aktif'];

$query = mysqli_query(
    $koneksi,
    "INSERT INTO t_pelanggaran_jenis
    (kategori_id, nama, deskripsi, status_aktif)
    VALUES
    ('$kategori_id', '$nama', '$deskripsi', '$status_aktif')"
);

if ($query) {

    header("Location: kelola_pelanggaran_jenis.php");
    exit;

} else {

    echo "Gagal menyimpan data: " . mysqli_error($koneksi);

}

?>