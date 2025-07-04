<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard SPK SAW</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f4f8; 
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
    
    .sidebar h2 {
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

    .welcome {
      background-color: #d0e4ff; 
      color: #003366; 
      padding: 15px 20px;
      border-radius: 5px;
      margin-bottom: 30px;
      font-size: 14px;
    }

    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .card-dashboard {
      background: white;
      border-left: 5px solid #004080; /* aksen biru gelap */
      border-radius: 10px;
      padding: 20px;
      width: 250px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.2s ease;
    }

    .card-dashboard:hover {
      transform: scale(1.03);
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .card-dashboard i {
      font-size: 24px;
      color: #004080;
    }

    .card-title {
      font-size: 16px;
      font-weight: 600;
      color: #002244;
    }
  </style>
</head>
<body>

<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">ADMIN</span>
</div>

<div class="sidebar">
  <h2><i class="fas fa-database"></i> SPK SAW</h2>
  <ul>
    <li><a href="halaman_utama.php"><i class="fas fa-home"></i> Dashboard</a></li>
    <li><a href="data_kriteria.php"><i class="fas fa-cube"></i> Data Kriteria</a></li>
    <li><a href="data_subkriteria.php"><i class="fas fa-sitemap"></i> Data Sub Kriteria</a></li>
    <li><a href="data_karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
    <li><a href="data_penilaian.php"><i class="fas fa-pen"></i> Data Penilaian</a></li>
    <li><a href="data_perhitungan.php"><i class="fas fa-calculator"></i> Data Perhitungan</a></li>
    <li><a href="hasil_akhir.php"><i class="fas fa-chart-bar"></i> Hasil Akhir</a></li>
    <hr>
    <li><a href="data_profil.php"><i class="fas fa-user"></i> Profile</a></li>
    <li><a href="crud/edit_profil.php"><i class="fas fa-id-badge"></i> Update Profile</a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</div>

<div class="main">
  <h4 class="mb-4"><i class="fas fa-home"></i> Dashboard</h4>

  <div class="welcome">
    Selamat datang <strong>ADMIN</strong>! Anda dapat mengelola sistem melalui menu di bawah ini.
  </div>

  <div class="cards">
    <div class="card-dashboard" style="border-left-color:#007bff">
      <span class="card-title">Data Kriteria</span>
      <i class="fas fa-cube"></i>
    </div>
    <div class="card-dashboard" style="border-left-color:#2196F3">
      <span class="card-title">Data Sub Kriteria</span>
      <i class="fas fa-sitemap"></i>
    </div>
    <div class="card-dashboard" style="border-left-color:#4CAF50">
      <span class="card-title">Data Karyawan</span>
      <i class="fas fa-users"></i>
    </div>
    <div class="card-dashboard" style="border-left-color:#9C27B0">
      <span class="card-title">Data Penilaian</span>
      <i class="fas fa-pen"></i>
    </div>
    <div class="card-dashboard" style="border-left-color:#FFC107">
      <span class="card-title">Data Perhitungan</span>
      <i class="fas fa-calculator"></i>
    </div>
    <div class="card-dashboard" style="border-left-color:#f44336">
      <span class="card-title">Hasil Akhir</span>
      <i class="fas fa-chart-bar"></i>
    </div>
  </div>
</div>
</body>
</html>
