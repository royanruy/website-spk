<?php
$conn = new mysqli("localhost", "root", "", "spk_saw");
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sub = $_POST['nama_sub'] ?? '';
    $nilai = $_POST['nilai'] ?? 0;
    $nama_kriteria = $_POST['nama_kriteria'] ?? '';

    $stmt = $conn->prepare("INSERT INTO sub_kriteria (nama_sub, nilai, nama_kriteria) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nama_sub, $nilai, $nama_kriteria);

    if ($stmt->execute()) {
        $id_sub = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'id_sub' => $id_sub,
            'nama_sub' => $nama_sub,
            'nilai' => $nilai,
            'nama_kriteria' => $nama_kriteria
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
}
?>