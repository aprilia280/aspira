<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Kelola Pengguna';
$pageSub = 'Kelola akun pengguna sistem.';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'Petugas');
        if ($nama && $email && !find_user_by_email($email)) {
            insert_user($nama, $email, 'ganti123', $role, 'Aktif');
            add_log('Menambahkan pengguna baru: ' . $nama);
            $msg = 'Pengguna baru berhasil ditambahkan. Password default: ganti123';
        } else {
            $msg = 'Email sudah digunakan atau data belum lengkap.';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'Petugas');
        if ($id && $nama && $email) {
            update_user($id, $nama, $email, $role);
            add_log('Mengubah data pengguna: ' . $nama);
            $msg = 'Pengguna berhasil diperbarui.';
        }
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        toggle_user_status($id);
        add_log('Mengubah status pengguna (ID ' . $id . ')');
        $msg = 'Status pengguna berhasil diperbarui.';
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        delete_user($id);
        add_log('Menghapus pengguna (ID ' . $id . ')');
        $msg = 'Pengguna berhasil dihapus.';
    }
}
$users = get_users();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pengguna - ASPIRA Admin</title>
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
      <div class="card">
        <div class="card-header">
          <div>
            <h3>Kelola Pengguna</h3>
            <p>Kelola akun pengguna sistem.</p>
          </div>
          <button type="button" class="btn btn-primary btn-sm" onclick="openTambahModal()"><i class="fa-solid fa-plus"></i> Tambah Pengguna</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td class="cell-strong"><?= h($u['nama']) ?></td>
                <td class="cell-muted"><?= h($u['email']) ?></td>
                <td><?= h($u['role']) ?></td>
                <td><span class="<?= $u['status'] === 'Aktif' ? 'badge badge-success' : 'badge badge-danger' ?>"><?= h($u['status']) ?></span></td>
                <td>
                  <div class="table-actions">
                    <button type="button" class="icon-btn" title="Edit" onclick='openEditModal(<?= json_encode($u) ?>)'><i class="fa-solid fa-pen"></i></button>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="action" value="toggle">
                      <input type="hidden" name="id" value="<?= $u['id'] ?>">
                      <button type="submit" class="icon-btn" title="Ubah status"><i class="fa-solid fa-toggle-on"></i></button>
                    </form>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Hapus pengguna <?= h($u['nama']) ?>?')">
                      <input type="hidden" name="action" value="hapus">
                      <input type="hidden" name="id" value="<?= $u['id'] ?>">
                      <button type="submit" class="icon-btn" title="Hapus" style="color:var(--red-600);"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<div id="tambahModal" style="display:none; position:fixed; inset:0; background:rgba(16,26,51,.45); align-items:center; justify-content:center; z-index:200;">
  <div class="card" style="width:100%; max-width:400px;">
    <div class="card-header">
      <h3 id="modalTitle">Tambah Pengguna</h3>
      <button type="button" onclick="closeModal()" class="icon-btn"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form method="post" id="userForm">
      <input type="hidden" name="action" id="modalAction" value="tambah">
      <input type="hidden" name="id" id="modalId" value="">
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" id="modalNama" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" id="modalEmail" required></div>
      <div class="form-group">
        <label>Role</label>
        <select name="role" id="modalRole">
          <option>Petugas</option>
          <option>Verifikator</option>
          <option>Super Admin</option>
        </select>
      </div>
      <p class="form-hint" id="modalHint">Password default untuk akun baru: <strong>ganti123</strong></p>
      <button type="submit" class="btn btn-primary btn-block" id="modalSubmit">Simpan Pengguna</button>
    </form>
  </div>
</div>

<script src="../assets/js/admin.js"></script>
<script>
function openTambahModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
  document.getElementById('modalAction').value = 'tambah';
  document.getElementById('userForm').reset();
  document.getElementById('modalId').value = '';
  document.getElementById('modalHint').style.display = 'block';
  document.getElementById('modalSubmit').textContent = 'Simpan Pengguna';
  document.getElementById('tambahModal').style.display = 'flex';
}
function openEditModal(u) {
  document.getElementById('modalTitle').textContent = 'Edit Pengguna';
  document.getElementById('modalAction').value = 'edit';
  document.getElementById('modalId').value = u.id;
  document.getElementById('modalNama').value = u.nama;
  document.getElementById('modalEmail').value = u.email;
  document.getElementById('modalRole').value = u.role;
  document.getElementById('modalHint').style.display = 'none';
  document.getElementById('modalSubmit').textContent = 'Simpan Perubahan';
  document.getElementById('tambahModal').style.display = 'flex';
}
function closeModal() { document.getElementById('tambahModal').style.display = 'none'; }
</script>
</body>
</html>
