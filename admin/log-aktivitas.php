<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Log Aktivitas';
$pageSub = 'Riwayat aktivitas seluruh pengguna sistem.';
$logs = get_logs(200);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log Aktivitas - ASPIRA Admin</title>
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
      <div class="card">
        <div class="card-header"><h3>Riwayat Aktivitas</h3></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Pengguna</th><th>Aktivitas</th><th>Waktu</th></tr></thead>
            <tbody>
            <?php if (!$logs): ?>
              <tr><td colspan="3" style="text-align:center; color:var(--gray-500);">Belum ada aktivitas tercatat.</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $l): ?>
              <tr>
                <td class="cell-strong"><?= h($l['user_nama']) ?></td>
                <td><?= h($l['aksi']) ?></td>
                <td class="cell-muted"><?= h(date('d M Y H:i', strtotime($l['waktu']))) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
