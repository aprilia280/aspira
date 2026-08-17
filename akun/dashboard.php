<?php
require_once __DIR__ . '/../includes/functions.php';
require_user_login();

$user = current_user();
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profil') {
        $nama = trim($_POST['nama'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        if ($nama) {
            update_profile_self($user['id'], $nama, $telepon);
            $user['nama'] = $nama;
            $user['telepon'] = $telepon;
            $_SESSION['user'] = $user;
            $msg = 'Profil berhasil diperbarui.';
        } else {
            $error = 'Nama tidak boleh kosong.';
        }
    } elseif ($action === 'password') {
        $lama = $_POST['password_lama'] ?? '';
        $baru = $_POST['password_baru'] ?? '';
        $konfirmasi = $_POST['password_konfirmasi'] ?? '';
        $fullUser = find_user_by_id($user['id']);
        if (!password_verify($lama, $fullUser['password'])) {
            $error = 'Password lama tidak sesuai.';
        } elseif (strlen($baru) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } elseif ($baru !== $konfirmasi) {
            $error = 'Konfirmasi password baru tidak sama.';
        } else {
            update_password_self($user['id'], $baru);
            $msg = 'Password berhasil diubah.';
        }
    }
}

$riwayat = get_pengaduan(['email' => $user['email']]);
$pageTitle = 'Dashboard Saya';
include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <h2 style="margin-bottom:2px;">Halo, <?= h($user['nama']) ?> 👋</h2>
    <p style="color:var(--gray-500); font-size:13.5px; margin-bottom:20px;">Kelola profil Anda dan pantau riwayat pengaduan yang pernah diajukan.</p>

    <?php if ($msg): ?><div class="form-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="form-error"><?= h($error) ?></div><?php endif; ?>

    <div class="detail-grid">
      <div class="card">
        <div class="card-header"><h3>Riwayat Pengaduan Saya</h3></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>No. Tiket</th><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (!$riwayat): ?>
              <tr><td colspan="6" style="text-align:center; color:var(--gray-500);">Anda belum pernah mengajukan pengaduan.</td></tr>
            <?php endif; ?>
            <?php foreach ($riwayat as $p): ?>
              <tr>
                <td class="cell-strong"><?= h($p['no_tiket']) ?></td>
                <td><?= h($p['judul']) ?></td>
                <td><?= h($p['kategori']) ?></td>
                <td class="cell-muted"><?= h(date('d M Y', strtotime($p['tanggal']))) ?></td>
                <td><span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span></td>
                <td><a href="<?= base_url('cek-pengaduan-detail.php?tiket=' . urlencode($p['no_tiket'])) ?>" class="btn btn-ghost btn-sm">Lihat</a></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <a href="<?= base_url('ajukan-pengaduan.php') ?>" class="btn btn-primary btn-sm" style="margin-top:14px;"><i class="fa-solid fa-plus"></i> Ajukan Pengaduan Baru</a>
      </div>

      <div>
        <div class="card">
          <div class="card-header"><h3>Profil Saya</h3></div>
          <form method="post">
            <input type="hidden" name="action" value="profil">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" value="<?= h($user['nama']) ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" value="<?= h($user['email']) ?>" disabled style="background:var(--gray-50); color:var(--gray-500);"></div>
            <div class="form-group"><label>No. Telepon</label><input type="tel" name="telepon" value="<?= h($user['telepon'] ?? '') ?>"></div>
            <button type="submit" class="btn btn-primary btn-block">Simpan Profil</button>
          </form>
        </div>
        <div class="card">
          <div class="card-header"><h3>Ganti Password</h3></div>
          <form method="post">
            <input type="hidden" name="action" value="password">
            <div class="form-group"><label>Password Lama</label><input type="password" name="password_lama" required></div>
            <div class="form-group"><label>Password Baru</label><input type="password" name="password_baru" required></div>
            <div class="form-group"><label>Konfirmasi Password Baru</label><input type="password" name="password_konfirmasi" required></div>
            <button type="submit" class="btn btn-outline btn-block">Ubah Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
