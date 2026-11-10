<?php
// guru/catat_pelanggaran.php
session_start();
include '../config/koneksi.php';

// Cek hak akses guru
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['pesan_error'] = 'Silakan login terlebih dahulu sebagai guru.';
    header('Location: ../login.php');
    exit;
}

$nama_guru = $_SESSION['name'];
$pesan = '';

// Proses simpan data pelanggaran jika form disubmit
if (isset($_POST['submit'])) {
    $siswa_id = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);
    $kategori_id = mysqli_real_escape_string($koneksi, $_POST['kategori_id']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $user_id = $_SESSION['id']; // ID guru yang sedang login

    $query = "INSERT INTO t_pelanggaran_siswa (siswa_id, kategori_id, tanggal, keterangan, user_id) 
              VALUES ('$siswa_id', '$kategori_id', '$tanggal', '$keterangan', '$user_id')";
    
    if (mysqli_query($koneksi, $query)) {
        $pesan = "<div style='background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;'>Pelanggaran berhasil dicatat!</div>";
    } else {
        $pesan = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;'>Gagal mencatat pelanggaran: " . mysqli_error($koneksi) . "</div>";
    }
}

// Ambil data siswa untuk dropdown
$query_siswa = mysqli_query($koneksi, "SELECT * FROM t_siswa");
// Ambil data kategori pelanggaran untuk dropdown
$query_kategori = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pelanggaran - Pelanggaran Siswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
        body { background-color: #f3f4f6; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #111827; color: white; padding: 25px 20px; display: flex; flex-direction: column; justify-content: space-between; position: fixed; top: 0; bottom: 0; left: 0; }
        .sidebar-brand { font-size: 20px; font-weight: bold; margin-bottom: 35px; line-height: 1.4; color: white; }
        .sidebar-menu { list-style: none; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a { color: #9ca3af; text-decoration: none; display: block; padding: 12px 15px; border-radius: 8px; transition: 0.2s; font-size: 14px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #2563eb; color: white; }
        .logout-btn { color: #ef4444; text-decoration: none; display: block; padding: 12px 15px; border-radius: 8px; font-size: 14px; font-weight: bold; transition: 0.2s; }
        .logout-btn:hover { background: rgba(239, 68, 68, 0.1); }
        .main-content { margin-left: 260px; flex-grow: 1; padding: 30px; }
        .form-container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; max-width: 700px; }
        .form-container h2 { margin-bottom: 20px; color: #111827; font-size: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: bold; color: #374151; margin-bottom: 8px; }
        .form-group select, .form-group input, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; }
        .form-group select:focus, .form-group input:focus, .form-group textarea:focus { border-color: #2563eb; }
        .btn-submit { background: #2563eb; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { background: #1d4ed8; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">Pelanggaran<br>Siswa</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard_guru.php">Dashboard</a></li>
                <li><a href="catat_pelanggaran.php" class="active">Catat Pelanggaran</a></li>
                <li><a href="data_pelanggaran.php">Data Pelanggaran</a></li>
            </ul>
        </div>
        <div>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Form Catat Pelanggaran Siswa</h2>
            <?php echo $pesan; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Pilih Siswa</label>
                    <select name="siswa_id" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = mysqli_fetch_assoc($query_siswa)) { ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nama'] ?? $s['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kategori Pelanggaran</label>
                    <select name="kategori_id" required>
                        <option value="">-- Pilih Kategori Pelanggaran --</option>
                        <?php while ($k = mysqli_fetch_assoc($query_kategori)) { ?>
                            <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_pelanggaran'] ?? $k['kategori']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="4" placeholder="Masukkan detail atau kronologi pelanggaran..."></textarea>
                </div>
                <button type="submit" name="submit" class="btn-submit">Simpan Pelanggaran</button>
            </form>
        </div>
    </div>

</body>
</html>