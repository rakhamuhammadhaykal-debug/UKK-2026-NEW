<?php
// hapus_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Cek apakah ID tersedia
if (!isset($_GET['id'])) {
    header('Location: kelola_kelas.php');
    exit;
}

// Ambil ID kelas
$id = (int) $_GET['id'];

// Cek apakah data kelas ada
$cek = mysqli_query(
    $koneksi,
    "SELECT id FROM t_kelas WHERE id = $id"
);

if (!$cek || mysqli_num_rows($cek) == 0) {
    header('Location: kelola_kelas.php');
    exit;
}

// Hapus data kelas
$sql = "DELETE FROM t_kelas WHERE id = $id";

if (mysqli_query($koneksi, $sql)) {

    // Berhasil dihapus
    header('Location: kelola_kelas.php');
    exit;

} else {

    echo "Data kelas gagal dihapus: " . mysqli_error($koneksi);
}
?>