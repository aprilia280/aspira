<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'Daftar Pengaduan';
$pageSub = 'Kelola semua pengaduan yang masuk dari masyarakat.';
$filterStatus = trim($_GET['status'] ?? '');
$filterKategori = trim($_GET['kategori'] ?? '');
$search = trim($_GET['q'] ?? '');
$all = get_pengaduan(['status' => $filterStatus, 'kategori' => $filterKategori, 'search' => $search]);
$kategori = get_kategori();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Pengaduan - ASPIRA Admin</title>
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
        <form method="get" class="toolbar">
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass ic"></i>
            <input type="search" name="q" id="tableSearch" placeholder="Cari nomor tiket, judul, atau pelapor..." value="<?= h($search) ?>">
          </div>
          <div class="toolbar-filters">
            <select name="status" id="filterStatus" onchange="this.form.submit()">
              <option value="">Semua Status</option>
              <?php foreach (['Diverifikasi', 'Proses', 'Selesai', 'Ditolak'] as $s): ?>
              <option value="<?= h($s) ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= h($s) ?></option>
              <?php endforeach; ?>
            </select>
            <select name="kategori" id="filterKategori" onchange="this.form.submit()">
              <option value="">Semua Kategori</option>
              <?php foreach ($kategori as $k): ?>
              <option <?= $filterKategori === $k['nama'] ? 'selected' : '' ?>><?= h($k['nama']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-filter"></i> Terapkan</button>
            <a href="export-pengaduan.php?<?= h(http_build_query(['status' => $filterStatus, 'kategori' => $filterKategori, 'q' => $search])) ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-file-export"></i> Export</a>
          </div>
        </form>
        <div class="table-wrap">
          <table id="dataTable">
            <thead>
              <tr><th>No. Tiket</th><th>Judul Pengaduan</th><th>Pelapor</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($all as $p): ?>
              <tr data-status="<?= h($p['status']) ?>" data-kategori="<?= h($p['kategori']) ?>">
                <td class="cell-strong"><?= h($p['no_tiket']) ?></td>
                <td><?= h($p['judul']) ?></td>
                <td><?= h($p['nama']) ?></td>
                <td><?= h($p['kategori']) ?></td>
                <td class="cell-muted"><?= h(date('d M Y', strtotime($p['tanggal']))) ?></td>
                <td><span class="<?= status_badge_class($p['status']) ?>"><?= h($p['status']) ?></span></td>
                <td>
                  <div class="table-actions">
                    <a class="icon-btn" href="pengaduan-detail.php?tiket=<?= urlencode($p['no_tiket']) ?>" title="Lihat"><i class="fa-solid fa-eye"></i></a>
                    <a class="icon-btn" href="tindak-lanjut.php?tiket=<?= urlencode($p['no_tiket']) ?>" title="Tindak Lanjut"><i class="fa-solid fa-diagram-project"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="pagination">
          <span>&laquo;</span><span class="active">1</span><span>2</span><span>3</span><span>...</span><span>&raquo;</span>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
