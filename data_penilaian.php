<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data karyawan
$karyawan = $conn->query("SELECT * FROM karyawan");

// Ambil data kriteria
$kriteria = $conn->query("SELECT * FROM data_kriteria");
$data_kriteria = [];
while ($kr = $kriteria->fetch_assoc()) {
    $data_kriteria[] = $kr;
}

// Ambil data sub-kriteria dan kelompokkan berdasarkan nama_kriteria
$sub_kriteria = $conn->query("SELECT * FROM sub_kriteria ORDER BY nilai DESC");
$map_sub = [];
while ($s = $sub_kriteria->fetch_assoc()) {
    $map_sub[$s['nama_kriteria']][] = $s;
}

// Simpan penilaian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    if ($_POST['ajax'] === 'save_penilaian') {
        $id_karyawan = $_POST['id_karyawan'];
        $nilai_data = $_POST['nilai'] ?? [];
        $conn->query("DELETE FROM penilaian WHERE id_karyawan = '{$id_karyawan}'");

        foreach ($nilai_data as $id_kriteria => $nilai) {
            if ($nilai === '' || !is_numeric($nilai)) continue;
            $nilai = floatval($nilai);
            $stmt = $conn->prepare("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $id_karyawan, $id_kriteria, $nilai);
            $stmt->execute();
            $stmt->close();
        }

        echo json_encode(['success' => true]);
        exit;
    }

    if ($_POST['ajax'] === 'get_penilaian') {
        $id_karyawan = $_POST['id_karyawan'];
        $result = $conn->query("SELECT id_kriteria, nilai FROM penilaian WHERE id_karyawan = '$id_karyawan'");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[$row['id_kriteria']] = $row['nilai'];
        }
        echo json_encode($data);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penilaian</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f0f4f8; /* Biru kalem */
  display: flex;
}

.sidebar {
  width: 250px;
  background: linear-gradient(to bottom, #004080, #002244); /* Biru navy */
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

.sidebar hr {
  border-color: rgba(255,255,255,0.3);
}

.main {
  margin-left: 250px;
  padding: 75px 30px 30px 30px;
  width: calc(100% - 250px);
}

.card {
  border: none;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
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


/* TOMBOL */
.btn-success {
  background-color: #004080;
  border: none;
  color: white;
}

.btn-success:hover {
  background-color: #003366;
}

.btn-warning {
  background-color: #f0ad4e;
  border: none;
  color: white;
}

.btn-warning:hover {
  background-color: #d99627;
}

/* MODAL HEADER */
.modal-header {
  background-color: #004080;
  color: white;
}

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


<div class="main">
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-pen"></i> Daftar Data Penilaian</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatable">
                <thead>
                    <tr><th>No</th><th>Nama Karyawan</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $karyawan->data_seek(0);
                while ($row = $karyawan->fetch_assoc()):
                    $id_k = $row['id_karyawan'];
                    $cek = $conn->query("SELECT COUNT(*) as total FROM penilaian WHERE id_karyawan = '{$id_k}'");
                    $total = $cek->fetch_assoc()['total'];
                    $btn_class = $total ? 'btn-warning' : 'btn-success';
                    $btn_icon = $total ? 'fa-edit' : 'fa-plus';
                    $btn_text = $total ? 'Edit' : 'Input';
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                        <td>
                            <button class="btn <?= $btn_class ?> btn-sm inputBtn"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPenilaian"
                                data-id="<?= $id_k ?>"
                                data-nama="<?= htmlspecialchars($row['nama_karyawan']) ?>">
                                <i class="fas <?= $btn_icon ?>"></i> <?= $btn_text ?>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Input/Edit -->
<div class="modal fade" id="modalPenilaian" tabindex="-1">
<div class="modal-dialog">
    <div class="modal-content">
        <form id="form-penilaian" method="POST">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Form Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_karyawan" id="modal-id-karyawan">
                <div class="mb-2">
                    <strong>Nama Karyawan:</strong> <span id="modal-nama-karyawan"></span>
                </div>
                <?php foreach ($data_kriteria as $kr): ?>
                    <div class="mb-2">
                        <label class="form-label"><?= htmlspecialchars($kr['nama_kriteria']) ?> (<?= $kr['jenis'] ?>)</label>
                        <select name="nilai[<?= $kr['id_kriteria'] ?>]" class="form-select" required>
                            <option value="">--Pilih Sub Kriteria--</option>
                            <?php foreach ($map_sub[$kr['nama_kriteria']] ?? [] as $sub): ?>
                                <option value="<?= $sub['nilai'] ?>"><?= htmlspecialchars($sub['nama_sub']) ?> (<?= $sub['nilai'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
            </div>
        </form>
    </div>
</div>
</div>



<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    $('#datatable').DataTable();

    document.querySelectorAll('.inputBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            document.getElementById('modal-id-karyawan').value = id;
            document.getElementById('modal-nama-karyawan').textContent = nama;

            document.querySelectorAll('#modalPenilaian select').forEach(sel => {
                sel.selectedIndex = 0;
            });

            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `ajax=get_penilaian&id_karyawan=${id}`
            })
            .then(res => res.json())
            .then(data => {
                for (const id_kriteria in data) {
                    const nilai = data[id_kriteria];
                    const select = document.querySelector(`#modalPenilaian select[name="nilai[${id_kriteria}]"]`);
                    if (select) {
                        for (let opt of select.options) {
                            if (opt.value == nilai) {
                                opt.selected = true;
                                break;
                            }
                        }
                    }
                }
            });
        });
    });

    document.getElementById('form-penilaian').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        formData.append('ajax', 'save_penilaian');

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const idKaryawan = document.getElementById('modal-id-karyawan').value;
                const btn = document.querySelector(`.inputBtn[data-id='${idKaryawan}']`);
                btn.classList.remove('btn-success');
                btn.classList.add('btn-warning');
                btn.innerHTML = '<i class="fas fa-edit"></i> Edit';
                bootstrap.Modal.getInstance(document.getElementById('modalPenilaian')).hide();
            }
        });
    });
});
</script>

</body>
</html>