<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['metode']) && $_POST['metode'] === 'saw') {
    $kriteria = [];
    $resKriteria = $conn->query("SELECT * FROM data_kriteria");
    while ($row = $resKriteria->fetch_assoc()) {
        $kriteria[$row['id_kriteria']] = $row;
    }

    $karyawan = [];
    $resKaryawan = $conn->query("SELECT * FROM karyawan");
    while ($row = $resKaryawan->fetch_assoc()) {
        $karyawan[$row['id_karyawan']] = $row;
    }

    $penilaian = [];
    foreach ($karyawan as $id_karyawan => $data) {
        $penilaian[$id_karyawan] = [];
        foreach ($kriteria as $id_kriteria => $kri) {
            $res = $conn->query("SELECT nilai FROM penilaian WHERE id_karyawan='$id_karyawan' AND id_kriteria='$id_kriteria'");
            $row = $res->fetch_assoc();
            $penilaian[$id_karyawan][$id_kriteria] = $row ? $row['nilai'] : null;
        }
    }

    // FIXED max/min logic
    $max = $min = [];
    foreach ($kriteria as $id_kriteria => $kri) {
        $nilai_kriteria = [];
        foreach ($penilaian as $nilai_per_karyawan) {
            if (isset($nilai_per_karyawan[$id_kriteria]) && $nilai_per_karyawan[$id_kriteria] !== null) {
                $nilai_kriteria[] = $nilai_per_karyawan[$id_kriteria];
            }
        }
        $max[$id_kriteria] = !empty($nilai_kriteria) ? max($nilai_kriteria) : 1;
        $min[$id_kriteria] = !empty($nilai_kriteria) ? min($nilai_kriteria) : 1;
    }

    $normalisasi = [];
    $preferensi = [];
    foreach ($penilaian as $id_karyawan => $nilai_karyawan) {
        $normalisasi[$id_karyawan] = [];
        $preferensi[$id_karyawan] = null;
        $prefTotal = 0;
        $complete = true;
        foreach ($kriteria as $id_kriteria => $kri) {
            $nilai = $nilai_karyawan[$id_kriteria];
            if ($nilai === null) {
                $normalisasi[$id_karyawan][$id_kriteria] = null;
                $complete = false;
                continue;
            }
            $norm = (strtolower($kri['jenis']) === 'benefit') ? ($nilai / $max[$id_kriteria]) : ($min[$id_kriteria] / $nilai);
            $normalisasi[$id_karyawan][$id_kriteria] = $norm;
            $prefTotal += $norm * $kri['bobot'];
        }
        if ($complete) $preferensi[$id_karyawan] = $prefTotal;
    }

    $conn->query("DELETE FROM hasil_preferensi");
    foreach ($preferensi as $id => $val) {
        if ($val !== null) {
            $stmt = $conn->prepare("INSERT INTO hasil_preferensi (id_karyawan, nilai_preferensi) VALUES (?, ?)");
            $stmt->bind_param("id", $id, $val);
            $stmt->execute();
        }
    }

    ob_start(); ?>

    <div class="card p-4 mt-4">
        <h5>1. Matriks Penilaian Awal</h5>
        <table class="table table-bordered mt-3">
            <thead><tr><th>Nama</th><?php foreach ($kriteria as $k) echo "<th>{$k['nama_kriteria']}</th>"; ?></tr></thead>
            <tbody>
            <?php foreach ($penilaian as $id => $nilai): ?>
                <tr><td><?= $karyawan[$id]['nama_karyawan'] ?></td>
                <?php foreach ($nilai as $v) echo "<td>" . ($v === null ? '-' : $v) . "</td>"; ?></tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h5 class="mt-4">2. Bobot Kriteria</h5>
        <table class="table table-bordered mt-3">
            <thead><tr><?php foreach ($kriteria as $k) echo "<th>{$k['nama_kriteria']}</th>"; ?></tr></thead>
            <tbody><tr><?php foreach ($kriteria as $k) echo "<td>{$k['bobot']}</td>"; ?></tr></tbody>
        </table>

        <h5 class="mt-4">3. Matriks Normalisasi</h5>
        <table class="table table-bordered mt-3">
            <thead><tr><th>Nama</th><?php foreach ($kriteria as $k) echo "<th>{$k['nama_kriteria']}</th>"; ?></tr></thead>
            <tbody>
            <?php foreach ($normalisasi as $id => $nilai): ?>
                <tr><td><?= $karyawan[$id]['nama_karyawan'] ?></td>
                <?php foreach ($nilai as $v) echo "<td>" . ($v === null ? '-' : number_format($v, 4)) . "</td>"; ?></tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h5 class="mt-4">4. Normalisasi Terbobot</h5>
        <table class="table table-bordered mt-3">
            <thead><tr><th>Nama</th><?php foreach ($kriteria as $k) echo "<th>{$k['nama_kriteria']}</th>"; ?><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($normalisasi as $id => $nilai): ?>
                <tr><td><?= $karyawan[$id]['nama_karyawan'] ?></td>
                <?php $total = 0; foreach ($nilai as $id_k => $v) {
                    if ($v === null) {
                        echo "<td>-</td>";
                    } else {
                        $bobot = $v * $kriteria[$id_k]['bobot'];
                        $total += $bobot;
                        echo "<td>" . number_format($bobot, 4) . "</td>";
                    }
                }
                echo "<td>" . ($preferensi[$id] !== null ? number_format($preferensi[$id], 4) : '-') . "</td></tr>";
                ?>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h5 class="mt-4">5. Nilai Preferensi Akhir</h5>
        <table class="table table-bordered mt-3">
            <thead><tr><th>Nama</th><th>Preferensi</th></tr></thead>
            <tbody>
            <?php foreach ($preferensi as $id => $val): ?>
                <tr><td><?= $karyawan[$id]['nama_karyawan'] ?></td><td><?= $val === null ? '-' : number_format($val, 4) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php
    echo ob_get_clean();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Perhitungan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
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
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
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

.table th, .table td {
  vertical-align: middle;
}
    </style>
</head>
<body>

    
<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">ADMIN</span>
</div>

<!-- Sidebar -->
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

<!-- Main Content -->
<div class="main">
    <h4 class="mb-4"><i class="fas fa-calculator"></i> Data Perhitungan</h4>
    <div class="card p-4 mb-4">
        <form id="formMetode" class="row g-3 align-items-center">
            <div class="col-md-6">
                <select name="metode" class="form-select" required>
                    <option value="">--Pilih Metode Perhitungan--</option>
                    <option value="saw">Metode SAW</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-play"></i> Proses</button>
            </div>
        </form>
    </div>
    <div id="hasilPerhitungan"></div>
</div>

<script>
    $('#formMetode').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '', // same file
            data: $(this).serialize(),
            beforeSend: function () {
                $('#hasilPerhitungan').html('<div class="text-center p-5"><div class="spinner-border text-success"></div><p class="mt-2">Memproses...</p></div>');
            },
            success: function (res) {
                $('#hasilPerhitungan').html(res);
            },
            error: function () {
                $('#hasilPerhitungan').html('<div class="alert alert-danger">Terjadi kesalahan saat memproses data.</div>');
            }
        });
    });
</script>

</body>
</html>
