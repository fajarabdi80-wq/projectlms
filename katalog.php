<?php
include 'koneksi.php';
include 'layout.php';

$role = $_SESSION['role'];

// 1. PROSES TAMBAH BUKU (Petugas Only)
if (isset($_POST['tambah_buku']) && $role == 'petugas') {
    $id_buku      = anti_injection($_POST['id_buku']);
    $kode_buku    = anti_injection($_POST['kode_buku']);
    $judul_buku   = anti_injection($_POST['judul_buku']);
    $pengarang    = anti_injection($_POST['pengarang']);
    $id_penerbit  = anti_injection($_POST['id_penerbit']);
    $id_kategori  = anti_injection($_POST['id_kategori']);
    $id_rak       = anti_injection($_POST['id_rak']);
    $tahun_terbit = anti_injection($_POST['tahun_terbit']);
    $stock        = (int)$_POST['stock'];

    $query = "INSERT INTO buku VALUES ('$id_buku', '$kode_buku', '$judul_buku', '$pengarang', '$id_penerbit', '$id_kategori', '$id_rak', '$tahun_terbit', '$stock', '$stock')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: katalog.php?status=success_add");
    } else {
        header("Location: katalog.php?status=failed");
    }
}

// 2. PROSES UBAH BUKU (Petugas Only)
if (isset($_POST['ubah_buku']) && $role == 'petugas') {
    $id_buku    = anti_injection($_POST['id_buku']);
    $judul_buku = anti_injection($_POST['judul_buku']);
    $pengarang  = anti_injection($_POST['pengarang']);
    $stock      = (int)$_POST['stock'];

    $query = "UPDATE buku SET judul_buku='$judul_buku', pengarang='$pengarang', stock='$stock', tersedia='$stock' WHERE id_buku='$id_buku'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: katalog.php?status=success_edit");
    }
}

// 3. PROSES HAPUS BUKU (Petugas Only)
if (isset($_GET['hapus']) && $role == 'petugas') {
    $id_hapus = anti_injection($_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id_hapus'")) {
        header("Location: katalog.php?status=success_delete");
    }
}

// 4. PROSES PILIH / PINJAM BUKU (Anggota Only)
if (isset($_GET['pinjam_buku']) && $role == 'anggota') {
    $id_buku_pinjam = anti_injection($_GET['pinjam_buku']);
    
    // Cek ketersediaan buku terlebih dahulu
    $cek_buku = mysqli_query($koneksi, "SELECT tersedia FROM buku WHERE id_buku='$id_buku_pinjam'");
    $data_buku = mysqli_fetch_assoc($cek_buku);
    
    if ($data_buku['tersedia'] > 0) {
        $id_peminjam_auto = "P-" . rand(100000, 999999);
        $tgl_pinjam       = date('Y-m-d');
        $tgl_kembali      = date('Y-m-d', strtotime('+7 days')); // Durasi pinjam standar 7 hari
        
        // Meminjam menggunakan default ID Anggota dummy pertama 'A001' & Petugas 'PT001'
        $query_pinjam = "INSERT INTO peminjaman VALUES ('$id_peminjam_auto', 'A001', '$id_buku_pinjam', 'PT001', '$tgl_pinjam', '$tgl_kembali', 'dipinjam')";
        
        if (mysqli_query($koneksi, $query_pinjam)) {
            // Kurangi stok yang tersedia
            mysqli_query($koneksi, "UPDATE buku SET tersedia = tersedia - 1 WHERE id_buku='$id_buku_pinjam'");
            header("Location: katalog.php?status=success_borrow");
        }
    } else {
        header("Location: katalog.php?status=out_of_stock");
    }
}

render_header("Katalog Buku");
?>

<?php if(isset($_GET['status'])): ?>
    <div class="alert alert-success alert-dismissible fade show card-custom py-2.5 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        <span>
            <?php 
                if($_GET['status'] == 'success_add') echo "Data buku baru berhasil ditambahkan ke katalog.";
                elseif($_GET['status'] == 'success_edit') echo "Perubahan data buku berhasil disimpan.";
                elseif($_GET['status'] == 'success_delete') echo "Buku berhasil dihapus dari sistem database.";
                elseif($_GET['status'] == 'success_borrow') echo "Sukses! Buku berhasil Anda pilih, silakan ambil di ruang sirkulasi.";
                elseif($_GET['status'] == 'out_of_stock') echo "Maaf, buku ini tidak dapat dipilih karena stok habis.";
            ?>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0">Koleksi Pustaka Aktif</h5>
        <?php if ($role == 'petugas'): ?>
            <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fa-solid fa-plus me-1"></i> Entri Buku Baru</button>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead class="table-light">
                <tr>
                    <th>Kode Buku</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok (Tersedia)</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM buku";
                $result = mysqli_query($koneksi, $sql);
                while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><code><?= $row['kode_buku'] ?></code></td>
                    <td><span class="fw-semibold text-dark"><?= $row['judul_buku'] ?></span></td>
                    <td><?= $row['pengarang'] ?></td>
                    <td><?= date('Y', strtotime($row['tahun_terbit'])) ?></td>
                    <td>
                        <span class="badge bg-light text-dark border"><?= $row['stock'] ?> Total</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary"><?= $row['tersedia'] ?> Tersedia</span>
                    </td>
                    <td class="text-center">
                        <?php if($role == 'petugas'): ?>
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $row['id_buku'] ?>"><i class="fa-solid fa-marker"></i></button>
                            <a href="katalog.php?hapus=<?= $row['id_buku'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus buku ini secara permanen?')"><i class="fa-solid fa-trash-can"></i></a>
                        <?php else: ?>
                            <?php if($row['tersedia'] > 0): ?>
                                <a href="katalog.php?pinjam_buku=<?= $row['id_buku'] ?>" class="btn btn-sm btn-success px-3 rounded-pill"><i class="fa-solid fa-book-bookmark me-1"></i> Pilih</a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary disabled px-3 rounded-pill">Kosong</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>

                <div class="modal fade" id="modalUbah<?= $row['id_buku'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form class="modal-content" action="" method="POST">
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">Ubah Informasi Buku</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_buku" value="<?= $row['id_buku'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">Judul Buku</label>
                                    <input type="text" name="judul_buku" value="<?= $row['judul_buku'] ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Pengarang</label>
                                    <input type="text" name="pengarang" value="<?= $row['pengarang'] ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Total Alokasi Stok</label>
                                    <input type="number" name="stock" value="<?= $row['stock'] ?>" class="form-control" required min="1">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="ubah_buku" class="btn btn-warning btn-sm px-3">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="" method="POST">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Buku Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">ID Buku</label>
                        <input type="text" name="id_buku" class="form-control" placeholder="B011" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Kode Buku</label>
                        <input type="text" name="kode_buku" class="form-control" placeholder="KB011" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Judul Lengkap</label>
                        <input type="text" name="judul_buku" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" required>
                    </div>
                    <input type="hidden" name="id_penerbit" value="P001">
                    <input type="hidden" name="id_kategori" value="K001">
                    <input type="hidden" name="id_rak" value="R001">
                    <div class="col-6">
                        <label class="form-label">Tanggal Terbit</label>
                        <input type="date" name="tahun_terbit" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Jumlah Stok</label>
                        <input type="number" name="stock" class="form-control" required min="1">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" name="tambah_buku" class="btn btn-primary btn-sm px-3">Simpan ke DB</button>
            </div>
        </form>
    </div>
</div>

<?php render_footer(); ?>