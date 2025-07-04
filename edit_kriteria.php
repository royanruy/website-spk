<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    http_response_code(500);
    exit("Koneksi gagal");
}

// ========== AMBIL DATA (GET) ==========
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM data_kriteria WHERE id_kriteria = $id");
    echo json_encode($result->fetch_assoc());
    exit;
}

// ========== UPDATE DATA (POST) ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $bobot = floatval($_POST['bobot']);
    $jenis = $_POST['jenis'];

    if (!$id || !$kode || !$nama || $bobot <= 0 || !in_array($jenis, ['benefit', 'cost'])) {
        http_response_code(400);
        exit("Data tidak valid");
    }

    $stmt = $conn->prepare("UPDATE data_kriteria SET kode_kriteria=?, nama_kriteria=?, bobot=?, jenis=? WHERE id_kriteria=?");
    $stmt->bind_param("ssdsi", $kode, $nama, $bobot, $jenis, $id);
    if ($stmt->execute()) {
        echo "Berhasil";
    } else {
        http_response_code(500);
        echo "Gagal update";
    }
    exit;
}

// Jika tidak GET/POST yang sesuai
http_response_code(405);
exit("Method tidak diizinkan");
