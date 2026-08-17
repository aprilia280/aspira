<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Beranda';
$stats = stats_ringkasan();
$kategori = get_kategori();
usort($kategori, fn($a, $b) => ($a['nama'] === 'Lainnya') <=> ($b['nama'] === 'Lainnya'));
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-inner">
    <div>
      <span class="eyebrow"><i class="fa-solid fa-bullhorn"></i> Layanan Pengaduan Resmi</span>
      <h1>Sampaikan Keluhan, <span>Kami Siap Melayani</span></h1>
      <p>Laporkan keluhan, saran, atau masukan Anda terkait layanan publik. Setiap pengaduan akan diverifikasi dan ditindaklanjuti oleh instansi terkait.</p>
      <div class="hero-actions">
        <a href="ajukan-pengaduan.php" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Ajukan Pengaduan</a>
        <a href="cek-pengaduan.php" class="btn btn-outline"><i class="fa-solid fa-magnifying-glass"></i> Cek Pengaduan</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><i class="fa-solid fa-folder-open" style="color:#2f6fed"></i><div><div class="num"><?= number_format($stats['total'], 0, ',', '.') ?></div><div class="label">Total Pengaduan</div></div></div>
        <div class="hero-stat"><i class="fa-solid fa-circle-check" style="color:#1a9c4b"></i><div><div class="num"><?= number_format($stats['selesai'], 0, ',', '.') ?></div><div class="label">Selesai</div></div></div>
        <div class="hero-stat"><i class="fa-solid fa-spinner" style="color:#c9820b"></i><div><div class="num"><?= number_format($stats['proses'], 0, ',', '.') ?></div><div class="label">Proses</div></div></div>
        <div class="hero-stat"><i class="fa-solid fa-circle-xmark" style="color:#d3342d"></i><div><div class="num"><?= number_format($stats['ditolak'], 0, ',', '.') ?></div><div class="label">Ditolak</div></div></div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-visual-card">
        <div class="t">Rata-rata waktu respon</div>
        <div class="v">&lt; 24 Jam</div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-title">
      <h2>Kategori Pengaduan</h2>
      <p>Pilih kategori yang paling sesuai dengan keluhan Anda.</p>
    </div>
    <div class="cat-grid">
      <?php
      $icons = ['file-text' => 'fa-file-lines', 'road' => 'fa-road', 'users' => 'fa-users', 'leaf' => 'fa-leaf', 'dots' => 'fa-ellipsis'];
      $imageIcons = ['idcard' => 'assets/images/icon-administrasi-kependudukan.png', 'graduation' => 'assets/images/icon-pendidikan-sekolah.png', 'bansos' => 'assets/images/icon-bantuan-sosial.png', 'shield' => 'assets/images/icon-ketertiban-umum.png', 'pungli' => 'assets/images/icon-pungli.png', 'mbg' => 'assets/images/icon-mbg.png'];
      foreach ($kategori as $k):
        $ic = $icons[$k['icon']] ?? 'fa-circle';
      ?>
      <a href="ajukan-pengaduan.php?kategori=<?= urlencode($k['nama']) ?>" class="cat-card">
        <?php if (isset($imageIcons[$k['icon']])): ?>
        <div class="cat-icon" style="background:transparent; padding:0;"><img src="<?= base_url($imageIcons[$k['icon']]) ?>" alt="<?= h($k['nama']) ?>" style="width:100%; height:100%; object-fit:contain; border-radius:10px;"></div>
        <?php else: ?>
        <div class="cat-icon"><i class="fa-solid <?= $ic ?>"></i></div>
        <?php endif; ?>
        <h4><?= h($k['nama']) ?></h4>
        <p><?= h($k['deskripsi']) ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="background:#fff; border-top:1px solid var(--gray-200); border-bottom:1px solid var(--gray-200);">
  <div class="container">
    <div class="section-title">
      <h2>Bagaimana Cara Kerjanya?</h2>
      <p>Empat langkah mudah menyampaikan pengaduan Anda.</p>
    </div>
    <div class="step-grid">
      <div class="cat-card">
        <div class="cat-icon"><i class="fa-solid fa-pen"></i></div>
        <h4>1. Isi Formulir</h4>
        <p>Lengkapi data diri dan detail keluhan Anda.</p>
      </div>
      <div class="cat-card">
        <div class="cat-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <h4>2. Verifikasi</h4>
        <p>Tim kami memeriksa kelengkapan laporan.</p>
      </div>
      <div class="cat-card">
        <div class="cat-icon"><i class="fa-solid fa-people-arrows"></i></div>
        <h4>3. Ditindaklanjuti</h4>
        <p>Pengaduan diteruskan ke instansi terkait.</p>
      </div>
      <div class="cat-card">
        <div class="cat-icon"><i class="fa-solid fa-bell"></i></div>
        <h4>4. Notifikasi</h4>
        <p>Anda menerima kabar melalui email/WhatsApp.</p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
