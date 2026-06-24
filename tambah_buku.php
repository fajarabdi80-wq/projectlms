<?php
require 'koneksi.php';
checkLogin();

$kategori = $pdo->query("SELECT * FROM kategori_buku")->fetchAll();
$penerbit = $pdo->query("SELECT * FROM penerbit")->fetchAll();
$rak = $pdo->query("SELECT * FROM rak")->fetchAll();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = $_POST['id_buku'];
    $kode_buku = $_POST['kode_buku'];
    $judul_buku = $_POST['judul_buku'];
    $pengarang = $_POST['pengarang'];
    $id_penerbit = $_POST['id_penerbit'];
    $id_kategori = $_POST['id_kategori'];
    $id_rak = $_POST['id_rak'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $stock = (int)$_POST['stock'];

    $check = $pdo->prepare("SELECT id_buku FROM buku WHERE id_buku = ? OR kode_buku = ?");
    $check->execute([$id_buku, $kode_buku]);
    
    if ($check->fetch()) {
        $msg = "Error: ID Buku atau Kode Buku sudah terdaftar!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO buku (id_buku, kode_buku, judul_buku, pengarang, id_penerbit, id_kategori, id_rak, tahun_terbit, stock, tersedia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_buku, $kode_buku, $judul_buku, $pengarang, $id_penerbit, $id_kategori, $id_rak, $tahun_terbit, $stock, $stock]);
        header("Location: buku.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2>Tambah Buku Baru</h2>
            <hr>
            <?php if($msg): ?> <div class="alert alert-danger"><?= $msg ?></div> <?php endif; ?>
            
            <?php if(empty($kategori) || empty($penerbit) || empty($rak)): ?>
                <div class="alert alert-warning">
                    <strong>Penting:</strong> Pastikan Anda telah mengisi tabel data <code>kategori_buku</code>, <code>penerbit</code>, dan <code>rak</code> di database Anda terlebih dahulu agar relasi data valid.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label>ID Buku (Unique)</label><input type="text" name="id_buku" class="form-control" placeholder="B001" required></div>
                    <div class="col-md-6 mb-3"><label>Kode Buku</label><input type="text" name="kode_buku" class="form-control" placeholder="KODE-B001" required></div>
                </div>
                <div class="mb-3"><label>Judul Buku</label><input type="text" name="judul_buku" class="form-control" required></div>
                <div class="mb-3"><label>Pengarang</label><input type="text" name="pengarang" class="form-control" required></div>
                
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <?php foreach($kategori as $k): ?><option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nm_kategori']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Penerbit</label>
                    <select name="id_penerbit" class="form-select" required>
                        <?php foreach($penerbit as $p): ?><option value="<?= $p['id_penerbit'] ?>"><?= htmlspecialchars($p['nm_penerbit']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Rak Penempatan</label>
                    <select name="id_rak" class="form-select" required>
                        <?php foreach($rak as $r): ?><option value="<?= $r['id_rak'] ?>"><?= htmlspecialchars($r['kode_rak']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label>Tanggal/Tahun Terbit</label><input type="date" name="tahun_terbit" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label>Jumlah Stok Master</label><input type="number" name="stock" class="form-control" min="1" required></div>
                </div>
                <button type="submit" class="btn btn-success">Simpan Buku</button>
                <a href="buku.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
