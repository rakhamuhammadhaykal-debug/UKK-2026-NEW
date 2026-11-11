
<?php
// layout/sidebar.php

$current_page = basename($_SERVER['PHP_SELF']);

$menu_admin = [
    [
        'nama' => 'Dashboard',
        'link' => 'dashboard.php',
        'icon' => '🏠'
    ],
    [
        'nama' => 'Kelola Siswa',
        'link' => 'kelola_siswa.php',
        'icon' => '👨‍🎓'
    ],
    [
        'nama' => 'Kelola Guru',
        'link' => 'kelola_guru.php',
        'icon' => '👨‍🏫'
    ],
    [
        'nama' => 'Kelola Kelas',
        'link' => 'kelola_kelas.php',
        'icon' => '🏫'
    ],
    [
        'nama' => 'Tahun Ajaran',
        'link' => 'kelola_tahun_ajaran.php',
        'icon' => '📅'
    ],
    [
        'nama' => 'Wali Kelas',
        'link' => 'kelola_walikelas.php',
        'icon' => '👥'
    ],
    [
        'nama' => 'Penempatan Siswa',
        'link' => 'penempatan_siswa.php',
        'icon' => '📋'
    ],
    [
        'nama' => 'Kategori Pelanggaran',
        'link' => 'kelola_pelanggaran_kategori.php',
        'icon' => '⚠️'
    ],
    [
        'nama' => 'About Me',
        'link' => 'about_me.php',
        'icon' => 'ℹ️'
    ]
];
?>

<style>
.sidebar-admin {
    background: #212529;
    min-height: 100vh;
    padding: 20px 12px;
    color: white;
}

.sidebar-admin h5 {
    text-align: center;
    font-weight: bold;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #495057;
}

.sidebar-admin .menu-link {
    display: block;
    color: #dee2e6;
    text-decoration: none;
    padding: 11px 13px;
    margin-bottom: 6px;
    border-radius: 7px;
    font-size: 14px;
    transition: 0.2s;
}

.sidebar-admin .menu-link:hover {
    background: #343a40;
    color: white;
}

.sidebar-admin .menu-link.active {
    background: #0d6efd;
    color: white;
    font-weight: bold;
}

.sidebar-admin .logout-link {
    background: #dc3545;
    color: white;
    margin-top: 20px;
}

.sidebar-admin .logout-link:hover {
    background: #bb2d3b;
}
</style>

<div class="col-md-3 col-lg-2 sidebar-admin">
    <h5>ADMIN PANEL</h5>

    <?php foreach ($menu_admin as $menu): ?>
        <a
            href="<?= htmlspecialchars($menu['link']); ?>"
            class="menu-link <?= ($current_page == $menu['link']) ? 'active' : ''; ?>"
        >
            <?= $menu['icon']; ?>
            <?= htmlspecialchars($menu['nama']); ?>
        </a>
    <?php endforeach; ?>

    <a href="logout.php" class="menu-link logout-link">
        🚪 Logout
    </a>
</div>
