<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Laporan Pengaduan';
$pageSub = 'Buat dan unduh laporan pengaduan.';
$dari = trim($_GET['dari'] ?? '');
$sampai = trim($_GET['sampai'] ?? '');
$filterKategori = trim($_GET['kategori'] ?? '');
$filterInstansi = trim($_GET['instansi'] ?? '');

$stats = stats_ringkasan(['dari' => $dari, 'sampai' => $sampai, 'kategori' => $filterKategori, 'instansi' => $filterInstansi]);
$trendDays = ($dari && $sampai) ? (int) max(1, (strtotime($sampai) - strtotime($dari)) / 86400) : 30;
[$trendLabels, $trendData] = trend_pengaduan($trendDays, ['kategori' => $filterKategori, 'instansi' => $filterInstansi]);
$kategori = get_kategori();
$instansiList = get_instansi_list();
$katLabels = array_column($kategori, 'nama');
$katData = array_column($kategori, 'jumlah');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Pengaduan - ASPIRA Admin</title>
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
      <div class="card">
        <div class="card-header"><h3>Buat Laporan</h3></div>
        <form method="get" style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:14px; align-items:end;">
          <div class="form-group" style="margin-bottom:0;">
            <label>Dari Tanggal</label>
            <input type="date" name="dari" value="<?= h($dari) ?>">
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label>Sampai Tanggal</label>
            <input type="date" name="sampai" value="<?= h($sampai) ?>">
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label>Kategori</label>
            <select name="kategori">
              <option value="">Semua Kategori</option>
              <?php foreach ($kategori as $k): ?><option <?= $filterKategori === $k['nama'] ? 'selected' : '' ?>><?= h($k['nama']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label>Instansi</label>
            <select name="instansi">
              <option value="">Semua Instansi</option>
              <?php foreach ($instansiList as $ins): ?><option <?= $filterInstansi === $ins ? 'selected' : '' ?>><?= h($ins) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">Generate Laporan</button>
            <a href="export-pengaduan.php?<?= h(http_build_query(['dari' => $dari, 'sampai' => $sampai, 'kategori' => $filterKategori, 'instansi' => $filterInstansi])) ?>" class="btn btn-outline"><i class="fa-solid fa-file-excel"></i> Export Excel</a>
          </div>
        </form>
      </div>

      <div class="card">
        <div class="card-header"><h3>Ringkasan</h3></div>
        <div class="stat-grid" style="margin-bottom:0;">
          <div class="stat-card c-blue"><div class="stat-icon"><i class="fa-solid fa-folder-open"></i></div><div><div class="num"><?= number_format($stats['total'],0,',','.') ?></div><div class="label">Total Pengaduan</div></div></div>
          <div class="stat-card c-green"><div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div><div><div class="num"><?= number_format($stats['selesai'],0,',','.') ?></div><div class="label">Selesai</div></div></div>
          <div class="stat-card c-amber"><div class="stat-icon"><i class="fa-solid fa-spinner"></i></div><div><div class="num"><?= number_format($stats['proses'],0,',','.') ?></div><div class="label">Proses</div></div></div>
          <div class="stat-card c-red"><div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div><div><div class="num"><?= number_format($stats['ditolak'],0,',','.') ?></div><div class="label">Ditolak</div></div></div>
        </div>
      </div>

      <div class="two-col">
        <div class="card">
          <div class="card-header"><h3>Grafik Tren Pengaduan</h3></div>
          <div style="height:230px;"><canvas id="trendChart"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Pengaduan per Kategori</h3></div>
          <div style="height:230px;"><canvas id="katChart"></canvas></div>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
<script>
  aspiraLineChart('trendChart', <?= json_encode($trendLabels) ?>, <?= json_encode($trendData) ?>, 'Pengaduan');
  aspiraBarChart('katChart', <?= json_encode($katLabels) ?>, <?= json_encode($katData) ?>, '#2f6fed');
</script>
</body>
</html>
