<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "spk_saw";

$conn = mysqli_connect("localhost", "root", "", "spk_saw");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
