<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../halaman_utama.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data username dari admin dan user
$usernames = [];

$admins = $conn->query("SELECT username FROM admin");
while ($row = $admins->fetch_assoc()) {
    $usernames[] = ['username' => $row['username'], 'role' => 'admin'];
}

$users = $conn->query("SELECT username FROM user");
while ($row = $users->fetch_assoc()) {
    $usernames[] = ['username' => $row['username'], 'role' => 'user'];
}

// Proses AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $table = ($role === 'user') ? 'user' : 'admin';

    if ($_POST['aksi'] === 'get') {
        $username = $_POST['username'];
        $q = $conn->query("SELECT nama, email FROM $table WHERE username = '$username'");
        $data = $q->fetch_assoc();
        echo json_encode($data);
        exit;
    } elseif ($_POST['aksi'] === 'update') {
        $username = $_POST['username'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Upload foto
        $fotoPath = null;
        if (!empty($_FILES['foto']['name'])) {
            $dir = "uploads/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $namaFile = time() . "_" . preg_replace("/[^a-zA-Z0-9\._]/", "_", basename($_FILES["foto"]["name"]));
            $targetFile = $dir . $namaFile;
            $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                if ($_FILES['foto']['error'] === 0 && move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
                    $fotoPath = $targetFile;
                }
            }
        }

        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            if ($fotoPath) {
                $stmt = $conn->prepare("UPDATE $table SET nama=?, email=?, password=?, foto=? WHERE username=?");
                $stmt->bind_param("sssss", $nama, $email, $hashed, $fotoPath, $username);
            } else {
                $stmt = $conn->prepare("UPDATE $table SET nama=?, email=?, password=? WHERE username=?");
                $stmt->bind_param("ssss", $nama, $email, $hashed, $username);
            }
        } else {
            if ($fotoPath) {
                $stmt = $conn->prepare("UPDATE $table SET nama=?, email=?, foto=? WHERE username=?");
                $stmt->bind_param("ssss", $nama, $email, $fotoPath, $username);
            } else {
                $stmt = $conn->prepare("UPDATE $table SET nama=?, email=? WHERE username=?");
                $stmt->bind_param("sss", $nama, $email, $username);
            }
        }

        echo $stmt->execute() ? 'sukses' : 'gagal';
        exit;
    } elseif ($_POST['aksi'] === 'hapus') {
        $username = $_POST['username'];
        $stmt = $conn->prepare("DELETE FROM $table WHERE username = ?");
        $stmt->bind_param("s", $username);
        echo $stmt->execute() ? 'sukses' : 'gagal';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #004080, #002244);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
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
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: auto;
            background-color: #ffffff;
        }
        #notif {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">ADMIN</span>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h4><i class="fas fa-database"></i> SPK SAW</h4>
    <ul>
        <li><a href="../halaman_utama.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="../data_kriteria.php"><i class="fas fa-cube"></i> Data Kriteria</a></li>
        <li><a href="../data_subkriteria.php"><i class="fas fa-sitemap"></i> Data Sub Kriteria</a></li>
        <li><a href="../data_karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
        <li><a href="../data_penilaian.php"><i class="fas fa-pen"></i> Data Penilaian</a></li>
        <li><a href="../data_perhitungan.php"><i class="fas fa-calculator"></i> Data Perhitungan</a></li>
        <li><a href="../hasil_akhir.php"><i class="fas fa-chart-bar"></i> Hasil Akhir</a></li>
        <hr>
        <li><a href="../data_profil.php"><i class="fas fa-user"></i> Profile</a></li>
        <li><a href="../crud/edit_profil.php"><i class="fas fa-id-badge"></i> Update Profile</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">
    <div class="card p-4">
        <h4 class="mb-3"><i class="fas fa-user-edit text-success"></i> Edit Profil</h4>
        <div id="notif"></div>
        <form id="formUpdate" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="update">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Pengguna</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <select name="username" id="username" class="form-control" required>
                        <option value="">Pilih Username</option>
                        <?php foreach ($usernames as $u): ?>
                            <option value="<?= $u['username'] ?>" data-role="<?= $u['role'] ?>">
                                <?= $u['role'] ?> - <?= $u['username'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                <button type="button" class="btn btn-danger" id="btnHapus"><i class="fas fa-trash"></i> Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function () {
    $('#username').on('change', function () {
        let role = $('#username option:selected').data('role');
        $('#role').val(role);
        let user = $(this).val();

        if (user && role) {
            $.post('', { aksi: 'get', username: user, role: role }, function (res) {
                let data = JSON.parse(res);
                $('#email').val(data.email || '');
                $('#nama').val(data.nama || '');
                $('input[name="password"]').val('');
                $('input[name="foto"]').val('');
            });
        } else {
            $('#formUpdate')[0].reset();
        }
    });

    $('#formUpdate').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('role', $('#role').val());

        $.ajax({
            type: 'POST',
            url: '',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res === 'sukses') {
                    $('#notif').html('<div class="alert alert-success">Data berhasil diupdate.</div>');
                    $('#formUpdate')[0].reset();
                    $('#username').val('');
                    $('#role').val('');
                } else {
                    $('#notif').html('<div class="alert alert-danger">Gagal update: ' + res + '</div>');
                }
            }
        });
    });

    $('#btnHapus').on('click', function () {
        let user = $('#username').val();
        let role = $('#role').val();
        if (user && role && confirm("Yakin ingin menghapus user ini?")) {
            $.post('', { aksi: 'hapus', username: user, role: role }, function (res) {
                if (res === 'sukses') {
                    $('#notif').html('<div class="alert alert-success">User berhasil dihapus.</div>');
                    $('#username option:selected').remove();
                    $('#formUpdate')[0].reset();
                    $('#username').val('');
                    $('#role').val('');
                } else {
                    $('#notif').html('<div class="alert alert-danger">Gagal menghapus user.</div>');
                }
            });
        }
    });
});
</script>

</body>
</html>
