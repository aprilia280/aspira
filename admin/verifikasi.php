<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Verifikasi Pengaduan';
$pageSub = 'Verifikasi dan validasi pengaduan sebelum ditindaklanjuti.';

$pending = get_pengaduan(['status' => 'Diverifikasi']);
$tiket = trim($_GET['tiket'] ?? ($pending[0]['no_tiket'] ?? ''));
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tiketPost = trim($_POST['no_tiket'] ?? '');
    $hasil = $_POST['hasil'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    $adminNama = current_admin()['nama'];

    $fields = [];
    if ($catatan) $fields['tanggapan'] = $catatan;

    if ($hasil === 'valid') {
        $fields['status'] = 'Proses';
        update_pengaduan($tiketPost, $fields);
        add_riwayat($tiketPost, 'Diverifikasi - Valid', $adminNama);
        add_log('Memverifikasi pengaduan ' . $tiketPost . ' (Valid)');
    } else {
        $fields['status'] = 'Ditolak';
        update_pengaduan($tiketPost, $fields);
        $tahap = $hasil === 'bukan_kewenangan' ? 'Ditolak - Bukan Kewenangan' : 'Ditolak - Informasi Tidak Lengkap';
        add_riwayat($tiketPost, $tahap, $adminNama);
        add_log('Menolak pengaduan ' . $tiketPost . ' (' . $tahap . ')');
    }
    add_notifikasi('circle-exclamation', 'Pengaduan diverifikasi', $tiketPost . ' - ' . ($hasil === 'valid' ? 'Valid, diteruskan ke proses' : 'Ditolak'));

    header('Location: verifikasi.php');
    exit;
}

$currentPengaduan = $tiket ? find_pengaduan($tiket) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Pengaduan - ASPIRA Admin</title>
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
      <div class="detail-grid">
        <div class="card">
          <div class="card-header"><h3>Menunggu Verifikasi (<?= count($pending) ?>)</h3></div>
          <ul class="mini-list">
            <?php if (!$pending): ?>
              <li><div class="t" style="color:var(--gray-500); font-weight:400;">Tidak ada pengaduan yang menunggu verifikasi.</div></li>
            <?php endif; ?>
            <?php foreach ($pending as $p): ?>
            <li>
              <div>
                <div class="t"><?= h($p['judul']) ?></div>
                <div class="d"><?= h($p['no_tiket']) ?> &middot; <?= h($p['kategori']) ?></div>
              </div>
              <a href="verifikasi.php?tiket=<?= urlencode($p['no_tiket']) ?>" class="btn btn-sm <?= $p['no_tiket'] === $tiket ? 'btn-primary' : 'btn-outline' ?>">Pilih</a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <?php if ($currentPengaduan): ?>
        <div class="card">
          <div class="card-header">
            <div>
              <p class="cell-muted" style="margin:0;"><?= h($currentPengaduan['no_tiket']) ?></p>
              <h3><?= h($currentPengaduan['judul']) ?></h3>
            </div>
          </div>
          <p style="font-size:13.8px; color:var(--gray-700);"><?= nl2br(h($currentPengaduan['isi'])) ?></p>
          <table style="margin-bottom:14px;">
            <tr><td class="cell-muted" style="width:100px;">Kategori</td><td><span class="badge badge-info"><?= h($currentPengaduan['kategori']) ?></span></td></tr>
            <tr><td class="cell-muted">Tanggal</td><td><?= h(date('d M Y H:i', strtotime($currentPengaduan['tanggal']))) ?> WIB</td></tr>
            <tr><td class="cell-muted">Pelapor</td><td><?= h($currentPengaduan['nama']) ?></td></tr>
          </table>

          <form method="post">
            <input type="hidden" name="no_tiket" value="<?= h($currentPengaduan['no_tiket']) ?>">
            <label>Hasil Verifikasi</label>
            <label class="radio-card"><input type="radio" name="hasil" value="valid" required> Valid - Dapat Ditindaklanjuti</label>
            <label class="radio-card"><input type="radio" name="hasil" value="bukan_kewenangan"> Tidak Valid - Bukan Kewenangan</label>
            <label class="radio-card"><input type="radio" name="hasil" value="tidak_lengkap"> Tidak Valid - Informasi Tidak Lengkap</label>

            <div class="form-group" style="margin-top:14px;">
              <label>Catatan (Opsional)</label>
              <textarea name="catatan" placeholder="Tambah catatan verifikasi..."></textarea>
            </div>
            <div style="display:flex; gap:10px;">
              <button type="submit" class="btn btn-primary" style="flex:1;">Verifikasi</button>
            </div>
          </form>
        </div>
        <?php else: ?>
        <div class="card"><p style="color:var(--gray-500);">Pilih pengaduan di sebelah kiri untuk memulai verifikasi.</p></div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
