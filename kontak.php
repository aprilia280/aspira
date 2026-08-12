<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Kontak';
$settings = get_settings();
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulasi pengiriman pesan kontak
    $sent = true;
}
include __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:900px;">
    <h2 style="margin-bottom:20px;">Hubungi Kami</h2>
    <div class="detail-grid">
      <div class="card">
        <?php if ($sent): ?>
          <div class="form-success">Pesan Anda berhasil dikirim. Tim kami akan segera menghubungi Anda.</div>
        <?php endif; ?>
        <form method="post">
          <div class="form-row">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" placeholder="Masukkan nama lengkap" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" placeholder="Masukkan email" required></div>
          </div>
          <div class="form-group"><label>Subjek</label><input type="text" name="subjek" placeholder="Subjek pesan"></div>
          <div class="form-group"><label>Pesan</label><textarea name="pesan" placeholder="Tulis pesan Anda..." required></textarea></div>
          <button type="submit" class="btn btn-primary btn-block">Kirim Pesan</button>
        </form>
      </div>
      <div class="card">
        <h3 style="font-size:15px;">Informasi Kontak</h3>
        <p style="font-size:13.8px; color:var(--gray-700); margin:14px 0;"><i class="fa-solid fa-location-dot" style="color:#2f6fed"></i> <?= h($settings['alamat'] ?: '-') ?></p>
        <p style="font-size:13.8px; color:var(--gray-700); margin:14px 0;"><i class="fa-solid fa-clock" style="color:#2f6fed"></i> Senin - Kamis 08.00-16.00, Jumat 08.00-17.00 WIB</p>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
