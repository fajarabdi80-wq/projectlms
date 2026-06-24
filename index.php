<?php
require 'koneksi.php';
checkLogin();

$total_buku = $pdo->query("SELECT SUM(stock) FROM buku")->fetchColumn();
$buku_dipinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'")->fetchColumn();
$total_anggota = $pdo->query("SELECT COUNT(*) FROM anggota WHERE status='aktif'")->fetchColumn();
$total_denda = $pdo->query("SELECT SUM(total_denda) FROM denda WHERE status_bayar='nunggak'")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">PerpusWeb</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link active" href="index.php">Dashboard</a>
            <a class="nav-link" href="buku.php">Katalog Buku</a>
            <a class="nav-link" href="peminjaman.php">Transaksi Peminjaman</a>
        </div>
        <div>
            <span class="text-white me-3">Halo, <?= htmlspecialchars($_SESSION['nama']) ?> (<?= ucfirst($_SESSION['role']) ?>)</span>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>Dashboard</h2>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5>Total Buku</h5>
                    <h2><?= $total_buku ?: 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h5>Dipinjam</h5>
                    <h2><?= $buku_dipinjam ?: 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5>Anggota Aktif</h5>
                    <h2><?= $total_anggota ?: 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h5>Total Denda Pending</h5>
                    <h2>Rp <?= number_format($total_denda ?: 0, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="buku.php" class="btn btn-outline-primary">Kelola Katalog Buku</a>
        <a href="peminjaman.php" class="btn btn-primary">Buka Transaksi Peminjaman</a>
    </div>
</div>
</body>
</html>
