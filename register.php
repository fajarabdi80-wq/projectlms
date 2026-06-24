<?php
require 'koneksi.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM login WHERE nama = ?");
    $stmt->execute([$nama]);
    if ($stmt->fetch()) {
        $error = "Username sudah digunakan!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO login (nama, password, role) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $password, $role])) {
            $success = "Registrasi berhasil! Silakan <a href='login.php'>Login</a>";
        } else {
            $error = "Gagal mendaftar.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Register</h3>
                    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="petugas">Petugas</option>
                                <option value="anggota">Anggota</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Daftar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
