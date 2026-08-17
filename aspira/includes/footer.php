<?php $footerSettings = get_settings(); ?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <a href="<?= base_url('index.php') ?>" class="brand" style="color:#fff;">
          <?php render_logo(36); ?>
          <span><?= h($footerSettings['nama_sistem'] ?: 'ASPIRA') ?></span>
        </a>
        <p style="margin-top:14px; max-width:280px;"><?= h($footerSettings['deskripsi'] ?: 'Platform aspirasi dan pengaduan masyarakat untuk pelayanan publik yang lebih responsif dan transparan.') ?></p>
      </div>
      <div>
        <h5>Layanan</h5>
        <ul>
          <li><a href="<?= base_url('ajukan-pengaduan.php') ?>">Ajukan Pengaduan</a></li>
          <li><a href="<?= base_url('cek-pengaduan.php') ?>">Cek Status</a></li>
          <li><a href="<?= base_url('informasi.php') ?>">Informasi Layanan</a></li>
        </ul>
      </div>
      <div>
        <h5>Bantuan</h5>
        <ul>
          <li><a href="<?= base_url('faq.php') ?>">FAQ</a></li>
          <li><a href="<?= base_url('kontak.php') ?>">Kontak</a></li>
          <li><a href="#">Syarat &amp; Ketentuan</a></li>
        </ul>
      </div>
      <div>
        <h5>Kontak</h5>
        <p><i class="fa-solid fa-location-dot"></i> <?= h($footerSettings['alamat'] ?: '-') ?></p>
      </div>
    </div>
    <div class="footer-bottom">&copy; <?= date('Y') ?> <?= h($footerSettings['nama_sistem'] ?: 'ASPIRA') ?>. Seluruh hak cipta dilindungi.</div>
  </div>
</footer>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
