<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../halaman_utama.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_karyawan = $_GET['id'] ?? null;
if (!$id_karyawan) {
    die("ID karyawan tidak ditemukan.");
}

$karyawan = $conn->query("SELECT * FROM karyawan WHERE id_karyawan = '$id_karyawan'")->fetch_assoc();
$kriteria = $conn->query("SELECT * FROM data_kriteria");

// Proses simpan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['nilai'] as $id_kriteria => $nilai) {
        $conn->query("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai) VALUES ('$id_karyawan', '$id_kriteria', '$nilai')");
    }
    $_SESSION['sukses_tambah'] = "Penilaian berhasil disimpan!";
    header("Location: ../data_penilaian.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Penilaian</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-plus"></i> Input Penilaian - <?= $karyawan['nama_karyawan'] ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php while($k = $kriteria->fetch_assoc()): ?>
                    <div class="mb-3">
                        <label><?= $k['nama_kriteria'] ?> (<?= $k['jenis'] ?>)</label>
                        <input type="number" name="nilai[<?= $k['id_kriteria'] ?>]" class="form-control" step="0.01" required>
                    </div>
                <?php endwhile; ?>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                <a href="../data_penilaian.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
