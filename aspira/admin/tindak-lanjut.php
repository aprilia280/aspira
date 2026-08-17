<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Proses Tindak Lanjut';
$pageSub = 'Kelola proses penanganan pengaduan.';

$proses = get_pengaduan(['status' => 'Proses']);
$tiket = trim($_GET['tiket'] ?? ($proses[0]['no_tiket'] ?? ''));
$success = false;
$uploadError = '';

$dinasList = get_instansi_list();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tiketPost = trim($_POST['no_tiket'] ?? '');
    $tugasKe = trim($_POST['tugas_ke'] ?? '');
    $prioritas = trim($_POST['prioritas'] ?? 'Sedang');
    $statusBaru = trim($_POST['status'] ?? 'Proses');
    $catatan = trim($_POST['catatan'] ?? '');
    $adminNama = current_admin()['nama'];

    $upload = handle_upload('lampiran', 'tindak-lanjut');
    if (!$upload['ok']) {
        $uploadError = $upload['error'];
    } else {
        $fields = ['status' => $statusBaru, 'prioritas' => $prioritas];
        if (!empty($_POST['batas_waktu'])) $fields['batas_waktu'] = $_POST['batas_waktu'];
        if ($tugasKe) $fields['petugas'] = $tugasKe;
        if ($catatan) $fields['tanggapan'] = $catatan;
        if ($upload['filename']) $fields['lampiran'] = $upload['filename'];
        update_pengaduan($tiketPost, $fields);

        add_riwayat($tiketPost, 'Ditugaskan ke ' . $tugasKe . ' (Prioritas ' . $prioritas . ')', $adminNama);
        if ($statusBaru === 'Selesai') {
            add_riwayat($tiketPost, 'Selesai', $adminNama);
            add_notifikasi('circle-check', 'Pengaduan selesai ditangani', $tiketPost . ' telah ditandai Selesai');
        }
        add_log('Menindaklanjuti pengaduan ' . $tiketPost . ' ke ' . $tugasKe . ' (' . $statusBaru . ')');

        $success = true;
        $tiket = $tiketPost;
        $proses = get_pengaduan(['status' => 'Proses']);
    }
}

$currentPengaduan = $tiket ? find_pengaduan($tiket) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proses Tindak Lanjut - ASPIRA Admin</title>
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
      <?php if ($success): ?><div class="form-success">Tugas berhasil dikirim / status pengaduan diperbarui.</div><?php endif; ?>
      <?php if ($uploadError): ?><div class="form-error"><?= h($uploadError) ?></div><?php endif; ?>
      <div class="detail-grid">
        <div class="card">
          <div class="card-header"><h3>Pengaduan Dalam Proses (<?= count($proses) ?>)</h3></div>
          <ul class="mini-list">
            <?php if (!$proses): ?>
              <li><div class="t" style="color:var(--gray-500); font-weight:400;">Tidak ada pengaduan dalam proses saat ini.</div></li>
            <?php endif; ?>
            <?php foreach ($proses as $p): ?>
            <li>
              <div>
                <div class="t"><?= h($p['judul']) ?></div>
                <div class="d"><?= h($p['no_tiket']) ?></div>
              </div>
              <a href="tindak-lanjut.php?tiket=<?= urlencode($p['no_tiket']) ?>" class="btn btn-sm <?= $p['no_tiket'] === $tiket ? 'btn-primary' : 'btn-outline' ?>">Pilih</a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <?php if ($currentPengaduan): ?>
        <div class="card">
          <div class="card-header">
            <div>
              <p class="cell-muted" style="margin:0;">No. Tiket: <?= h($currentPengaduan['no_tiket']) ?></p>
              <h3><?= h($currentPengaduan['judul']) ?></h3>
            </div>
            <span class="<?= status_badge_class($currentPengaduan['status']) ?>">Status Saat Ini: <?= h($currentPengaduan['status']) ?></span>
          </div>
          <?php if (!empty($currentPengaduan['instansi'])): ?>
          <p class="cell-muted" style="margin:-8px 0 0;">Instansi pilihan pengadu: <strong><?= h($currentPengaduan['instansi']) ?></strong></p>
          <?php endif; ?>

          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="no_tiket" value="<?= h($currentPengaduan['no_tiket']) ?>">
            <div class="form-row">
              <div class="form-group">
                <label>Tugas ke</label>
                <select name="tugas_ke">
                  <?php
                    $terpilih = ($currentPengaduan['petugas'] && $currentPengaduan['petugas'] !== '-')
                        ? $currentPengaduan['petugas']
                        : ($currentPengaduan['instansi'] ?? '');
                  ?>
                  <?php foreach ($dinasList as $d): ?>
                  <option <?= $terpilih === $d ? 'selected' : '' ?>><?= h($d) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Status Saat Ini</label>
                <select name="status">
                  <option value="Proses" <?= $currentPengaduan['status'] === 'Proses' ? 'selected' : '' ?>>Proses</option>
                  <option value="Selesai" <?= $currentPengaduan['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                  <option value="Ditolak" <?= $currentPengaduan['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Prioritas</label>
                <select name="prioritas">
                  <option>Rendah</option>
                  <option selected>Sedang</option>
                  <option>Tinggi</option>
                </select>
              </div>
              <div class="form-group">
                <label>Batas Waktu</label>
                <input type="date" name="batas_waktu" value="<?= date('Y-m-d', strtotime('+5 days')) ?>">
              </div>
            </div>
            <div class="form-group">
              <label>Catatan</label>
              <textarea name="catatan" placeholder="Mohon ditindaklanjuti dan diberikan informasi perkembangan."><?= h($currentPengaduan['tanggapan'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
              <label>Lampiran (Opsional)</label>
              <div class="input-file">
                <label class="btn btn-outline btn-sm" style="cursor:pointer;">
                  <i class="fa-solid fa-paperclip"></i> Pilih File
                  <input type="file" name="lampiran">
                </label>
                <div class="file-name">Belum ada file dipilih</div>
                <div class="form-hint">Format: jpg, jpeg, png, pdf (Maks. 5MB)</div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Kirim Tugas</button>
          </form>
        </div>
        <?php else: ?>
        <div class="card"><p style="color:var(--gray-500);">Pilih pengaduan di sebelah kiri untuk mengelola tindak lanjut.</p></div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
