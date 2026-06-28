<?php
include 'koneksi.php';

if (isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = "";
if (isset($_POST['login'])) {
    $username = anti_injection($_POST['username']);
    $password = anti_injection($_POST['password']);
    $role     = anti_injection($_POST['role']);

    $query  = "SELECT * FROM login WHERE nama='$username' AND password='$password' AND role='$role'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $row['nama'];
        $_SESSION['role']      = $row['role'];
        $_SESSION['id_user']   = $row['id'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Username, Password, atau Hak Akses salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal PerpusLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { width: 100%; max-width: 420px; background: #ffffff; padding: 35px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .btn-unindra { background-color: #0284c7; color: white; border: none; }
        .btn-unindra:hover { background-color: #0369a1; color: white; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="text-center mb-4">
        <div class="text-primary mb-2"><i class="fa-solid fa-graduation-cap fa-3x"></i></div>
        <h4 class="fw-bold text-dark m-0">PERPUSLMS</h4>
        <small class="text-muted">Sistem Informasi Perpustakaan Terintegrasi</small>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger d-flex align-items-center py-2" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> <div style="font-size: 14px;"><?= $error; ?></div>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Username / Nomor Identitas</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Grup Modul Pengguna</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-sliders text-muted"></i></span>
                <select name="role" class="form-select" required>
                    <option value="petugas">Admin / Petugas</option>
                    <option value="anggota">Anggota / Mahasiswa</option>
                </select>
            </div>
        </div>
        <button type="submit" name="login" class="btn btn-unindra w-100 py-2.5 fw-bold shadow-sm">Masuk Ke Sistem <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i></button>
    </form>
</div>
</body>
</html>