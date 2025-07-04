<?php
$conn = new mysqli("localhost", "root", "", "spk_saw");
$id = $_POST['id_sub'];
$nama = $_POST['nama_sub'];
$nilai = $_POST['nilai'];

$stmt = $conn->prepare("UPDATE sub_kriteria SET nama_sub = ?, nilai = ? WHERE id_sub = ?");
$stmt->bind_param("sii", $nama, $nilai, $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
