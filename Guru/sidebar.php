<?php
// Pastikan session sudah dimulai di halaman pemanggil
if (!defined('INCLUDED')) exit;
?>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f8f9fa;
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #1a202c;
    color: white;
    padding: 20px 0;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.sidebar-header {
    padding: 15px 20px;
    font-size: 18px;
    font-weight: 600;
    border-bottom: 1px solid #2d3748;
    margin-bottom: 10px;
}

.sidebar-menu {
    list-style: none;
}

.sidebar-menu li a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #cbd5e0;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.sidebar-menu li a:hover {
    background-color: #2d3748;
    color: white;
}

.sidebar-menu li a.active {
    background-color: #2b6cb0;
    color: white;
    border-left: 4px solid #3182ce;
}

.sidebar-menu li a.logout {
    color: #fc8181;
    margin-top: 20px;
}

.sidebar-menu li a.logout:hover {
    background-color: #742a2a;
    color: white;
}

.content {
    flex: 1;
    margin-left: 250px;
    padding: 30px;
}

.page-header {
    margin-bottom: 25px;
}

.page-header h1 {
    font-size: 24px;
    color: #2d3748;
    margin-bottom: 5px;
}

.page-header p {
    color: #718096;
    font-size: 14px;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 25px;
}

.card-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    color: #2d3748;
}

.card-body {
    padding: 20px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #2b6cb0;
    color: white;
}

.btn-primary:hover {
    background-color: #2c5282;
}

.btn-success {
    background-color: #2f855a;
    color: white;
}

.btn-success:hover {
    background-color: #276749;
}

.btn-danger {
    background-color: #c53030;
    color: white;
}

.btn-danger:hover {
    background-color: #9b2c2c;
}

.btn-light {
    background-color: #e2e8f0;
    color: #2d3748;
}

.btn-light:hover {
    background-color: #cbd5e0;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #2d3748;
    margin-bottom: 6px;
}

.form-control, .form-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 14px;
    color: #2d3748;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: #3182ce;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th {
    background-color: #f7fafc;
    padding: 12px 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    border-bottom: 1px solid #e2e8f0;
}

table td {
    padding: 12px 15px;
    font-size: 14px;
    color: #2d3748;
    border-bottom: 1px solid #e2e8f0;
}

table tbody tr:hover {
    background-color: #f7fafc;
}

.alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background-color: #f0fff4;
    border: 1px solid #9ae6b4;
    color: #22543d;
}

.alert-danger {
    background-color: #fed7d7;
    border: 1px solid #feb2b2;
    color: #742a2a;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-primary { background: #ebf8ff; color: #2c5282; }
.badge-success { background: #f0fff4; color: #22543d; }
.badge-warning { background: #fef5e7; color: #975a16; }
.badge-danger { background: #fed7d7; color: #742a2a; }

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 25px;
}

.col-6 { flex: 0 0 calc(50% - 10px); }
.col-4 { flex: 0 0 calc(33.333% - 14px); }

.stat-card {
    background: white;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 20px;
}

.stat-label {
    font-size: 14px;
    color: #718096;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
}

@media (max-width: 768px) {
    .sidebar { position: relative; width: 100%; height: auto; }
    .content { margin-left: 0; padding: 20px; }
    .row { display: block; }
    .col-6, .col-4 { margin-bottom: 20px; }
}
</style>

<div class="sidebar">
    <div class="sidebar-header">Pelanggaran Siswa</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard_guru.php" class="<?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="catat_pelanggaran.php" class="<?= ($active_page ?? '') === 'catat' ? 'active' : '' ?>">Catat Pelanggaran</a></li>
        <li><a href="data_pelanggaran.php" class="<?= ($active_page ?? '') === 'data' ? 'active' : '' ?>">Data Pelanggaran</a></li>
        <li><a href="rekap_poin.php" class="<?= ($active_page ?? '') === 'rekap' ? 'active' : '' ?>">Rekap Poin</a></li>
        <li><a href="tindakan.php" class="<?= ($active_page ?? '') === 'tindakan' ? 'active' : '' ?>">Tindakan</a></li>
        <li><a href="riwayat.php" class="<?= ($active_page ?? '') === 'riwayat' ? 'active' : '' ?>">Riwayat</a></li>
        <li><a href="../logout.php" class="logout">Logout</a></li>
    </ul>
</div>
