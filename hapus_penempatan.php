<?php
// hapus_penempatan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: penempatan_siswa.php");
    exit;
}

// Cek data penempatan
$sql = "SELECT id FROM t_kelas_siswa WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['pesan_error'] = "Data penempatan tidak ditemukan.";
    header("Location: penempatan_siswa.php");
    exit;
}

// Hapus data penempatan
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {

    $_SESSION['pesan_sukses'] = "Data penempatan siswa berhasil dihapus.";

    header("Location: penempatan_siswa.php");
    exit;

} else {

    $_SESSION['pesan_error'] = "Gagal menghapus data penempatan: " . mysqli_error($koneksi);

    header("Location: penempatan_siswa.php");
    exit;
}
?>