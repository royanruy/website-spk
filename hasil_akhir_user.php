<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: halaman_user.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$karyawan = [];
$resKaryawan = $conn->query("SELECT * FROM karyawan");
while ($row = $resKaryawan->fetch_assoc()) {
    $karyawan[$row['id_karyawan']] = $row;
}

$preferensi = [];
$res = $conn->query("SELECT * FROM hasil_preferensi");
while ($row = $res->fetch_assoc()) {
    $preferensi[$row['id_karyawan']] = $row['nilai_preferensi'];
}
arsort($preferensi);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Akhir - Metode SAW</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #004080, #002244); 
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
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
        }
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: #d6e4f5; /* biru muda lembut */
            font-weight: 600;
            color: #002244; /* biru tua */
            font-size: 14px;
        }

        .table td {
            font-size: 14px;
            color: #333; /* teks biasa */
        }

        .table th:nth-child(1){
            text-align: left;
    }
        .table th:nth-child(3){
            text-align: center;
        }
        .table td:nth-child(1),{
            text-align: left;
        }
        .table td:nth-child(3) {
            text-align: center;
        }

        .table th:nth-child(2){
            text-align: left;
        }
        .table td:nth-child(2) {
            text-align: left;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .card, .card * {
                visibility: visible;
            }
            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .sidebar, .btn {
                display: none;
            }
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
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Hasil Akhir Perankingan </h4>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Nilai</th>
                    <th>Ranking</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($preferensi as $id => $val): ?>
                <tr>
                    <td><?= htmlspecialchars($karyawan[$id]['nama_karyawan']) ?></td>
                    <td><?= number_format($val, 4) ?></td>
                    <td><?= $rank++ ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
