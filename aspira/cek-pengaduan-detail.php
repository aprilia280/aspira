<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Detail Pengaduan';
$tiket = trim($_GET['tiket'] ?? '');
$p = find_pengaduan($tiket);
if (!$p) {
    header('Location: cek-pengaduan.php');
    exit;
}
include __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <p style="color:var(--gray-500); font-size:13.5px;">Beranda / Cek Pengaduan / Detail</p>
    <h2 style="margin-bottom:20px;">Detail Pengaduan</h2>

    <div class="detail-grid">
      <div class="card">
        <div class="card-header">
          <h3>Informasi Pengaduan</h3>
          <span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span>
        </div>
        <table>
          <tr><td class="cell-muted" style="width:160px;">No. Tiket</td><td class="cell-strong"><?= h($p['no_tiket']) ?></td></tr>
          <tr><td class="cell-muted">Tanggal</td><td><?= h(date('d M Y, H:i', strtotime($p['tanggal']))) ?> WIB</td></tr>
          <tr><td class="cell-muted">Kategori</td><td><span class="badge badge-info"><?= h($p['kategori']) ?></span><?php if (!empty($p['kategori_detail'])): ?> — <?= h($p['kategori_detail']) ?><?php endif; ?></td></tr>
          <tr><td class="cell-muted">Judul</td><td><?= h($p['judul']) ?></td></tr>
          <tr><td class="cell-muted">Isi Pengaduan</td><td><?= nl2br(h($p['isi'])) ?></td></tr>
          <tr><td class="cell-muted">Lokasi</td><td><?= h($p['lokasi']) ?></td></tr>
          <?php if (!empty($p['instansi'])): ?>
          <tr><td class="cell-muted">Instansi Terkait</td><td><?= h($p['instansi']) ?></td></tr>
          <?php endif; ?>
          <?php if (!empty($p['lampiran'])): ?>
          <tr><td class="cell-muted">Lampiran</td><td><i class="fa-solid fa-paperclip"></i> <?= h($p['lampiran']) ?></td></tr>
          <?php endif; ?>
        </table>

        <?php if (!empty($p['tanggapan'])): ?>
        <div style="margin-top:18px; padding-top:18px; border-top:1px solid var(--gray-100);">
          <h4 style="font-size:14px;">Tanggapan</h4>
          <p style="font-size:13.8px; color:var(--gray-700);"><?= nl2br(h($p['tanggapan'])) ?></p>
          <p style="font-size:12.5px; color:var(--gray-500);">- <?= h($p['petugas']) ?></p>
        </div>
        <?php endif; ?>
      </div>

      <div class="card">
        <h3>Riwayat Proses</h3>
        <div class="timeline" style="margin-top:16px;">
          <?php foreach ($p['riwayat'] as $r): ?>
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
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
