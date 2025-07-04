<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "spk_saw");
    if ($conn->connect_error) {
        http_response_code(500);
        echo "Koneksi gagal!";
        exit;
    }

    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $bobot = $_POST['bobot'];
    $jenis = $_POST['jenis'];

    $stmt = $conn->prepare("INSERT INTO data_kriteria (kode_kriteria, nama_kriteria, bobot, jenis) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $kode, $nama, $bobot, $jenis);

    if ($stmt->execute()) {
        echo "sukses";
    } else {
        http_response_code(500);
        echo "Gagal menambah data!";
    }

    $stmt->close();
    $conn->close();
}
?>
