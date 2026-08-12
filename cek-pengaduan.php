<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Cek Pengaduan';
$tiket = trim($_GET['tiket'] ?? '');
$notFound = false;
if ($tiket) {
    $found = find_pengaduan($tiket);
    if ($found) {
        header('Location: cek-pengaduan-detail.php?tiket=' . urlencode($tiket));
        exit;
    } else {
        $notFound = true;
    }
}

// Riwayat contoh: pengaduan dengan email yang dicari (simulasi "riwayat pengaduan saya")
$emailCari = trim($_GET['email'] ?? '');
$riwayat = [];
if ($emailCari) {
    foreach (get_pengaduan() as $p) {
        if (strcasecmp($p['email'], $emailCari) === 0) $riwayat[] = $p;
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:760px;">
    <div class="card">
      <h2 style="font-size:19px;">Cek Status Pengaduan</h2>
      <p style="color:var(--gray-500); font-size:13.5px; margin-bottom:20px;">Masukan nomor tiket pengaduan Anda untuk melihat status terbaru.</p>
      <?php if ($notFound): ?>
        <div class="form-error">Nomor tiket <strong><?= h($tiket) ?></strong> tidak ditemukan. Periksa kembali nomor tiket Anda.</div>
      <?php endif; ?>
      <form method="get" style="display:flex; gap:10px; margin-bottom:8px;">
        <div class="form-group" style="flex:1; margin-bottom:0;">
          <label>Nomor Tiket Pengaduan</label>
          <input type="text" name="tiket" placeholder="Contoh: 2026000123" value="<?= h($tiket) ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Cek Status</button>
      </form>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <h3>Riwayat Pengaduan Saya</h3>
          <p>Masukkan email yang Anda gunakan saat mengajukan pengaduan.</p>
        </div>
      </div>
      <form method="get" style="display:flex; gap:10px; margin-bottom:16px;">
        <input type="email" name="email" placeholder="Masukkan email Anda" value="<?= h($emailCari) ?>" style="flex:1;">
        <button type="submit" class="btn btn-outline">Cari</button>
      </form>
      <div class="table-wrap">
        <table>
          <thead><tr><th>No. Tiket</th><th>Judul Pengaduan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php if ($emailCari && !$riwayat): ?>
            <tr><td colspan="5" style="text-align:center; color:var(--gray-500);">Tidak ada pengaduan ditemukan untuk email ini.</td></tr>
          <?php endif; ?>
          <?php foreach ($riwayat as $p): ?>
            <tr>
              <td class="cell-strong"><?= h($p['no_tiket']) ?></td>
              <td><?= h($p['judul']) ?></td>
              <td class="cell-muted"><?= h(date('d M Y', strtotime($p['tanggal']))) ?></td>
              <td><span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span></td>
              <td><a href="cek-pengaduan-detail.php?tiket=<?= urlencode($p['no_tiket']) ?>" class="btn btn-ghost btn-sm">Lihat</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$emailCari): foreach (array_slice(get_pengaduan(), 0, 3) as $p): ?>
            <tr>
              <td class="cell-strong"><?= h($p['no_tiket']) ?></td>
              <td><?= h($p['judul']) ?></td>
              <td class="cell-muted"><?= h(date('d M Y', strtotime($p['tanggal']))) ?></td>
              <td><span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span></td>
              <td><a href="cek-pengaduan-detail.php?tiket=<?= urlencode($p['no_tiket']) ?>" class="btn btn-ghost btn-sm">Lihat</a></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
