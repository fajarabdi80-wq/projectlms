<?php
include 'koneksi.php';
include 'layout.php';

render_header("Kelola Denda");
$role = $_SESSION['role'];

// PROSES PELUNASAN DENDA (Petugas Only)
if (isset($_GET['lunaskan']) && $role == 'petugas') {
    $id_denda = anti_injection($_GET['lunaskan']);
    // Mengubah status_denda menjadi lunas (bisa disesuaikan dengan tipe datanya di SQL Anda, misal varchar atau enum)
    $q_lunas = "UPDATE denda SET status_denda='lunas' WHERE id_denda='$id_denda'";
    if (mysqli_query($koneksi, $q_lunas)) {
        header("Location: denda.php?status=sukses_lunas");
        exit;
    }
}
?>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_lunas'): ?>
    <div class="alert alert-success alert-dismissible fade show card-custom py-2.5 mb-4" role="alert">
        <i class="fa-solid fa-square-check me-2"></i> Status denda telah diperbarui menjadi <strong>Lunas</strong>. Terima kasih.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 p-4 bg-white shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i>Catatan Denda Mahasiswa</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle m-0">
            <thead class="table-dark">
                <tr>
                    <th>ID Denda</th>
                    <th>Nama Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tgl Kembali</th>
                    <th>Tarif Denda</th>
                    <th>Status Pembayaran</th>
                    <?php if ($role == 'petugas'): ?><th class="text-center">Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query relasi data denda -> pengembalian -> peminjaman -> anggota & buku
                if ($role == 'petugas') {
                    $query = "SELECT d.*, k.tgl_pengembalian, a.nm_anggota, b.judul_buku 
                              FROM denda d
                              JOIN pengembalian k ON d.id_pengembalian = k.id_pengembalian
                              JOIN peminjaman p ON k.id_peminjam = p.id_peminjam
                              JOIN anggota a ON p.id_anggota = a.id_anggota
                              JOIN buku b ON p.id_buku = b.id_buku
                              ORDER BY d.tgl_denda DESC";
                } else {
                    $query = "SELECT d.*, k.tgl_pengembalian, a.nm_anggota, b.judul_buku 
                              FROM denda d
                              JOIN pengembalian k ON d.id_pengembalian = k.id_pengembalian
                              JOIN peminjaman p ON k.id_peminjam = p.id_peminjam
                              JOIN anggota a ON p.id_anggota = a.id_anggota
                              JOIN buku b ON p.id_buku = b.id_buku
                              WHERE p.id_anggota = 'A001'
                              ORDER BY d.tgl_denda DESC";
                }

                $res = mysqli_query($koneksi, $query);
                if (mysqli_num_rows($res) == 0) {
                    echo "<tr><td colspan='7' class='text-center text-muted py-4'>Bersih! Tidak ada catatan tanggungan denda keterlambatan.</td></tr>";
                }

                while ($row = mysqli_fetch_assoc($res)):
                    $is_lunas = (strtolower($row['status_denda']) == 'lunas');
                ?>
                <tr>
                    <td><code><?= $row['id_denda'] ?></code></td>
                    <td><?= $row['nm_anggota'] ?></td>
                    <td><?= $row['judul_buku'] ?></td>
                    <td><?= date('d M Y', strtotime($row['tgl_pengembalian'])) ?></td>
                    <td class="fw-bold text-danger">Rp <?= number_format($row['tarif_denda'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge bg-<?= $is_lunas ? 'success' : 'danger' ?> px-3 rounded-pill text-capitalize">
                            <?= $row['status_denda'] ?>
                        </span>
                    </td>
                    <?php if ($role == 'petugas'): ?>
                    <td class="text-center">
                        <?php if (!$is_lunas): ?>
                            <a href="denda.php?lunaskan=<?= $row['id_denda'] ?>" class="btn btn-sm btn-outline-success px-2 py-1 rounded shadow-sm" onclick="return confirm('Konfirmasi Pelunasan Denda Tunai?')">
                                <i class="fa-solid fa-cash-register me-1"></i> Set Lunas
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-light text-muted disabled"><i class="fa-solid fa-check-double"></i> Selesai</button>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php render_footer(); ?>