<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Kelola Kategori';
$pageSub = 'Atur kategori pengaduan yang tersedia untuk masyarakat.';
$msg = '';

$iconOptions = [
    'file-text' => 'Dokumen', 'road' => 'Jalan', 'users' => 'Orang',
    'leaf' => 'Daun', 'dots' => 'Lainnya',
];
$iconFa = ['file-text' => 'fa-file-lines', 'road' => 'fa-road', 'users' => 'fa-users', 'leaf' => 'fa-leaf', 'dots' => 'fa-ellipsis'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $icon = trim($_POST['icon'] ?? 'dots');
        if ($nama) {
            insert_kategori($nama, $deskripsi, $icon);
            add_log('Menambahkan kategori "' . $nama . '"');
            $msg = 'Kategori berhasil ditambahkan.';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $icon = trim($_POST['icon'] ?? 'dots');
        if ($id && $nama) {
            update_kategori($id, $nama, $deskripsi, $icon);
            add_log('Mengubah kategori "' . $nama . '"');
            $msg = 'Kategori berhasil diperbarui.';
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        delete_kategori($id);
        add_log('Menghapus kategori (ID ' . $id . ')');
        $msg = 'Kategori berhasil dihapus.';
    }
}
$kategori = get_kategori();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Kategori - ASPIRA Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>
  <main class="admin-main">
    <?php include __DIR__ . '/../includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <?php if ($msg): ?><div class="form-success"><?= h($msg) ?></div><?php endif; ?>
      <div class="detail-grid">
        <div class="card">
          <div class="card-header"><h3>Daftar Kategori</h3></div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Ikon</th><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Pengaduan</th><th>Aksi</th></tr></thead>
              <tbody>
              <?php foreach ($kategori as $k): ?>
                <tr>
                  <td><div class="cat-icon" style="width:36px; height:36px; font-size:14px;"><i class="fa-solid <?= $iconFa[$k['icon']] ?? 'fa-circle' ?>"></i></div></td>
                  <td class="cell-strong"><?= h($k['nama']) ?></td>
                  <td class="cell-muted"><?= h($k['deskripsi']) ?></td>
                  <td><?= number_format($k['jumlah'], 0, ',', '.') ?></td>
                  <td>
                    <div class="table-actions">
                      <button type="button" class="icon-btn" title="Edit"
                        onclick='openEditKategori(<?= json_encode($k) ?>)'><i class="fa-solid fa-pen"></i></button>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Hapus kategori &quot;<?= h($k['nama']) ?>&quot;?')">
                        <input type="hidden" name="action" value="hapus">
                        <input type="hidden" name="id" value="<?= $k['id'] ?>">
                        <button type="submit" class="icon-btn" title="Hapus" style="color:var(--red-600); border:none; background:none;"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h3 id="formTitle">Tambah Kategori</h3></div>
          <form method="post" id="kategoriForm">
            <input type="hidden" name="action" id="formAction" value="tambah">
            <input type="hidden" name="id" id="formId" value="">
            <div class="form-group"><label>Nama Kategori</label><input type="text" name="nama" id="formNama" placeholder="Contoh: Kesehatan" required></div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" id="formDeskripsi" placeholder="Deskripsi singkat kategori..."></textarea></div>
            <div class="form-group">
              <label>Ikon</label>
              <select name="icon" id="formIcon">
                <?php foreach ($iconOptions as $val => $label): ?>
                <option value="<?= $val ?>"><?= h($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div style="display:flex; gap:10px;">
              <button type="submit" class="btn btn-primary btn-block" id="formSubmit">Tambah Kategori</button>
              <button type="button" class="btn btn-outline" id="formCancel" style="display:none;" onclick="resetKategoriForm()">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
<script>
function openEditKategori(k) {
  document.getElementById('formTitle').textContent = 'Edit Kategori';
  document.getElementById('formAction').value = 'edit';
  document.getElementById('formId').value = k.id;
  document.getElementById('formNama').value = k.nama;
  document.getElementById('formDeskripsi').value = k.deskripsi;
  document.getElementById('formIcon').value = k.icon;
  document.getElementById('formSubmit').textContent = 'Simpan Perubahan';
  document.getElementById('formCancel').style.display = 'inline-flex';
  document.getElementById('kategoriForm').scrollIntoView({behavior:'smooth', block:'center'});
}
function resetKategoriForm() {
  document.getElementById('formTitle').textContent = 'Tambah Kategori';
  document.getElementById('formAction').value = 'tambah';
  document.getElementById('kategoriForm').reset();
  document.getElementById('formSubmit').textContent = 'Tambah Kategori';
  document.getElementById('formCancel').style.display = 'none';
}
</script>
</body>
</html>
