<?php
// proses_tambah_guru.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data dari form
$nip = $_POST['nip'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$status_aktif = $_POST['status_aktif'];

// Cari ID user dari session
$user_id = null;

if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
}

// Kalau session menyimpan email, cari ID user berdasarkan email
if (!$user_id && isset($_SESSION['email'])) {

    $email_user = mysqli_real_escape_string(
        $koneksi,
        $_SESSION['email']
    );

    $cek_user = mysqli_query(
        $koneksi,
        "SELECT id FROM t_users WHERE email = '$email_user' LIMIT 1"
    );

    if ($cek_user && mysqli_num_rows($cek_user) > 0) {
        $data_user = mysqli_fetch_assoc($cek_user);
        $user_id = $data_user['id'];
    }
}

// Kalau ID user tetap tidak ditemukan
if (!$user_id) {
    die("ID user tidak ditemukan dalam session. Silakan login kembali.");
}

// Pastikan user memang ada di t_users
$cek_user = mysqli_query(
    $koneksi,
    "SELECT id FROM t_users WHERE id = '$user_id'"
);

if (!$cek_user || mysqli_num_rows($cek_user) == 0) {
    die("User tidak ditemukan di tabel t_users.");
}

// Simpan data guru
$sql = "INSERT INTO t_guru
        (user_id, nip, nama, email, status_aktif)
        VALUES
        ('$user_id', '$nip', '$nama', '$email', '$status_aktif')";

$hasil = mysqli_query($koneksi, $sql);

if ($hasil) {

    header("Location: kelola_guru.php");
    exit;

} else {

    echo "Gagal menambahkan data guru: " . mysqli_error($koneksi);
}
?>