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

// Proses AJAX tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'tambah_user') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($role === 'admin') {
        $stmt = $conn->prepare("INSERT INTO admin (username, password, role, nama, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $role, $nama, $email);
    } else {
        $stmt = $conn->prepare("INSERT INTO user (username, password, nama, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $nama, $email);
    }

    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        echo json_encode([
            'status' => 'success',
            'id' => $id,
            'username' => $username,
            'nama' => $nama,
            'email' => $email,
            'role' => $role
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan user.']);
    }
    exit;
}

$admins = $conn->query("SELECT *, 'admin' as role FROM admin");
$users = $conn->query("SELECT *, 'user' as role FROM user");
$dataGabungan = [];
while ($row = $admins->fetch_assoc()) $dataGabungan[] = $row;
while ($row = $users->fetch_assoc()) $dataGabungan[] = $row;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f0f4f8; /* biru muda lembut */
  display: flex;
}

.sidebar {
  width: 250px;
  background: linear-gradient(to bottom, #004080, #002244); /* biru navy */
  color: white;
  height: 100vh;
  position: fixed;
  padding: 20px;
  box-sizing: border-box;
}

.sidebar h4 {
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

.table th, .table td {
  vertical-align: middle;
}
.table th:nth-child(1),
.table td:nth-child(1) {
  text-align: center;
}
.table th:nth-child(5),
.table td:nth-child(5) {
  text-align: center;
}

.table th:nth-child(6),
.table td:nth-child(6) {
  text-align: center;
}

        .btn-success { background-color: #00b388; border: none; }
        .modal-header { background-color: #00b388; color: white; }
    </style>
</head>
<body>

<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">ADMIN</span>
</div>

<div class="sidebar">
    <h4><i class="fas fa-database"></i> SPK SAW</h4>
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

<!-- Main Content -->
<div class="main">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-user"></i> Data User</h5>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fas fa-plus"></i> Tambah User</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php $no = 1; foreach ($dataGabungan as $u): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['nama'] ?? '-') ?></td>
        <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $u['id'] ?? $u['id_user'] ?>">
                <i class="fas fa-eye"></i> Detail
            </button>
        </td>
    </tr>

    <!-- Modal Detail User -->
    <div class="modal fade" id="modalDetail<?= $u['id'] ?? $u['id_user'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user"></i> Detail User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <?php if (!empty($u['foto'])): ?>
                        <img src="crud/<?= $u['foto'] ?>" class="img-fluid rounded-circle shadow mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Foto User">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary d-inline-block mb-3" style="width: 150px; height: 150px; line-height: 150px;">
                            <span class="text-white">No Foto</span>
                        </div>
                    <?php endif; ?>
                    <p><strong>Username:</strong> <?= htmlspecialchars($u['username']) ?></p>
                    <p><strong>Nama:</strong> <?= htmlspecialchars($u['nama'] ?? '-') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($u['email'] ?? '-') ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($u['role']) ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</tbody>

            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambahUser">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ajax" value="tambah_user">
                    <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div id="errorAlert" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    const table = $('#datatable').DataTable();

    $('#formTambahUser').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '',
            data: $(this).serialize(),
            success: function(res) {
                const json = JSON.parse(res);
                if (json.status === 'success') {
                    const index = table.rows().count() + 1;
                    table.row.add([
                        index,
                        json.username,
                        json.nama,
                        json.email,
                        json.role,
                        `<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetail${json.id}">
                            <i class="fas fa-eye"></i> Detail
                        </button>`
                    ]).draw(false);

                    $('body').append(`
                        <div class="modal fade" id="modalDetail${json.id}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-user"></i> Detail User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <div class="rounded-circle bg-secondary d-inline-block mb-3" style="width: 150px; height: 150px; line-height: 150px;">
                                            <span class="text-white">No Foto</span>
                                        </div>
                                        <p><strong>Username:</strong> ${json.username}</p>
                                        <p><strong>Nama:</strong> ${json.nama}</p>
                                        <p><strong>Email:</strong> ${json.email}</p>
                                        <p><strong>Role:</strong> ${json.role}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    $('#modalTambah').modal('hide');
                    $('#formTambahUser')[0].reset();
                    $('#errorAlert').text('');
                } else {
                    $('#errorAlert').text(json.message);
                }
            }
        });
    });
});
</script>

</body>
</html>
