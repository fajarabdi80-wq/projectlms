<?php
include 'koneksi.php';
include 'layout.php';

render_header("Dashboard");

// Fetching counts
$q_buku  = mysqli_query($koneksi, "SELECT SUM(stock) as total FROM buku");
$t_buku  = mysqli_fetch_assoc($q_buku)['total'] ?? 0;

$q_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'");
$t_pinjam = mysqli_fetch_assoc($q_pinjam)['total'] ?? 0;

$q_member = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota WHERE status='aktif'");
$t_member = mysqli_fetch_assoc($q_member)['total'] ?? 0;
?>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card card-custom p-4 bg-white border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase small">Koleksi Buku Terdaftar</h6>
                    <h2 class="fw-bold m-0 text-dark"><?= $t_buku ?> <span class="fs-6 text-muted font-normal">Pcs</span></h2>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 rounded text-primary"><i class="fa-solid fa-book fa-2x"></i></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card card-custom p-4 bg-white border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase small">Sedang Dipinjam Out</h6>
                    <h2 class="fw-bold m-0 text-dark"><?= $t_pinjam ?> <span class="fs-6 text-muted font-normal">Transaksi</span></h2>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 rounded text-warning"><i class="fa-solid fa-handshake-angle fa-2x"></i></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card card-custom p-4 bg-white border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase small">Total Anggota Aktif</h6>
                    <h2 class="fw-bold m-0 text-dark"><?= $t_member ?> <span class="fs-6 text-muted font-normal">Orang</span></h2>
                </div>
                <div class="p-3 bg-success bg-opacity-10 rounded text-success"><i class="fa-solid fa-users fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom border-0 p-4 bg-white">
    <h4 class="fw-bold text-dark mb-2">Selamat datang di Panel Utama Akademik Perpustakaan</h4>
    <p class="text-secondary m-0">Sistem ini memfasilitasi sirkulasi penelusuran pustaka secara mandiri untuk Anggota, serta modul pencatatan penuh untuk administrator perpustakaan.</p>
</div>

<?php render_footer(); ?>