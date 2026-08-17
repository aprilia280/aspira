<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Dashboard';
$pageSub = 'Ringkasan pengaduan dan aktivitas layanan publik';
$stats = stats_ringkasan();
$terbaru = get_pengaduan();
$terbaru = array_slice($terbaru, 0, 5);
$kategori = get_kategori();

[$trendLabels, $trendData] = trend_pengaduan(6);

$totalKat = array_sum(array_column($kategori, 'jumlah')) ?: 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - ASPIRA Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>
  <main class="admin-main">
    <?php include __DIR__ . '/../includes/admin-topbar.php'; ?>
    <div class="admin-content">

      <div class="stat-grid">
        <div class="stat-card c-blue">
          <div class="stat-icon"><i class="fa-solid fa-folder-open"></i></div>
          <div><div class="num"><?= number_format($stats['total'], 0, ',', '.') ?></div><div class="label">Total Pengaduan</div></div>
        </div>
        <div class="stat-card c-green">
          <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div><div class="num"><?= number_format($stats['selesai'], 0, ',', '.') ?></div><div class="label">Selesai</div></div>
        </div>
        <div class="stat-card c-amber">
          <div class="stat-icon"><i class="fa-solid fa-spinner"></i></div>
          <div><div class="num"><?= number_format($stats['proses'], 0, ',', '.') ?></div><div class="label">Proses</div></div>
        </div>
        <div class="stat-card c-red">
          <div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div>
          <div><div class="num"><?= number_format($stats['ditolak'], 0, ',', '.') ?></div><div class="label">Ditolak</div></div>
        </div>
      </div>

      <div class="dash-grid">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Grafik Pengaduan</h3>
              <p>7 hari terakhir</p>
            </div>
          </div>
          <div style="height:230px;"><canvas id="trendChart"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Status Pengaduan</h3></div>
          <div style="height:170px; position:relative;">
            <canvas id="statusChart"></canvas>
          </div>
          <div style="margin-top:14px;">
            <div class="legend-row"><span class="legend-dot" style="background:#1a9c4b;"></span> Selesai — <?= $stats['selesai'] ?> (<?= round($stats['selesai']/max($stats['total'],1)*100,1) ?>%)</div>
            <div class="legend-row"><span class="legend-dot" style="background:#c9820b;"></span> Proses — <?= $stats['proses'] ?> (<?= round($stats['proses']/max($stats['total'],1)*100,1) ?>%)</div>
            <div class="legend-row"><span class="legend-dot" style="background:#d3342d;"></span> Ditolak — <?= $stats['ditolak'] ?> (<?= round($stats['ditolak']/max($stats['total'],1)*100,1) ?>%)</div>
          </div>
        </div>
      </div>

      <div class="dash-grid" style="margin-top:18px;">
        <div class="card">
          <div class="card-header">
            <div><h3>Pengaduan Terbaru</h3></div>
            <a href="pengaduan.php" class="btn btn-ghost btn-sm">Lihat Semua</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>No. Tiket</th><th>Judul Pengaduan</th><th>Pelapor</th><th>Kategori</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
              <?php foreach ($terbaru as $p): ?>
                <tr>
                  <td class="cell-strong"><?= h($p['no_tiket']) ?></td>
                  <td><?= h($p['judul']) ?></td>
                  <td><?= h($p['nama']) ?></td>
                  <td><?= h($p['kategori']) ?></td>
                  <td class="cell-muted"><?= h(date('d M Y', strtotime($p['tanggal']))) ?></td>
                  <td><span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Pengaduan per Kategori</h3></div>
          <?php foreach ($kategori as $k): $pct = round($k['jumlah'] / $totalKat * 100); ?>
          <div style="margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; font-size:12.8px; margin-bottom:6px;">
              <span><?= h($k['nama']) ?></span><span class="cell-muted"><?= $pct ?>%</span>
            </div>
            <div class="progress-track"><div class="progress-fill" style="width:<?= $pct ?>%; background:#2f6fed;"></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
<script>
  aspiraLineChart('trendChart', <?= json_encode($trendLabels) ?>, <?= json_encode($trendData) ?>, 'Pengaduan');
  aspiraDoughnutChart('statusChart', ['Selesai','Proses','Ditolak'], [<?= $stats['selesai'] ?>, <?= $stats['proses'] ?>, <?= $stats['ditolak'] ?>], ['#1a9c4b','#c9820b','#d3342d']);
</script>
</body>
</html>
