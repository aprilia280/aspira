<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Pengaturan Sistem';
$pageSub = 'Atur konfigurasi sistem perusahaan.';
$tab = $_GET['tab'] ?? 'umum';
$settings = get_settings();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tab === 'umum') {
    $settings = [
        'nama_sistem' => trim($_POST['nama_sistem'] ?? ''),
        'deskripsi' => trim($_POST['deskripsi'] ?? ''),
        'email_kontak' => trim($_POST['email_kontak'] ?? ''),
        'no_telepon' => trim($_POST['no_telepon'] ?? ''),
        'alamat' => trim($_POST['alamat'] ?? ''),
    ];
    save_settings($settings);
    add_log('Mengubah pengaturan sistem (Informasi Umum)');
    $msg = 'Perubahan berhasil disimpan.';
}

$tabs = [
    'umum' => 'Umum',
    'email' => 'Email & Notifikasi',
    'kategori' => 'Kategori',
    'status' => 'Status Pengaduan',
    'integrasi' => 'Integrasi',
    'keamanan' => 'Keamanan',
    'backup' => 'Backup',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengaturan Sistem - ASPIRA Admin</title>
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
      <div class="settings-shell">
        <div class="settings-nav">
          <?php foreach ($tabs as $key => $label): ?>
          <a href="?tab=<?= $key ?>" class="<?= $tab === $key ? 'active' : '' ?>"><?= h($label) ?></a>
          <?php endforeach; ?>
        </div>

        <div class="card">
          <?php if ($tab === 'umum'): ?>
            <div class="card-header"><h3>Informasi Umum</h3></div>
            <form method="post">
              <div class="form-group"><label>Nama Sistem</label><input type="text" name="nama_sistem" value="<?= h($settings['nama_sistem']) ?>"></div>
              <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi"><?= h($settings['deskripsi']) ?></textarea></div>
              <div class="form-row">
                <div class="form-group"><label>Email Kontak</label><input type="email" name="email_kontak" value="<?= h($settings['email_kontak']) ?>"></div>
                <div class="form-group"><label>No. Telepon</label><input type="text" name="no_telepon" value="<?= h($settings['no_telepon']) ?>"></div>
              </div>
              <div class="form-group"><label>Alamat</label><input type="text" name="alamat" value="<?= h($settings['alamat']) ?>"></div>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>

          <?php elseif ($tab === 'email'): ?>
            <div class="card-header"><h3>Email &amp; Notifikasi</h3></div>
            <div class="form-group"><label>SMTP Host</label><input type="text" placeholder="smtp.diskominfo.go.id"></div>
            <div class="form-row">
              <div class="form-group"><label>Port</label><input type="text" placeholder="587"></div>
              <div class="form-group"><label>Pengirim</label><input type="email" placeholder="noreply@diskominfo.go.id"></div>
            </div>
            <label class="radio-card"><input type="checkbox" checked> Kirim notifikasi email saat status pengaduan berubah</label>
            <label class="radio-card"><input type="checkbox" checked> Kirim notifikasi WhatsApp saat pengaduan selesai</label>
            <button class="btn btn-primary" style="margin-top:8px;">Simpan Perubahan</button>

          <?php elseif ($tab === 'kategori'): ?>
            <div class="card-header"><h3>Kategori Pengaduan</h3></div>
            <p style="font-size:13.5px; color:var(--gray-500); margin-bottom:14px;">Kelola daftar kategori pada halaman <a href="kategori.php" style="color:var(--blue-600); font-weight:600;">Kelola Kategori</a>.</p>
            <div class="tag-list">
              <?php foreach (get_kategori() as $k): ?><span class="tag"><?= h($k['nama']) ?></span><?php endforeach; ?>
            </div>

          <?php elseif ($tab === 'status'): ?>
            <div class="card-header"><h3>Status Pengaduan</h3></div>
            <div class="tag-list">
              <span class="tag">Diverifikasi</span>
              <span class="tag">Proses</span>
              <span class="tag">Selesai</span>
              <span class="tag">Ditolak</span>
            </div>
            <p class="form-hint" style="margin-top:12px;">Alur status: Diverifikasi &rarr; Proses &rarr; Selesai / Ditolak.</p>

          <?php elseif ($tab === 'integrasi'): ?>
            <div class="card-header"><h3>Integrasi</h3></div>
            <label class="radio-card"><input type="checkbox"> WhatsApp Business API</label>
            <label class="radio-card"><input type="checkbox" checked> Google Maps (lokasi pengaduan)</label>
            <label class="radio-card"><input type="checkbox"> Single Sign-On (SSO) Pemerintah Daerah</label>
            <button class="btn btn-primary" style="margin-top:8px;">Simpan Perubahan</button>

          <?php elseif ($tab === 'keamanan'): ?>
            <div class="card-header"><h3>Keamanan</h3></div>
            <label class="radio-card"><input type="checkbox" checked> Wajibkan verifikasi 2 langkah untuk Super Admin</label>
            <label class="radio-card"><input type="checkbox" checked> Kunci akun setelah 5 kali gagal masuk</label>
            <div class="form-group" style="margin-top:14px;"><label>Masa Berlaku Sesi (menit)</label><input type="text" value="60"></div>
            <button class="btn btn-primary">Simpan Perubahan</button>

          <?php elseif ($tab === 'backup'): ?>
            <div class="card-header"><h3>Backup Data</h3></div>
            <p style="font-size:13.5px; color:var(--gray-500); margin-bottom:14px;">Backup terakhir: 19 Juni 2024, 02.00 WIB (otomatis harian).</p>
            <button class="btn btn-outline"><i class="fa-solid fa-download"></i> Unduh Backup Sekarang</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
