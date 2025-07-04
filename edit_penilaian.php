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

// Ambil data karyawan dan kriteria
$karyawan = $conn->query("SELECT * FROM karyawan WHERE id_karyawan = '$id_karyawan'")->fetch_assoc();
$kriteria = $conn->query("SELECT * FROM data_kriteria");

// Ambil nilai penilaian sebelumnya
$nilai_penilaian = [];
$nilai_q = $conn->query("SELECT * FROM penilaian WHERE id_karyawan = '$id_karyawan'");
while ($row = $nilai_q->fetch_assoc()) {
    $nilai_penilaian[$row['id_kriteria']] = $row['nilai'];
}

// Update jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['nilai'] as $id_kriteria => $nilai_baru) {
        $cek = $conn->query("SELECT * FROM penilaian WHERE id_karyawan = '$id_karyawan' AND id_kriteria = '$id_kriteria'");
        if ($cek->num_rows > 0) {
            $conn->query("UPDATE penilaian SET nilai = '$nilai_baru' WHERE id_karyawan = '$id_karyawan' AND id_kriteria = '$id_kriteria'");
        } else {
            $conn->query("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai) VALUES ('$id_karyawan', '$id_kriteria', '$nilai_baru')");
        }
    }
    $_SESSION['sukses_edit'] = "Penilaian berhasil diperbarui!";
    header("Location: ../data_penilaian.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penilaian</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5><i class="fas fa-edit"></i> Edit Penilaian - <?= $karyawan['nama_karyawan'] ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php while($k = $kriteria->fetch_assoc()): 
                    $idk = $k['id_kriteria'];
                    $nilai = $nilai_penilaian[$idk] ?? '';
                ?>
                    <div class="mb-3">
                        <label><?= $k['nama_kriteria'] ?> (<?= $k['jenis'] ?>)</label>
                        <input type="number" name="nilai[<?= $idk ?>]" value="<?= $nilai ?>" class="form-control" step="0.01" required>
                    </div>
                <?php endwhile; ?>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                <a href="../data_penilaian.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
