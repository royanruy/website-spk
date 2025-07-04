<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: halaman_utama.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan parameter id ada dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Cek apakah user ada
    $cek = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
    $cek->bind_param("i", $id);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        // Hapus user
        $hapus = $conn->prepare("DELETE FROM user WHERE id_user = ?");
        $hapus->bind_param("i", $id);
        $hapus->execute();

        echo "<script>alert('User berhasil dihapus!'); window.location='../data_user.php';</script>";
    } else {
        echo "<script>alert('User tidak ditemukan.'); window.location='../data_user.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid.'); window.location='../data_user.php';</script>";
}
?>
