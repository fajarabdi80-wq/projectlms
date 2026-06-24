<?php
require 'koneksi.php';
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: peminjaman.php");
    exit;
}

$id_peminjam = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id_peminjam = ? AND status = 'dipinjam'");
$stmt->execute([$id_peminjam]);
$peminjaman = $stmt->fetch();

if (!$peminjaman) {
    die("Data transaksi tidak valid atau sudah dikembalikan.");
}

$id_pengembalian = 'R-' . time();
$tgl_kembalian   = date('Y-m-d');
$kondisi         = 'baik'; 

$denda_per_hari = 5000;
$total_denda = 0;

$batas_kembali = $peminjaman['tgl_kembali'];
if (strtotime($tgl_kembalian) > strtotime($batas_kembali)) {
    $selisih_detik = strtotime($tgl_kembalian) - strtotime($batas_kembali);
    $selisih_hari  = floor($selisih_detik / (60 * 60 * 24));
    $total_denda   = $selisih_hari * $denda_per_hari;
}

$pdo->beginTransaction();
try {
    $ins_kembali = $pdo->prepare("INSERT INTO pengembalian (id_pengembalian, id_peminjam, tgl_kembalian, kondisi) VALUES (?, ?, ?, ?)");
    $ins_kembali->execute([$id_pengembalian, $id_peminjam, $tgl_kembalian, $kondisi]);

    if ($total_denda > 0) {
        $id_denda = 'D-' . time();
        $ins_denda = $pdo->prepare("INSERT INTO denda (id_denda, id_pengembalian, total_denda, status_bayar, tgl_bayar) VALUES (?, ?, ?, 'nunggak', ?)");
        $ins_denda->execute([$id_denda, $id_pengembalian, $total_denda, $tgl_kembalian]);
        
        $upd_pinjam = $pdo->prepare("UPDATE peminjaman SET status = 'terlambat' WHERE id_peminjam = ?");
    } else {
        $upd_pinjam = $pdo->prepare("UPDATE peminjaman SET status = 'dikembalikan' WHERE id_peminjam = ?");
    }
    $upd_pinjam->execute([$id_peminjam]);

    $upd_stok = $pdo->prepare("UPDATE buku SET tersedia = tersedia + 1 WHERE id_buku = ?");
    $upd_stok->execute([$peminjaman['id_buku']]);

    $pdo->commit();
    header("Location: peminjaman.php");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal memproses pengembalian: " . $e->getMessage());
}
?>
