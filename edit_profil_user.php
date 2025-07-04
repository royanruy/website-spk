<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../alaman_user.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$listUsernames = $conn->query("SELECT username FROM user");

// AJAX: ambil data user
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_user' && isset($_GET['username'])) {
    $username = $_GET['username'];
    $result = $conn->query("SELECT email, nama FROM user WHERE username = '$username'");
    $data = $result->fetch_assoc();
    echo json_encode($data);
    exit;
}

// AJAX: update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'update_user') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    $fotoPath = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $fileName = time() . "_" . basename($_FILES['foto']['name']);
        $targetFile = $targetDir . $fileName;
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);
            $fotoPath = $targetFile;
        }
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        if ($fotoPath) {
            $stmt = $conn->prepare("UPDATE user SET email=?, nama=?, password=?, foto=? WHERE username=?");
            $stmt->bind_param("sssss", $email, $nama, $hashed, $fotoPath, $username);
        } else {
            $stmt = $conn->prepare("UPDATE user SET email=?, nama=?, password=? WHERE username=?");
            $stmt->bind_param("ssss", $email, $nama, $hashed, $username);
        }
    } else {
        if ($fotoPath) {
            $stmt = $conn->prepare("UPDATE user SET email=?, nama=?, foto=? WHERE username=?");
            $stmt->bind_param("ssss", $email, $nama, $fotoPath, $username);
        } else {
            $stmt = $conn->prepare("UPDATE user SET email=?, nama=? WHERE username=?");
            $stmt->bind_param("sss", $email, $nama, $username);
        }
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #ffffff, #ffffff);
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
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: auto;
            background-color: rgb(241, 241, 241);
            color: black;
        }
    </style>
</head>
<body>
<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
    <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
    <span style="font-weight: bold; color: #333; font-size: 14px;">USER</span>
</div>

<div class="sidebar">
    <h4><i class="fas fa-database"></i> SPK SAW</h4>
    <ul>
        <li><a href="../halaman_user.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="../hasil_akhir_user.php"><i class="fas fa-chart-bar"></i> Hasil Akhir</a></li>
        <hr>
        <li><a href="../crud/edit_profil_user.php"><i class="fas fa-id-badge"></i> Update Profile</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="card p-4">
        <h4 class="mb-3"><i class="fas fa-user-edit text-darkblue"></i> Edit Profil</h4>
        <form id="formProfil" enctype="multipart/form-data">
            <input type="hidden" name="ajax" value="update_user">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <select name="username" id="username" class="form-control" required>
                        <option value="">Pilih Username</option>
                        <?php while ($row = $listUsernames->fetch_assoc()): ?>
                            <option value="<?= $row['username'] ?>"><?= $row['username'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Foto</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success me-2"><i class="fas fa-save"></i> Update</button>
                <button type="reset" class="btn btn-info"><i class="fas fa-rotate-left"></i> Reset</button>
            </div>
            <div id="notif" class="mt-3"></div>
        </form>
    </div>
</div>

<script>
    $('#username').on('change', function () {
        var username = $(this).val();
        if (username !== '') {
            $.get('', { ajax: 'get_user', username: username }, function (res) {
                let data = JSON.parse(res);
                $('#email').val(data.email || '');
                $('#nama').val(data.nama || '');
            });
        } else {
            $('#email').val('');
            $('#nama').val('');
        }
    });

    $('#formProfil').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                let data = JSON.parse(res);
                if (data.status === 'success') {
                    $('#notif').html('<div class="alert alert-success">Berhasil diperbarui.</div>');
                    setTimeout(() => {
                        $('#formProfil')[0].reset();
                        $('#username').val('');
                    }, 1500);
                } else {
                    $('#notif').html('<div class="alert alert-danger">Gagal memperbarui data.</div>');
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
