<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "webperpustakaan";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("<div class='alert alert-danger'>Koneksi database gagal: " . mysqli_connect_error() . "</div>");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fungsi bantu untuk membersihkan input data dari SQL Injection
function anti_injection($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
}
?>