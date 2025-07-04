<?php
$conn = new mysqli("localhost", "root", "", "spk_saw");
$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM sub_kriteria WHERE id_sub = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
