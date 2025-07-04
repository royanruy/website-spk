<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "spk_saw");
$kriteria = $conn->query("SELECT * FROM data_kriteria");
$sub_kriteria = $conn->query("SELECT * FROM sub_kriteria ORDER BY nilai DESC");

$sub_map = [];
while ($row = $sub_kriteria->fetch_assoc()) {
    $sub_map[$row['nama_kriteria']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Sub Kriteria</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f0f4f8; /* Biru muda kalem */
  display: flex;
}

.sidebar {
  width: 250px;
  background: linear-gradient(to bottom, #004080, #002244); /* Biru navy gradasi */
  color: white;
  height: 100vh;
  position: fixed;
  padding: 20px;
  box-sizing: border-box;
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
  padding: 75px 35px 30px 30px;
  width: calc(100% - 250px);
}

.table th {
  text-align: center;
  vertical-align: middle;
  background-color: #d6e4f5; /* Biru muda terang untuk header */
  color: #002244; /* Biru tua untuk teks header */
  font-weight: 600;
  font-size: 14px;
}

.table td {
  color: #003366; /* Teks sel tabel lebih gelap dan konsisten */
  font-size: 14px;
  vertical-align: middle;
  background-color: #ffffff; /* Putih bersih */
  text-align: center; 
}

  </style>
</head>
<body>

<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 15px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); z-index: 1000;">
  <i class="fas fa-user-circle" style="font-size: 24px; color: #555; margin-right: 10px;"></i>
  <span style="font-weight: bold; color: #333; font-size: 14px;">ADMIN</span>
</div>

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

<div class="main">
  <h4 class="mb-4" style="color:rgb(29, 26, 126);">
    <i class="fas fa-sitemap"></i> Data Sub Kriteria</h4>
  <div id="alertContainer"></div>

  <?php while ($row = $kriteria->fetch_assoc()): ?>
    <div class="card mb-3">
      <div class="card-body d-flex justify-content-between align-items-center">
        <strong class="text-success"><i class="fas fa-layer-group me-2"></i><?= htmlspecialchars($row['nama_kriteria']) ?></strong>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal" onclick="setKriteria('<?= htmlspecialchars($row['nama_kriteria'], ENT_QUOTES) ?>')">
          <i class="fas fa-plus"></i> Tambah Data
        </button>
      </div>
    </div>
    <table class="table table-bordered table-hover bg-white mb-4" data-kriteria="<?= htmlspecialchars($row['nama_kriteria'], ENT_QUOTES) ?>">
      <thead class="table-success">
        <tr><th>No</th><th>Nama Sub</th><th>Nilai</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php
          $sublist = $sub_map[$row['nama_kriteria']] ?? [];
          if ($sublist):
            $no = 1;
            foreach ($sublist as $sub): ?>
              <tr id="baris<?= $sub['id_sub'] ?>">
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($sub['nama_sub']) ?></td>
                <td><?= $sub['nilai'] ?></td>
                <td>
                  <button class="btn btn-warning btn-sm" onclick="showEditModal(<?= $sub['id_sub'] ?>, '<?= htmlspecialchars($sub['nama_sub'], ENT_QUOTES) ?>', <?= $sub['nilai'] ?>)"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-danger btn-sm" onclick="hapusSub(<?= $sub['id_sub'] ?>)"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
        <?php endforeach; else: ?>
          <tr class="kosong"><td colspan="4">Belum ada data</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  <?php endwhile; ?>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formTambah" onsubmit="event.preventDefault(); simpanSub()">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Sub Kriteria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="nama_kriteria" id="nama_kriteria_input">
          <div class="mb-3">
            <label>Nama Sub Kriteria</label>
            <input type="text" name="nama_sub" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Nilai</label>
            <input type="number" name="nilai" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function setKriteria(nama) {
  document.getElementById('nama_kriteria_input').value = nama;
}

function tampilkanAlert(pesan, tipe = 'success') {
  document.getElementById("alertContainer").innerHTML = `
    <div class="alert alert-${tipe} alert-dismissible fade show" role="alert">
      ${pesan}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
}

function simpanSub() {
  const form = document.getElementById('formTambah');
  const formData = new FormData(form);

  fetch('crud/tambah_subkriteria.php', {
    method: 'POST',
    body: formData
  }).then(res => res.json()).then(res => {
    if (res.status === 'success') {
      const tabel = document.querySelector(`table[data-kriteria='${res.nama_kriteria}'] tbody`);
      const kosongRow = tabel.querySelector('.kosong');
      if (kosongRow) kosongRow.remove();
      const no = tabel.rows.length + 1;
      const tr = document.createElement('tr');
      tr.id = 'baris' + res.id_sub;
      tr.innerHTML = `
        <td>${no}</td>
        <td>${res.nama_sub}</td>
        <td>${res.nilai}</td>
        <td>
          <button class='btn btn-warning btn-sm' onclick='showEditModal(${res.id_sub}, "${res.nama_sub}", ${res.nilai})'><i class='fas fa-edit'></i></button>
          <button class='btn btn-danger btn-sm' onclick='hapusSub(${res.id_sub})'><i class='fas fa-trash'></i></button>
        </td>
      `;
      tabel.appendChild(tr);
      tampilkanAlert('Data berhasil ditambahkan!');
      form.reset();
      bootstrap.Modal.getInstance(document.getElementById('tambahModal')).hide();
    } else {
      tampilkanAlert('Gagal tambah: ' + res.message, 'danger');
    }
  });
}

function showEditModal(id, nama, nilai) {
  const modalHtml = `
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <form onsubmit="event.preventDefault(); editSub(${id})">
          <div class="modal-content">
            <div class="modal-header bg-warning">
              <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Sub Kriteria</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label>Nama Sub</label>
                <input type="text" class="form-control" id="nama_sub${id}" value="${nama}" required>
              </div>
              <div class="mb-3">
                <label>Nilai</label>
                <input type="number" class="form-control" id="nilai${id}" value="${nilai}" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-warning">Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>`;
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = new bootstrap.Modal(document.getElementById('editModal'));
  modal.show();
  document.getElementById('editModal').addEventListener('hidden.bs.modal', () => {
    document.getElementById('editModal').remove();
  });
}

function editSub(id) {
  const nama = document.getElementById('nama_sub' + id).value;
  const nilai = document.getElementById('nilai' + id).value;

  const data = new URLSearchParams();
  data.append('id_sub', id);
  data.append('nama_sub', nama);
  data.append('nilai', nilai);

  fetch('crud/edit_subkriteria.php', {
    method: 'POST',
    body: data
  }).then(res => res.json()).then(res => {
    if (res.status === 'success') {
      const tr = document.getElementById('baris' + id);
      tr.children[1].textContent = nama;
      tr.children[2].textContent = nilai;
      tampilkanAlert('Data berhasil diubah!');
      bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
    } else {
      tampilkanAlert('Gagal edit: ' + res.message, 'danger');
    }
  });
}

function hapusSub(id) {
  if (confirm("Yakin ingin menghapus?")) {
    const data = new URLSearchParams();
    data.append('id', id);

    fetch('crud/hapus_subkriteria.php', {
      method: 'POST',
      body: data
    }).then(res => res.json()).then(res => {
      if (res.status === 'success') {
        const row = document.getElementById('baris' + id);
        const tbody = row.parentElement;
        row.remove();
        if (tbody.children.length === 0) {
          const kosongRow = document.createElement('tr');
          kosongRow.classList.add('kosong');
          kosongRow.innerHTML = '<td colspan="4">Belum ada data</td>';
          tbody.appendChild(kosongRow);
        }
        tampilkanAlert('Data berhasil dihapus!', 'danger');
      } else {
        tampilkanAlert('Gagal hapus: ' + res.message, 'danger');
      }
    });
  }
}
</script>
</body>
</html>