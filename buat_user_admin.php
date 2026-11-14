<?php
// Jalankan sekali lewat browser untuk membuat atau mereset akun admin awal.
include 'config/koneksi.php';

$name = 'Administrator';
$email = 'admin@gmail.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';

$check = mysqli_prepare($koneksi, 'SELECT id FROM t_users WHERE email = ? LIMIT 1');
if (!$check) {
    exit('Gagal memeriksa akun admin: ' . htmlspecialchars(mysqli_error($koneksi), ENT_QUOTES, 'UTF-8'));
}

$check_email = $email;
mysqli_stmt_bind_param($check, 's', $check_email);
if (!mysqli_stmt_execute($check)) {
    exit('Gagal memeriksa akun admin: ' . htmlspecialchars(mysqli_stmt_error($check), ENT_QUOTES, 'UTF-8'));
}

$hasil = mysqli_stmt_get_result($check);
if (!$hasil) {
    exit('Gagal membaca akun admin: ' . htmlspecialchars(mysqli_stmt_error($check), ENT_QUOTES, 'UTF-8'));
}

if (mysqli_fetch_assoc($hasil)) {
    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE t_users SET name = ?, password = ?, role = ?, remember_token = '' WHERE email = ?"
    );
    if (!$stmt) {
        exit('Gagal menyiapkan reset akun admin: ' . htmlspecialchars(mysqli_error($koneksi), ENT_QUOTES, 'UTF-8'));
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $name, $password, $role, $email);
    $berhasil = mysqli_stmt_execute($stmt);
} else {
    $stmt = mysqli_prepare(
        $koneksi,
        'INSERT INTO t_users (name, email, password, role, remember_token) VALUES (?, ?, ?, ?, ?)'
    );
    if (!$stmt) {
        exit('Gagal menyiapkan akun admin: ' . htmlspecialchars(mysqli_error($koneksi), ENT_QUOTES, 'UTF-8'));
    }

    $remember_token = '';
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $password, $role, $remember_token);
    $berhasil = mysqli_stmt_execute($stmt);
}

if ($berhasil) {
    echo 'Kata sandi admin@gmail.com sekarang admin123. Hapus file ini setelah selesai.';
} else {
    echo 'Gagal mengatur akun admin: ' . htmlspecialchars(mysqli_stmt_error($stmt), ENT_QUOTES, 'UTF-8');
}
?>