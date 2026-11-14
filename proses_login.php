<?php
// proses_login.php

session_start();

include 'config/koneksi.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($koneksi, 'SELECT id, name, email, password, role FROM t_users WHERE email = ? LIMIT 1');

if (!$stmt) {
    $_SESSION['pesan_error'] = 'Terjadi kesalahan pada database.';
    header('Location: login.php');
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if (!$hasil) {
    mysqli_stmt_close($stmt);
    $_SESSION['pesan_error'] = 'Terjadi kesalahan pada database.';
    header('Location: login.php');
    exit;
}

if ($data = mysqli_fetch_assoc($hasil)) {
    mysqli_stmt_close($stmt);

    if (password_verify($password, $data['password'])) {
        // Session login
        $_SESSION['login'] = true;
        $_SESSION['id'] = $data['id'];
        $_SESSION['name'] = $data['name'];
        $_SESSION['role'] = $data['role'];

        // =========================
        // ADMIN
        // =========================
        if ($data['role'] === 'admin') {

            header('Location: dashboard.php');
            exit;
        }

        // =========================
        // GURU
        // =========================
        elseif ($data['role'] === 'guru') {

            header('Location: guru/dashboard_guru.php');
            exit;
        }

        // =========================
        // ROLE TIDAK DIKENAL
        // =========================
        else {

            $_SESSION['pesan_error'] = 'Role akun tidak dikenali.';

            unset($_SESSION['login']);
            unset($_SESSION['id']);
            unset($_SESSION['name']);
            unset($_SESSION['role']);

            header('Location: login.php');
            exit;
        }

    } else {

        $_SESSION['pesan_error'] = 'Password salah!';
        header('Location: login.php');
        exit;
    }

} else {
    mysqli_stmt_close($stmt);

    $_SESSION['pesan_error'] = 'Email tidak ditemukan!';
    header('Location: login.php');
    exit;
}
?>