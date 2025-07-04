<?php
session_start();
$conn = new mysqli("localhost", "root", "", "spk_saw");
if (!isset($_SESSION['username'])) {
    header("Location: halaman_utama.php");
    exit;
}
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// === Handle AJAX request ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    if ($_POST['aksi'] === 'tambah') {
        $nama = $conn->real_escape_string($_POST['nama_karyawan']);
        $conn->query("INSERT INTO karyawan (nama_karyawan) VALUES ('$nama')");
        exit;
    } elseif ($_POST['aksi'] === 'edit') {
        $id = (int)$_POST['id'];
        $nama = $conn->real_escape_string($_POST['nama_karyawan']);
        $conn->query("UPDATE karyawan SET nama_karyawan = '$nama' WHERE id_karyawan = $id");
        exit;
    } elseif ($_POST['aksi'] === 'hapus') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM karyawan WHERE id_karyawan = $id");
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get'])) {
    $id = (int)$_GET['get'];
    $data = $conn->query("SELECT * FROM karyawan WHERE id_karyawan = $id")->fetch_assoc();
    echo json_encode($data);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['load'])) {
    $result = $conn->query("SELECT * FROM karyawan");
    $no = 1;
    ?>
    <table id="datatable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                <td class="aksi">
                    <button class="btnEdit edit-btn" data-id="<?= $row['id_karyawan'] ?>"><i class="fas fa-edit"></i></button>
                    <button class="btnDelete hapus-btn" data-id="<?= $row['id_karyawan'] ?>"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f0f4f8; /* Biru muda kalem */
}

.sidebar {
  width: 250px;
  background: linear-gradient(to bottom, #004080, #002244); /* Biru navy gradasi */
  color: white;
  height: 100vh;
  position: fixed;
  padding: 20px;
}

.sidebar h2 {
  font-size: 18px;
  margin-bottom: 30px;
}

.sidebar ul {
  list-style: none;
  padding: 0;
}

.sidebar ul li {
  margin-bottom: 15px;
}

.sidebar ul li a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  font-size: 14px;
}

.sidebar ul li a i {
  margin-right: 10px;
}

.main {
  margin-left: 250px;
  padding: 75px 30px 30px 30px;
  width: calc(100% - 250px);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h1 {
  font-size: 24px;
  color: #002244; /* Warna teks biru gelap */
}

.header button {
  background:rgb(43, 143, 244); /* Tombol biru navy */
  color: white;
  padding: 10px 15px;
  border-radius: 5px;
  border: none;
  transition: background 0.2s ease;
}

.header button:hover {
  background: #002f66; /* Biru navy lebih gelap saat hover */
}

.table {
  background-color: #ffffff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.table th {
  background-color: #d6e4f5; /* biru muda lembut */
  font-weight: 600;
  color: #002244; /* biru tua */
  font-size: 14px;
}

.table td {
  font-size: 14px;
  color: #333; /* teks biasa */
}
.table th:nth-child(1),
.table th:nth-child(3),
.table td:nth-child(1),
.table td:nth-child(3) {
  text-align: center;
}

.table th:nth-child(2),
.table td:nth-child(2) {
  text-align: center;
}
.aksi button { 
    margin: 0 5px; 
    color: white; 
    padding: 5px 10px; 
    border: none; 
    border-radius: 5px; 
}

.edit-btn { 
    background-color: #ffc107; 
    
}

.hapus-btn { 
    background-color: #dc3545; 
}
    </style>
</head>
<body>

<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">ADMIN</span>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="fas fa-database"></i> SPK SAW</h2>
    <ul>
        <li><a href="halaman_utama.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="data_kriteria.php"><i class="fas fa-cube"></i> Data Kriteria</a></li>
        <li><a href="data_subkriteria.php"><i class="fas fa-sitemap"></i> Data Sub Kriteria</a></li>
        <li><a href="data_karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
        <li><a href="data_penilaian.php"><i class="fas fa-pen"></i> Data Penilaian</a></li>
        <li><a href="data_perhitungan.php"><i class="fas fa-calculator"></i> Data Perhitungan</a></li>
        <li><a href="hasil_akhir.php"><i class="fas fa-chart-bar"></i> Hasil Akhir</a></li>
        <hr>
        <li><a href="data_profil.php"><i class="fas fa-user"></i> Profile</a></li>
        <li><a href="crud/edit_profil.php"><i class="fas fa-id-badge"></i> Update Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main -->
<div class="main">
    <div class="header mb-3">
        <h1>Data Karyawan</h1>
        <button data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus"></i> Tambah Data</button>
    </div>

    <div id="tabelKaryawan"></div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form id="formTambah" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Karyawan</label>
                    <input type="text" class="form-control" name="nama_karyawan" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Nama Karyawan</label>
                    <input type="text" class="form-control" name="nama_karyawan" id="edit_nama" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
function loadData() {
    $('#tabelKaryawan').load('data_karyawan.php?load=1', function () {
        $('#datatable').DataTable();
    });
}

$(document).ready(function () {
    loadData();

    $('#formTambah').submit(function (e) {
        e.preventDefault();
        $.post('data_karyawan.php', $(this).serialize() + '&aksi=tambah', function () {
            $('#modalTambah').modal('hide');
            $('#formTambah')[0].reset();
            loadData();
        });
    });

    $(document).on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        $.get('data_karyawan.php', { get: id }, function (data) {
            const json = JSON.parse(data);
            $('#edit_id').val(json.id_karyawan);
            $('#edit_nama').val(json.nama_karyawan);
            $('#modalEdit').modal('show');
        });
    });

    $('#formEdit').submit(function (e) {
        e.preventDefault();
        $.post('data_karyawan.php', $(this).serialize() + '&aksi=edit', function () {
            $('#modalEdit').modal('hide');
            loadData();
        });
    });

    $(document).on('click', '.btnDelete', function () {
        const id = $(this).data('id');
        if (confirm('Yakin ingin menghapus?')) {
            $.post('data_karyawan.php', { id: id, aksi: 'hapus' }, function () {
                loadData();
            });
        }
    });
});
</script>
</body>
</html>
