<?php
require 'koneksi.php';
checkLogin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pinjam'])) {
    $id_peminjam = 'P-' . time();
    $id_anggota  = $_POST['id_anggota'];
    $id_buku     = $_POST['id_buku'];
    $id_petugas  = $_POST['id_petugas'];
    $tgl_pinjam  = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];

    $buku = $pdo->prepare("SELECT tersedia FROM buku WHERE id_buku = ?");
    $buku->execute([$id_buku]);
    $stok = $buku->fetchColumn();

    if ($stok > 0) {
        $pdo->beginTransaction();
        try {
            $ins = $pdo->prepare("INSERT INTO peminjaman (id_peminjam, id_anggota, id_buku, id_petugas, tgl_pinjam, tgl_kembali, status) VALUES (?, ?, ?, ?, ?, ?, 'dipinjam')");
            $ins->execute([$id_peminjam, $id_anggota, $id_buku, $id_petugas, $tgl_pinjam, $tgl_kembali]);

            $upd = $pdo->prepare("UPDATE buku SET tersedia = tersedia - 1 WHERE id_buku = ?");
            $upd->execute([$id_buku]);

            $pdo->commit();
            header("Location: peminjaman.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal memproses pinjaman: " . $e->getMessage();
        }
    } else {
        $error = "Buku ini sedang habis dipinjam!";
    }
}

$buku_list = $pdo->query("SELECT id_buku, judul_buku FROM buku WHERE tersedia > 0")->fetchAll();
$anggota_list = $pdo->query("SELECT id_anggota, nm_anggota FROM anggota WHERE status='aktif'")->fetchAll();
$petugas_list = $pdo->query("SELECT id_petugas, nm_petugas FROM petugas")->fetchAll();

$pinjaman = $pdo->query("SELECT p.*, a.nm_anggota, b.judul_buku FROM peminjaman p 
                         JOIN anggota a ON p.id_anggota = a.id_anggota 
                         JOIN buku b ON p.id_buku = b.id_buku 
                         ORDER BY p.tgl_pinjam DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Peminjaman & Pengembalian Buku</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
    
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5>Form Pinjam Buku</h5>
                    <form method="POST">
                        <div class="mb-2"><label>Anggota</label>
                            <select name="id_anggota" class="form-select" required>
                                <?php foreach($anggota_list as $a): ?><option value="<?= $a['id_anggota'] ?>"><?= $a['nm_anggota'] ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2"><label>Buku</label>
                            <select name="id_buku" class="form-select" required>
                                <?php foreach($buku_list as $b): ?><option value="<?= $b['id_buku'] ?>"><?= $b['judul_buku'] ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2"><label>Petugas Validator</label>
                            <select name="id_petugas" class="form-select" required>
                                <?php foreach($petugas_list as $pt): ?><option value="<?= $pt['id_petugas'] ?>"><?= $pt['nm_petugas'] ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2"><label>Tanggal Pinjam</label><input type="date" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" class="form-control" required></div>
                        <div class="mb-3"><label>Batas Pengembalian</label><input type="date" name="tgl_kembali" class="form-control" required></div>
                        <button type="submit" name="pinjam" class="btn btn-primary w-100">Proses Pinjam</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h5>Daftar Aktif & Riwayat Transaksi</h5>
            <table class="table table-bordered bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($pinjaman as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nm_anggota']) ?></td>
                        <td><?= htmlspecialchars($p['judul_buku']) ?></td>
                        <td><?= $p['tgl_kembali'] ?></td>
                        <td><span class="badge <?= $p['status'] == 'dipinjam' ? 'bg-warning' : 'bg-success' ?>"><?= strtoupper($p['status']) ?></span></td>
                        <td>
                            <?php if($p['status'] == 'dipinjam'): ?>
                                <a href="kembalikan_buku.php?id=<?= $p['id_peminjam'] ?>" class="btn btn-sm btn-success">Proses Kembali</a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-light" disabled>Selesai</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

