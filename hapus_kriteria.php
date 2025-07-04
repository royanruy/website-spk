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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM penilaian WHERE id_kriteria = $id");
    $conn->query("DELETE FROM data_kriteria WHERE id_kriteria = $id");
    echo "deleted";
    exit;
}
http_response_code(405);
