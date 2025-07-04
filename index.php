<?php
session_start();
include 'koneksi.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 1. Cek di tabel admin
    $stmtAdmin = $conn->prepare("SELECT id, username, password, role FROM admin WHERE username = ?");
    $stmtAdmin->bind_param("s", $username);
    $stmtAdmin->execute();
    $resAdmin = $stmtAdmin->get_result();

    if ($resAdmin && $resAdmin->num_rows === 1) {
        $admin = $resAdmin->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];
            header("Location: halaman_utama.php"); // Halaman admin
            exit;
        }
    }

    // 2. Cek di tabel user
    $stmtUser = $conn->prepare("SELECT id_user, username, password, nama FROM user WHERE username = ?");
    $stmtUser->bind_param("s", $username);
    $stmtUser->execute();
    $resUser = $stmtUser->get_result();

    if ($resUser && $resUser->num_rows === 1) {
        $user = $resUser->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = 'user';
            header("Location: halaman_user.php"); // Halaman user
            exit;
        }
    }

    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SPK MAUT TOPSIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(to right,rgb(2, 6, 119),rgb(101, 96, 168));
        }
        .main-content {
            flex: 1;
            display: flex;
        }
        .left {
            flex: 1;
            color: white;
            padding: 30px 30px 30px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .left h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .left p {
            font-size: 15px;
            line-height: 1.7;
            max-width: 90%;
        }
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-box {
            width: 320px;
            padding: 30px;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.82);
        }
        .form-box h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .form-box input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 30px;
            font-size: 14px;
            outline: none;
        }
        .form-box button {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.25);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .form-box button:hover {
            background-color: rgba(20, 13, 13, 0.95);
        }
        .error {
            color: red;
            font-size: 13px;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar di atas -->
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container-fluid justify-content-center">
      <i class="fas fa-layer-group fa-lg me-2"></i>
    <span class="navbar-brand mb-0 h1 fw-bold text-darkblue">Sistem Pendukung Keputusan Kinerja Karyawan </span>
  </div>
</nav>

<!-- ✅ Konten utama login -->
<div class="main-content">
    <div class="left">
        <h1>Sistem Pendukung Keputusan Metode SAW</h1>
        <p>
            Metode Simple Additive Weighting (SAW) merupakan salah satu metode dalam pengambilan keputusan multikriteria (MCDM) yang digunakan untuk menentukan alternatif terbaik berdasarkan bobot dan nilai kriteria.
        </p>
        Setiap alternatif dievaluasi dengan menjumlahkan nilai-nilai yang telah dinormalisasi dan dikalikan dengan bobot kriteria, sehingga menghasilkan skor akhir yang dapat dibandingkan secara objektif.
    </p>
            Metode SAW dikenal karena kesederhanaannya, kejelasan perhitungan, dan keakuratan hasil dalam situasi keputusan yang kompleks.
        </p>
    </div>

    <div class="right">
        <div class="form-box">
            <h2>Login Account</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit" name="login">&#128274; Masuk</button>
                <?php if (!empty($error)): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

</body>
</html>
