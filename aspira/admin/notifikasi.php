<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Notifikasi';
$pageSub = 'Aktivitas dan pembaruan terbaru pada sistem.';

if (isset($_GET['tandai_semua'])) {
    mark_all_notifikasi_read();
    header('Location: notifikasi.php');
    exit;
}

$notif = get_notifikasi(50);
$iconMap = [
    'inbox' => ['fa-inbox', 'blue'],
    'circle-check' => ['fa-circle-check', 'green'],
    'circle-exclamation' => ['fa-circle-exclamation', 'amber'],
    'user-plus' => ['fa-user-plus', 'blue'],
    'circle-xmark' => ['fa-circle-xmark', 'red'],
];
function waktu_lalu($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    if ($diff < 172800) return 'Kemarin';
    return floor($diff / 86400) . ' hari lalu';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifikasi - ASPIRA Admin</title>
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
      <div class="card" style="max-width:700px;">
        <div class="card-header">
          <h3>Semua Notifikasi</h3>
          <a href="?tandai_semua=1" class="btn btn-ghost btn-sm">Tandai semua dibaca</a>
        </div>
        <ul class="mini-list">
          <?php if (!$notif): ?>
            <li><div class="t" style="color:var(--gray-500); font-weight:400;">Belum ada notifikasi.</div></li>
          <?php endif; ?>
          <?php foreach ($notif as $n): [$ic, $color] = $iconMap[$n['tipe']] ?? ['fa-circle-info', 'blue']; ?>
          <li style="<?= $n['dibaca'] ? 'opacity:.6;' : '' ?>">
            <div style="display:flex; gap:12px; align-items:flex-start;">
              <div class="stat-icon" style="width:36px; height:36px; font-size:14px; background:var(--<?= $color ?>-50); color:var(--<?= $color ?>-600);">
                <i class="fa-solid <?= $ic ?>"></i>
              </div>
              <div>
                <div class="t"><?= h($n['judul']) ?></div>
                <div class="d"><?= h($n['deskripsi']) ?></div>
              </div>
            </div>
            <div class="d"><?= h(waktu_lalu($n['waktu'])) ?></div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
