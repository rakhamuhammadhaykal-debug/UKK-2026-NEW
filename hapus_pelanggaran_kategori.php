<?php
// hapus_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}

$id = (int) $_GET['id'];

/* Ambil data terlebih dahulu */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    $_SESSION['pesan_error'] = "Kategori pelanggaran tidak ditemukan.";
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}

/* Hapus data */
$stmt_hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt_hapus, "i", $id);

if (mysqli_stmt_execute($stmt_hapus)) {

    $_SESSION['pesan_sukses'] =
        "Kategori pelanggaran \"" . $data['nama'] . "\" berhasil dihapus.";

} else {

    $_SESSION['pesan_error'] =
        "Kategori pelanggaran gagal dihapus.";
}

header("Location: kelola_pelanggaran_kategori.php");
exit;
?>