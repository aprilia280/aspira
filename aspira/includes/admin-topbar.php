<?php $admin = current_admin(); $unread = count_unread_notifikasi(); $current = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="admin-topbar">
  <div>
    <button class="mobile-nav-toggle btn btn-ghost btn-sm" onclick="document.getElementById('adminSidebar').classList.toggle('open')" style="display:none;">
      <i class="fa-solid fa-bars"></i>
    </button>
    <h1><?= h($pageTitle ?? 'Dashboard') ?></h1>
    <div class="sub"><?= h($pageSub ?? '') ?></div>
  </div>
  <div class="admin-topbar-actions">
    <form method="get" action="pengaduan.php" class="search-box" style="min-width:220px;">
      <i class="fa-solid fa-magnifying-glass ic"></i>
      <input type="search" name="q" placeholder="Cari nomor tiket, judul, atau pelapor...">
    </form>
    <a href="<?= $current === 'notifikasi.php' ? '#' : 'notifikasi.php' ?>" class="icon-btn-plain" style="position:relative;">
      <i class="fa-solid fa-bell"></i>
      <?php if ($unread > 0): ?><span class="notif-dot"></span><?php endif; ?>
    </a>
    <div class="admin-user">
      <div class="avatar"><?= strtoupper(substr($admin['nama'] ?? 'A', 0, 1)) ?></div>
      <div>
        <div style="font-size:13.5px; font-weight:600;"><?= h($admin['nama'] ?? 'Admin') ?></div>
        <div style="font-size:11.5px; color:var(--gray-500);"><?= h($admin['role'] ?? 'Super Admin') ?></div>
      </div>
    </div>
  </div>
</div>
