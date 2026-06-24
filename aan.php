<?php
// Menampilkan judul dengan tag HTML
echo "<h1>Uji Coba PHP Berhasil!</h1>";

// Mendefinisikan variabel string
$nama_sistem = "Sistem Pemilihan Bumbu";

// Mendefinisikan array
$bumbu_dasar = ["Bawang Merah", "Bawang Putih", "Merica", "Ketumbar"];

// Menampilkan output dari variabel
echo "<p>Sistem yang sedang diuji: <strong>" . $nama_sistem . "</strong></p>";
echo "<p>Daftar Bumbu Dasar:</p>";

// Menggunakan perulangan foreach untuk menampilkan isi array
echo "<ul>";
foreach ($bumbu_dasar as $bumbu) {
    echo "<li>" . $bumbu . "</li>";
}
echo "</ul>";

// Uji coba operasi matematika sederhana

$terpakai = 15;
$sisa = $stok_bawang - $terpakai;

echo "<p>Sisa stok bawang: " . $sisa . " gram.</p>";
?>