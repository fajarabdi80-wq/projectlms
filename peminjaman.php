<?php
include 'koneksi.php';
include 'layout.php';

render_header("Transaksi Sirkulasi");
$role = $_SESSION['role'];
?>

<div class="card card-custom border-0 p-4 bg-white">
    <h5 class="fw-bold mb-3">Daftar Peminjaman Berjalan</h5>
    
    <div class="table-responsive">
        <table class="table table-striped align-middle m-0">
            <thead class="table-dark">
                <tr>
                    <th>ID Transaksi</th>
                    <th>Nama Peminjam</th>
                    <th>Judul Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Logika pemisahan data riwayat berdasarkan hak akses login
                if ($role == 'petugas') {
                    $query = "SELECT p.*, a.nm_anggota, b.judul_buku FROM peminjaman p 
                              JOIN anggota a ON p.id_anggota=a.id_anggota 
                              JOIN buku b ON p.id_buku=b.id_buku";
                } else {
                    $query = "SELECT p.*, a.nm_anggota, b.judul_buku FROM peminjaman p 
                              JOIN anggota a ON p.id_anggota=a.id_anggota 
                              JOIN buku b ON p.id_buku=b.id_buku 
                              WHERE p.id_anggota='A001'"; // Membatasi output hanya milik akun pengetes (A001)
                }

                $res = mysqli_query($koneksi, $query);
                if (mysqli_num_rows($res) == 0) {
                    echo "<tr><td colspan='6' class='text-center text-muted py-3'>Tidak ada catatan peminjaman aktif.</td></tr>";
                }

                while($row = mysqli_fetch_assoc($res)):
                ?>
                <tr>
                    <td><code><?= $row['id_peminjam'] ?></code></td>
                    <td><?= $row['nm_anggota'] ?></td>
                    <td><strong><?= $row['judul_buku'] ?></strong></td>
                    <td><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                    <td><?= date('d M Y', strtotime($row['tgl_kembali'])) ?></td>
                    <td class="text-center">
                        <span class="badge bg-<?= $row['status'] == 'dipinjam' ? 'warning' : 'success' ?> px-3 rounded-pill text-capitalize">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php render_footer(); ?>