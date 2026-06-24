<?php
require 'koneksi.php';
checkLogin();

$sql = "SELECT b.*, k.nm_kategori, p.nm_penerbit, r.kode_rak 
        FROM buku b
        LEFT JOIN kategori_buku k ON b.id_kategori = k.id_kategori
        LEFT JOIN penerbit p ON b.id_penerbit = p.id_penerbit
        LEFT JOIN rak r ON b.id_rak = r.id_rak
        ORDER BY b.judul_buku ASC";

$data_buku = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Katalog Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">PerpusWeb</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link active" href="buku.php">Katalog Buku</a>
            <a class="nav-link" href="peminjaman.php">Transaksi Peminjaman</a>
        </div>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h2>Katalog Buku</h2>
        <a href="tambah_buku.php" class="btn btn-primary">+ Tambah Buku</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Kategori</th>
                <th>Penerbit</th>
                <th>Rak</th>
                <th>Tersedia / Stok</th>
            </tr>
        </thead>
        <tbody>
        <?php if(empty($data_buku)): ?>
            <tr><td colspan="7" class="text-center">Belum ada data buku.</td></tr>
        <?php else: ?>
            <?php foreach($data_buku as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['kode_buku']) ?></td>
                    <td><strong><?= htmlspecialchars($row['judul_buku']) ?></strong></td>
                    <td><?= htmlspecialchars($row['pengarang']) ?></td>
                    <td><?= htmlspecialchars($row['nm_kategori'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($row['nm_penerbit'] ?: '-') ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['kode_rak'] ?: '-') ?></span></td>
                    <td>
                        <span class="badge <?= $row['tersedia'] > 0 ? 'bg-success':'bg-danger'?>">
                            <?= $row['tersedia'] ?>
                        </span> / <?= $row['stock'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

