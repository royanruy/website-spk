<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #004080, #002244); 
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar h4 {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .main {
            margin-left: 250px;
            padding: 75px 30px 30px 30px;
            width: calc(100% - 250px);
        }

        .card-menu {
            border: 1px solid #eee;
            border-left: 5px solid #004080; /* aksen biru gelap */
            border-radius: 8px;
            box-shadow:0 2px 6px rgba(0,0,0,0.1);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .card-menu:hover {
            transform: translateY(-3px);
            box-shadow:  0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .card-menu i {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .alert {
            border-left: 5px solidrgb(12, 0, 179);
            background-color: #d0e4ff; 
            color: #004080;
        }
    </style>
</head>
<body>
    
<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">USER</span>
</div>

<div class="sidebar">
    <h4><i class="fas fa-database"></i> SPK SAW</h4>
    <ul>
        <li><a href="halaman_user.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="hasil_akhir_user.php"><i class="fas fa-chart-bar"></i> Hasil Akhir</a></li>
        <hr>
        <li><a href="crud/edit_profil_user.php"><i class="fas fa-id-badge"></i> Update Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <h4><i class="fas fa-home"></i> Dashboard</h4>
    <div class="alert alert-success mt-3">
        Selamat datang <strong><?= strtoupper($_SESSION['username']) ?></strong>! Anda bisa mengoperasikan sistem dengan wewenang tertentu melalui pilihan menu di bawah.
    </div>

    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <a href="#" class="text-decoration-none text-dark">
                <div class="card-menu text-center">
                    <i class="fas fa-home text-darkblue"></i>
                    <h6 class="mt-2">Dashboard</h6>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="#" class="text-decoration-none text-dark">
                <div class="card-menu text-center">
                    <i class="fas fa-chart-bar text-darkblue"></i>
                    <h6 class="mt-2">Hasil Akhir</h6>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="#" class="text-decoration-none text-dark">
                <div class="card-menu text-center">
                    <i class="fas fa-user text-darkblue"></i>
                    <h6 class="mt-2">Profile</h6>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
