<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Detail Pengaduan';
$pageSub = 'Informasi lengkap pengaduan masyarakat.';
$tiket = trim($_GET['tiket'] ?? '');
$p = find_pengaduan($tiket);
if (!$p) { header('Location: pengaduan.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Pengaduan - ASPIRA Admin</title>
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
      <div class="toolbar">
        <a href="pengaduan.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <div class="toolbar-filters">
          <a href="tindak-lanjut.php?tiket=<?= urlencode($p['no_tiket']) ?>" class="btn btn-primary btn-sm">Tindak Lanjut</a>
          <a href="verifikasi.php?tiket=<?= urlencode($p['no_tiket']) ?>" class="btn btn-outline btn-sm">Verifikasi</a>
          <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">Cetak</button>
        </div>
      </div>

      <div class="detail-grid">
        <div class="card">
          <div class="card-header"><h3>Informasi Pengaduan</h3></div>
          <table>
            <tr><td class="cell-muted" style="width:150px;">No. Tiket</td><td class="cell-strong"><?= h($p['no_tiket']) ?></td></tr>
            <tr><td class="cell-muted">Tanggal</td><td><?= h(date('d M Y H:i', strtotime($p['tanggal']))) ?> WIB</td></tr>
            <tr><td class="cell-muted">Kategori</td><td><span class="badge badge-info"><?= h($p['kategori']) ?></span><?php if (!empty($p['kategori_detail'])): ?> — <?= h($p['kategori_detail']) ?><?php endif; ?></td></tr>
            <tr><td class="cell-muted">Judul</td><td><?= h($p['judul']) ?></td></tr>
            <tr><td class="cell-muted">Isi Pengaduan</td><td><?= nl2br(h($p['isi'])) ?></td></tr>
            <tr><td class="cell-muted">Lokasi</td><td><?= h($p['lokasi']) ?></td></tr>
            <?php if (!empty($p['instansi'])): ?>
            <tr><td class="cell-muted">Instansi Terkait</td><td><?= h($p['instansi']) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($p['lampiran'])): $ext = strtolower(pathinfo($p['lampiran'], PATHINFO_EXTENSION)); ?>
            <tr><td class="cell-muted">Lampiran</td><td>
              <a href="../<?= h($p['lampiran']) ?>" target="_blank" class="tag"><i class="fa-solid <?= $ext === 'pdf' ? 'fa-file-pdf' : 'fa-image' ?>"></i> Lihat Lampiran</a>
              <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
              <div style="margin-top:10px;"><img src="../<?= h($p['lampiran']) ?>" alt="Lampiran" style="max-width:260px; border-radius:10px; border:1px solid var(--gray-200);"></div>
              <?php endif; ?>
            </td></tr>
            <?php endif; ?>
            <tr><td class="cell-muted">Status</td><td><span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span></td></tr>
          </table>
        </div>

        <div>
          <div class="card">
            <div class="card-header"><h3>Informasi Pelapor</h3></div>
            <table>
              <tr><td class="cell-muted" style="width:100px;">Nama</td><td><?= h($p['nama']) ?></td></tr>
              <tr><td class="cell-muted">Email</td><td><?= h($p['email']) ?></td></tr>
              <tr><td class="cell-muted">No. Telepon</td><td><?= h($p['telepon'] ?: '-') ?></td></tr>
            </table>
          </div>
          <div class="card">
            <div class="card-header"><h3>Riwayat Proses</h3></div>
            <div class="timeline">
              <?php foreach ($p['riwayat'] as $i => $r): ?>
              <div class="timeline-item done">
                <span class="timeline-dot"></span>
                <div class="timeline-title"><?= h($r['tahap']) ?></div>
                <div class="timeline-time"><?= h($r['waktu']) ?> &middot; <?= h($r['oleh']) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
