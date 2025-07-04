<?php
include "koneksi.php";

// Ambil semua data dari tabel buku
$query = "SELECT * FROM nilai_karyawan";
$result = mysqli_query($koneksi, $query);

// Cek jika query gagal
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>
