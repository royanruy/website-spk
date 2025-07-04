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

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Kriteria</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f0f4f8; /* biru muda kalem */
  display: flex;
}

.sidebar {
  width: 250px;
  background: linear-gradient(to bottom, #004080, #002244); /* biru gelap */
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
  padding: 75px 30px 30px 30px;
  width: calc(100% - 250px);
}

/* CARD */
.card {
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

/* TABEL */
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
  text-align: center; /* tambahkan ini */
}


  </style>
</head>
<body>

<!-- Navbar Atas -->
<div style="position: fixed; top: 0; left: 250px; right: 0; height: 50px; background: white; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000;">
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
  <h4 class="mb-4"><i class="fas fa-cube"></i> Data Kriteria</h4>
  

  <!-- Tombol Tambah -->
  <button id="showFormBtn" class="btn btn-primary mb-3">
      <i class="fas fa-plus"></i> Tambah Data
  </button>

  <!-- Form Tambah -->
  <div id="formTambah" class="card mb-4" style="display: none;">
    <div class="card-body">
      <h5>Form Tambah Kriteria</h5>
      <form id="formKriteria">
        <div class="mb-3">
          <label>Kode Kriteria</label>
          <input type="text" class="form-control" name="kode" required>
        </div>
        <div class="mb-3">
          <label>Nama Kriteria</label>
          <input type="text" class="form-control" name="nama" required>
        </div>
        <div class="mb-3">
          <label>Bobot</label>
          <input type="number" step="0.01" class="form-control" name="bobot" required>
        </div>
        <div class="mb-3">
          <label>Jenis</label>
          <select class="form-control" name="jenis" required>
            <option value="">-- Pilih --</option>
            <option value="benefit">Benefit</option>
            <option value="cost">Cost</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
      </form>
    </div>
  </div>

  <!-- Tabel Kriteria dalam Card -->
<div class="card shadow-sm mb-4">
  <!-- Header Card -->
  <div class="card-header bg-primary text-white">
    <i class="fas fa-table"></i> Daftar Data Kriteria
  </div>

  <!-- Dropdown Show Entries -->
  <div class="px-3 py-2 d-flex align-items-center">
    <label class="me-2 mb-0">Show</label>
    <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
      <option value="5">5</option>
      <option value="10" selected>10</option>
      <option value="25">25</option>
      <option value="50">50</option>
    </select>
    <span class="ms-2">entries</span>
  </div>

  <!-- Isi Tabel -->
  <div class="card-body" id="tabelKriteria">
    <?php $result = $conn->query("SELECT * FROM data_kriteria"); ?>
    <table class="table table-bordered table-striped table-kriteria">
      <thead class="table-success">
        <tr>
          <th>Kode</th><th>Nama</th><th>Bobot</th><th>Jenis</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['kode_kriteria']) ?></td>
          <td><?= htmlspecialchars($row['nama_kriteria']) ?></td>
          <td><?= $row['bobot'] ?></td>
          <td><?= ucfirst($row['jenis']) ?></td>
          <td>
            <button class="btn btn-warning btn-sm btnEdit" data-id="<?= $row['id_kriteria'] ?>">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-sm btnDelete" data-id="<?= $row['id_kriteria'] ?>">
              <i class="fas fa-trash-alt"></i>
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center px-2 pb-2" id="customTableInfo">
  <div class="text-muted small" id="showingInfo">Showing 0 to 0 of 0 entries</div>
  <nav>
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
    </ul>
  </nav>
</div>

  </div>
</div>


<!-- Modal Edit Kriteria -->
<div class="modal fade" id="modalEditKriteria" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditKriteria" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel"><i class="fas fa-edit"></i> Edit Kriteria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_id" name="id">
        <div class="mb-3">
          <label>Kode Kriteria</label>
          <input type="text" name="kode" id="edit_kode" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Nama Kriteria</label>
          <input type="text" name="nama" id="edit_nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Bobot</label>
          <input type="number" step="0.01" name="bobot" id="edit_bobot" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Jenis</label>
          <select name="jenis" id="edit_jenis" class="form-control" required>
            <option value="benefit">Benefit</option>
            <option value="cost">Cost</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#showFormBtn').click(() => $('#formTambah').slideDown());
  $('#cancelBtn').click(() => {
    $('#formTambah').slideUp();
    $('#formKriteria')[0].reset();
  });

  $('#formKriteria').on('submit', function(e) {
    e.preventDefault();
    $.post('crud/tambah_kriteria.php', $(this).serialize(), function() {
      alert("Data berhasil ditambahkan!");
      $('#formTambah').slideUp();
      $('#formKriteria')[0].reset();
      $('#tabelKriteria').load(location.href + ' #tabelKriteria>*', "");
    }).fail(() => alert("Gagal menyimpan data."));
  });

  $(document).on('click', '.btnEdit', function() {
    const id = $(this).data('id');
    $.get('crud/edit_kriteria.php', { id }, function(data) {
      const d = JSON.parse(data);
      $('#edit_id').val(d.id_kriteria);
      $('#edit_kode').val(d.kode_kriteria);
      $('#edit_nama').val(d.nama_kriteria);
      $('#edit_bobot').val(d.bobot);
      $('#edit_jenis').val(d.jenis);
      $('#modalEditKriteria').modal('show');
    });
  });

  $(document).on('click', '.btnDelete', function () {
  const id = $(this).data('id');
  if (confirm('Yakin ingin menghapus data ini?')) {
    $.ajax({
      url: 'crud/hapus_kriteria.php',
      type: 'POST',
      data: { id: id },
      success: function () {
        alert('Data berhasil dihapus!');
        $('#tabelKriteria').load(location.href + ' #tabelKriteria>*', "");
      },
      error: function () {
        alert('Gagal menghapus data!');
      }
    });
  }
});

  $('#formEditKriteria').on('submit', function(e) {
    e.preventDefault();
    $.post('crud/edit_kriteria.php', $(this).serialize(), function() {
      alert("Data berhasil diperbarui!");
      $('#modalEditKriteria').modal('hide');
      $('#tabelKriteria').load(location.href + ' #tabelKriteria>*', "");
    }).fail(() => alert("Gagal memperbarui data."));
  });
});
</script>
<script>
$(document).ready(function () {
  function filterTable(limit) {
    const rows = $(".table-kriteria tbody tr");
    const total = rows.length;
    rows.hide();

    const start = total === 0 ? 0 : 1;
    const end = Math.min(limit, total);

    rows.slice(0, limit).show();

    // Update teks "Showing X to Y of Z entries"
    $("#showingInfo").text(`Showing ${start} to ${end} of ${total} entries`);
  }

  // Inisialisasi
  filterTable($("#entriesSelect").val());

  // Saat dropdown berubah
  $("#entriesSelect").on("change", function () {
    filterTable($(this).val());
  });
});

</script>

</body>
</html>
