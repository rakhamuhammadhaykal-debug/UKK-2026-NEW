<?php
// hapus_walikelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil ID
$id = (int) ($_GET['id'] ?? 0);

// Pastikan ID tersedia
if ($id <= 0) {
    header('Location: kelola_walikelas.php');
    exit;
}

// Cek apakah data wali kelas ada
$sql = "SELECT
            w.id,
            w.kelas_id,
            k.nama AS nama_kelas
        FROM t_wali_kelas w
        LEFT JOIN t_kelas k ON w.kelas_id = k.id
        WHERE w.id = ?";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Kalau data tidak ditemukan
if (!$data) {
    header('Location: kelola_walikelas.php');
    exit;
}

// Hapus data wali kelas
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {

    mysqli_stmt_close($hapus);

    // Kembali ke halaman kelola
    header('Location: kelola_walikelas.php');
    exit;

} else {

    echo "Gagal menghapus wali kelas: " . mysqli_error($koneksi);
}
?>