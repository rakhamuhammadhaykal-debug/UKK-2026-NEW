<?php
// proses_login.php

session_start();

include 'config/koneksi.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['pesan_error'] = 'Email dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

$query = mysqli_prepare(
    $koneksi,
    "SELECT id, name, email, password, role
     FROM t_users
     WHERE email = ?
     LIMIT 1"
);

if (!$query) {
    $_SESSION['pesan_error'] = 'Terjadi kesalahan database: ' . mysqli_error($koneksi);
    header('Location: login.php');
    exit;
}

mysqli_stmt_bind_param($query, "s", $email);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

if (!$result) {
    mysqli_stmt_close($query);
    $_SESSION['pesan_error'] = 'Terjadi kesalahan saat mengambil data.';
    header('Location: login.php');
    exit;
}

$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($query);

if (!$data) {
    $_SESSION['pesan_error'] = 'Email tidak ditemukan di tabel t_users.';
    header('Location: login.php');
    exit;
}

if (!password_verify($password, $data['password'])) {
    $_SESSION['pesan_error'] = 'Password salah.';
    header('Location: login.php');
    exit;
}

// Login berhasil
$_SESSION['login'] = true;
$_SESSION['id'] = $data['id'];
$_SESSION['name'] = $data['name'];
$_SESSION['role'] = $data['role'];
$_SESSION['email'] = $data['email'];

// Arahkan berdasarkan role
if ($data['role'] === 'admin') {

    header('Location: dashboard.php');
    exit;

} elseif ($data['role'] === 'guru') {

    header('Location: guru/dashboard_guru.php');
    exit;

} else {

    session_destroy();

    session_start();
    $_SESSION['pesan_error'] = 'Role akun tidak dikenali.';
    header('Location: login.php');
    exit;
}
?>