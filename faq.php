<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'FAQ';
$faqs = [
    ['q' => 'Bagaimana cara mengajukan pengaduan?', 'a' => 'Klik menu "Ajukan Pengaduan", isi formulir dengan data diri dan detail keluhan, lalu klik "Kirim Pengaduan". Anda akan menerima nomor tiket untuk melacak status.'],
    ['q' => 'Berapa lama proses verifikasi pengaduan?', 'a' => 'Verifikasi umumnya dilakukan dalam 1x24 jam kerja sejak pengaduan diterima.'],
    ['q' => 'Bagaimana cara mengecek status pengaduan saya?', 'a' => 'Gunakan menu "Cek Pengaduan" dan masukkan nomor tiket yang Anda terima saat pertama kali mengajukan pengaduan.'],
    ['q' => 'Apakah data pelapor akan dirahasiakan?', 'a' => 'Ya, data pribadi pelapor hanya digunakan untuk keperluan verifikasi dan tindak lanjut, serta dijaga kerahasiaannya.'],
    ['q' => 'Apa yang terjadi jika pengaduan saya ditolak?', 'a' => 'Anda akan menerima catatan alasan penolakan, misalnya informasi kurang lengkap atau di luar kewenangan instansi, dan dapat mengajukan ulang dengan informasi tambahan.'],
];
include __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:760px;">
    <h2 style="margin-bottom:20px;">Pertanyaan yang Sering Diajukan</h2>
    <?php foreach ($faqs as $f): ?>
    <div class="card">
      <h4 style="font-size:14.5px;"><i class="fa-solid fa-circle-question" style="color:#2f6fed"></i> <?= h($f['q']) ?></h4>
      <p style="font-size:13.8px; color:var(--gray-700); margin:8px 0 0;"><?= h($f['a']) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
