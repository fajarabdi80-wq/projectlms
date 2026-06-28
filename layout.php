<?php
include_once 'koneksi.php';

function render_header($title) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
    $role = $_SESSION['role'];
    $user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Portal Akademik PerpusWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #1e293b; --sidebar-hover: #334155; --primary-lms: #0284c7; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #f8fafc; }
        
        /* Sidebar LMS Unindra Styling */
        .sidebar { height: 100vh; width: 260px; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; z-index: 1000; box-shadow: 4px 0 10px rgba(0,0,0,0.05); }
        .sidebar-brand { padding: 24px; border-bottom: 1px solid #334155; background: #0f172a; }
        .sidebar .nav-link { color: #94a3b8; padding: 14px 20px; font-weight: 500; border-left: 4px solid transparent; display: flex; align-items: center; gap: 12px; transition: all 0.2s; }
        .sidebar .nav-link:hover { background-color: var(--sidebar-hover); color: #f1f5f9; }
        .sidebar .nav-link.active { background-color: #0f172a; color: #fff; border-left-color: var(--primary-lms); }
        
        /* Content & Navbar adjustments */
        .navbar-custom { margin-left: 260px; background-color: #ffffff; border-bottom: 1px solid #e2e8f0; height: 70px; }
        .main-container { margin-left: 260px; padding: 40px; min-height: calc(100vh - 70px); }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column justify-content-between">
    <div>
        <div class="sidebar-brand text-center">
            <h4 class="mb-0 fw-bold text-white"><i class="fa-solid fa-layer-group text-info me-2"></i>PerpusLMS</h4>
        </div>
        <div class="px-3 py-3 text-center border-bottom border-secondary bg-dark bg-opacity-25">
            <div class="text-white-50 small mb-1">Level Akses</div>
            <span class="badge bg-info text-capitalize px-3 rounded-pill"><?= $role ?></span>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?= $title == 'Dashboard' ? 'active' : '' ?>" href="index.php"><i class="fa-solid fa-house-laptop fs-5"></i> Beranda Utama</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $title == 'Katalog Buku' ? 'active' : '' ?>" href="katalog.php"><i class="fa-solid fa-book-bookmark fs-5"></i> Katalog & Pilih Buku</a>
            </li>
            
            <?php if ($role == 'petugas'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $title == 'Transaksi Sirkulasi' ? 'active' : '' ?>" href="peminjaman.php"><i class="fa-solid fa-business-time fs-5"></i> Kontrol Sirkulasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-users-gear fs-5"></i> Master Data Anggota</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="p-3 text-center text-muted border-top border-secondary small">
        &copy; 2026 PerpusWeb V2.0
    </div>
</div>

<nav class="navbar navbar-expand navbar-light navbar-custom px-4 sticky-top">
    <div class="container-fluid justify-content-between">
        <h5 class="m-0 fw-bold text-secondary"><i class="fa-solid fa-bars-staggered me-2"></i><?= $title ?></h5>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-sm-block">
                <span class="text-dark d-block fw-semibold"><?= $user ?></span>
                <small class="text-muted text-uppercase" style="font-size: 10px;">ID: <?= $_SESSION['id_user'] ?></small>
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-danger px-3"><i class="fa-solid fa-power-off me-1"></i> Keluar</a>
        </div>
    </div>
</nav>

<div class="main-container">
<?php 
}

function render_footer() {
    echo '</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}
?>