<?php
// hapus_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Cek apakah ID tersedia
if (!isset($_GET['id'])) {
    header('Location: kelola_siswa.php');
    exit;
}

// Ambil ID siswa
$id = (int) $_GET['id'];

// Cek apakah data siswa ada
$cek = mysqli_query(
    $koneksi,
    "SELECT id FROM t_siswa WHERE id = $id"
);

if (!$cek || mysqli_num_rows($cek) == 0) {
    header('Location: kelola_siswa.php');
    exit;
}

// Hapus data siswa
$sql = "DELETE FROM t_siswa WHERE id = $id";

if (mysqli_query($koneksi, $sql)) {

    // Berhasil dihapus
    header('Location: kelola_siswa.php');
    exit;

} else {

    // Kalau gagal menghapus
    echo "Data siswa gagal dihapus: " . mysqli_error($koneksi);
}
?>