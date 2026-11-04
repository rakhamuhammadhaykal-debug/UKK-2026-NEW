<?php
if ($_SESSION['role'] === "admin") {
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- SIDEBAR ADMIN -->
<div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

    <h4 class="text-white mb-4">
        Pelanggaran Siswa
    </h4>

    <ul class="nav nav-pills flex-column">

        <!-- Dashboard -->
        <li class="nav-item mb-2">
            <a href="dashboard.php"
               class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : 'text-white' ?>">
                Dashboard
            </a>
        </li>

        <!-- Data Siswa -->
        <li class="nav-item mb-2">
            <a href="kelola_siswa.php"
               class="nav-link <?= $current_page == 'kelola_siswa.php' ? 'active' : 'text-white' ?>">
                Data Siswa
            </a>
        </li>

        <!-- Data Guru -->
        <li class="nav-item mb-2">
            <a href="kelola_guru.php"
               class="nav-link <?= $current_page == 'kelola_guru.php' ? 'active' : 'text-white' ?>">
                Data Guru
            </a>
        </li>

        <!-- Data Kelas -->
        <li class="nav-item mb-2">
            <a href="kelola_kelas.php"
               class="nav-link <?= $current_page == 'kelola_kelas.php' ? 'active' : 'text-white' ?>">
                Data Kelas
            </a>
        </li>

        <!-- Data Tahun Ajaran -->
        <li class="nav-item mb-2">
            <a href="kelola_tahun_ajaran.php"
               class="nav-link <?= $current_page == 'kelola_tahun_ajaran.php' ? 'active' : 'text-white' ?>">
                Data Tahun Ajaran
            </a>
        </li>

        <!-- Data Wali Kelas -->
        <li class="nav-item mb-2">
            <a href="kelola_walikelas.php"
               class="nav-link <?= $current_page == 'kelola_walikelas.php' ? 'active' : 'text-white' ?>">
                Data Wali Kelas
            </a>
        </li>

        <!-- Penempatan Siswa -->
        <li class="nav-item mb-2">
            <a href="penempatan_siswa.php"
               class="nav-link <?= $current_page == 'penempatan_siswa.php' ? 'active' : 'text-white' ?>">
                Penempatan Siswa
            </a>
        </li>

        <!-- Kategori Pelanggaran -->
        <li class="nav-item mb-2">
            <a href="kelola_pelanggaran_kategori.php"
               class="nav-link <?= $current_page == 'kelola_pelanggaran_kategori.php' ? 'active' : 'text-white' ?>">
                Kategori Pelanggaran
            </a>
        </li>

        <!-- About Me -->
        <li class="nav-item mb-2">
            <a href="about_me.php"
               class="nav-link <?= $current_page == 'about_me.php' ? 'active' : 'text-white' ?>">
                About Me
            </a>
        </li>

        <hr class="text-secondary">

        <!-- Logout -->
        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                Logout
            </a>
        </li>

    </ul>
</div>

<?php
}
?>