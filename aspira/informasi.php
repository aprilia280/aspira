<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Informasi Layanan';
$settings = get_settings();
$berita = get_informasi(true);
include __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <h2 style="margin-bottom:20px;">Informasi Layanan</h2>
    <div class="detail-grid">
      <div>
        <div class="card">
          <div class="card-header"><h3><i class="fa-solid fa-clock" style="color:#2f6fed"></i> Jam Operasional</h3></div>
          <table>
            <tr><td class="cell-muted" style="width:140px;">Senin - Kamis</td><td>08.00 - 16.00 WIB</td></tr>
            <tr><td class="cell-muted">Jumat</td><td>08.00 - 17.00 WIB</td></tr>
          </table>
        </div>
        <div class="card">
          <div class="card-header"><h3><i class="fa-solid fa-location-dot" style="color:#2f6fed"></i> Lokasi</h3></div>
          <p style="font-size:13.8px; color:var(--gray-700);"><?= h($settings['alamat'] ?: '-') ?></p>
          <a href="https://maps.google.com/?q=<?= urlencode($settings['alamat'] ?? '') ?>" target="_blank" class="btn btn-outline btn-sm">Lihat di Maps</a>
        </div>
        <div class="card">
          <div class="card-header"><h3><i class="fa-solid fa-hand-holding-heart" style="color:#2f6fed"></i> Layanan Kami</h3></div>
          <p style="font-size:13.8px; color:var(--gray-700); margin-bottom:14px;">Berbagai layanan publik dalam satu tempat.</p>
          <div class="chip-row">
            <span class="tag">Prosedur Pengaduan</span>
            <span class="tag">FAQ</span>
            <span class="tag">Kontak</span>
            <span class="tag">Syarat &amp; Ketentuan</span>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3>Berita &amp; Pengumuman</h3></div>
        <ul class="mini-list">
          <?php if (!$berita): ?>
            <li><div class="t" style="color:var(--gray-500); font-weight:400;">Belum ada berita atau pengumuman.</div></li>
          <?php endif; ?>
          <?php foreach ($berita as $b): ?>
          <li>
            <div>
              <div class="t"><?= h($b['judul']) ?></div>
              <div class="d"><?= h(date('d M Y', strtotime($b['created_at']))) ?></div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
