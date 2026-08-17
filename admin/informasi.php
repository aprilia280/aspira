<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Kelola Informasi';
$pageSub = 'Kelola berita, pengumuman, dan informasi layanan.';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah') {
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $status = trim($_POST['status'] ?? 'Draft');
        if ($judul && $isi) {
            insert_informasi($judul, $isi, $status);
            add_log('Menambahkan informasi "' . $judul . '"');
            $msg = 'Informasi berhasil disimpan.';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $status = trim($_POST['status'] ?? 'Draft');
        if ($id && $judul && $isi) {
            update_informasi($id, $judul, $isi, $status);
            add_log('Mengubah informasi "' . $judul . '"');
            $msg = 'Informasi berhasil diperbarui.';
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        delete_informasi($id);
        add_log('Menghapus informasi (ID ' . $id . ')');
        $msg = 'Informasi berhasil dihapus.';
    }
}
$berita = get_informasi();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Informasi - ASPIRA Admin</title>
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
          <div class="card-header"><h3>Berita &amp; Pengumuman</h3></div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Judul</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
              <?php if (!$berita): ?>
                <tr><td colspan="4" style="text-align:center; color:var(--gray-500);">Belum ada informasi.</td></tr>
              <?php endif; ?>
              <?php foreach ($berita as $b): ?>
                <tr>
                  <td class="cell-strong"><?= h($b['judul']) ?></td>
                  <td class="cell-muted"><?= h(date('d M Y', strtotime($b['created_at']))) ?></td>
                  <td><span class="<?= $b['status'] === 'Tayang' ? 'badge badge-success' : 'badge badge-default' ?>"><?= h($b['status']) ?></span></td>
                  <td>
                    <div class="table-actions">
                      <button type="button" class="icon-btn" onclick='openEditInformasi(<?= json_encode($b) ?>)'><i class="fa-solid fa-pen"></i></button>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Hapus informasi ini?')">
                        <input type="hidden" name="action" value="hapus">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <button type="submit" class="icon-btn" style="color:var(--red-600);"><i class="fa-solid fa-trash"></i></button>
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
          <div class="card-header"><h3 id="formTitle">Tambah Pengumuman</h3></div>
          <form method="post" id="infoForm">
            <input type="hidden" name="action" id="formAction" value="tambah">
            <input type="hidden" name="id" id="formId" value="">
            <div class="form-group"><label>Judul</label><input type="text" name="judul" id="formJudul" placeholder="Judul pengumuman" required></div>
            <div class="form-group"><label>Isi</label><textarea name="isi" id="formIsi" placeholder="Isi pengumuman..." required></textarea></div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" id="formStatus"><option>Draft</option><option>Tayang</option></select>
            </div>
            <div style="display:flex; gap:10px;">
              <button type="submit" class="btn btn-primary btn-block" id="formSubmit">Simpan</button>
              <button type="button" class="btn btn-outline" id="formCancel" style="display:none;" onclick="resetInfoForm()">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
<script>
function openEditInformasi(b) {
  document.getElementById('formTitle').textContent = 'Edit Informasi';
  document.getElementById('formAction').value = 'edit';
  document.getElementById('formId').value = b.id;
  document.getElementById('formJudul').value = b.judul;
  document.getElementById('formIsi').value = b.isi;
  document.getElementById('formStatus').value = b.status;
  document.getElementById('formSubmit').textContent = 'Simpan Perubahan';
  document.getElementById('formCancel').style.display = 'inline-flex';
  document.getElementById('infoForm').scrollIntoView({behavior:'smooth', block:'center'});
}
function resetInfoForm() {
  document.getElementById('formTitle').textContent = 'Tambah Pengumuman';
  document.getElementById('formAction').value = 'tambah';
  document.getElementById('infoForm').reset();
  document.getElementById('formSubmit').textContent = 'Simpan';
  document.getElementById('formCancel').style.display = 'none';
}
</script>
</body>
</html>
