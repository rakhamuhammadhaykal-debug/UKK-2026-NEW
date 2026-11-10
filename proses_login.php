<?php
// proses_login.php

session_start();

include 'config/koneksi.php';

$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = '$email'";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    $_SESSION['pesan_error'] = 'Terjadi kesalahan pada database.';
    header('Location: login.php');
    exit;
}

if (mysqli_num_rows($hasil) == 1) {

    $data = mysqli_fetch_assoc($hasil);

    // Cek password menggunakan MD5 (karena database kamu pakai MD5)
    if (md5($password) === $data['password']) {

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

    $_SESSION['pesan_error'] = 'Email tidak ditemukan!';
    header('Location: login.php');
    exit;
}
?>