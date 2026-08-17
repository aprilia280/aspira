<?php
$current = basename($_SERVER['SCRIPT_NAME']);
function anav($file, $current, $label, $icon) {
    $active = $file === $current ? 'active' : '';
    echo '<a href="' . h($file) . '" class="' . $active . '"><span class="ic"><i class="fa-solid ' . $icon . '"></i></span>' . h($label) . '</a>';
}
?>
<aside class="admin-sidebar" id="adminSidebar">
  <a href="dashboard.php" class="brand">
    <?php render_logo(36); ?>
    <span>ASPIRA<span class="brand-sub">Admin Panel</span></span>
  </a>
  <nav class="admin-nav">
    <?php
    anav('dashboard.php', $current, 'Dashboard', 'fa-gauge');
    anav('pengaduan.php', $current, 'Pengaduan', 'fa-inbox');
    anav('verifikasi.php', $current, 'Verifikasi', 'fa-circle-check');
    anav('tindak-lanjut.php', $current, 'Proses Pengaduan', 'fa-diagram-project');
    anav('kategori.php', $current, 'Kategori', 'fa-tags');
    anav('pengguna.php', $current, 'Pengguna', 'fa-users');
    anav('laporan.php', $current, 'Laporan', 'fa-chart-column');
    anav('informasi.php', $current, 'Informasi', 'fa-circle-info');
    anav('notifikasi.php', $current, 'Notifikasi', 'fa-bell');
    anav('pengaturan.php', $current, 'Pengaturan', 'fa-gear');
    anav('log-aktivitas.php', $current, 'Log Aktivitas', 'fa-clock-rotate-left');
    ?>
  </nav>
  <div class="admin-nav-footer">
    <a href="logout.php" style="color:var(--red-600); display:flex; align-items:center; gap:11px; padding:10px 14px;">
      <span class="ic"><i class="fa-solid fa-right-from-bracket"></i></span> Keluar
    </a>
  </div>
</aside>
