<?php
require_once __DIR__ . '/functions.php';
$current = basename($_SERVER['SCRIPT_NAME']);
function nav_active($file, $current) { return $file === $current ? 'active' : ''; }
$siteSettings = get_settings();
$siteName = $siteSettings['nama_sistem'] ?: 'ASPIRA';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? h($pageTitle) . ' - ' . h($siteName) : h($siteName) . ' - Aplikasi Aspirasi dan Pengaduan Rakyat' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
  <nav class="navbar">
    <a href="<?= base_url('index.php') ?>" class="brand">
      <?php render_logo(38); ?>
      <span><?= h($siteName) ?><span class="brand-sub">Aplikasi Aspirasi dan Pengaduan Rakyat</span></span>
    </a>
    <div class="nav-links">
      <a href="<?= base_url('index.php') ?>" class="<?= nav_active('index.php', $current) ?>">Beranda</a>
      <a href="<?= base_url('ajukan-pengaduan.php') ?>" class="<?= nav_active('ajukan-pengaduan.php', $current) ?>">Ajukan Pengaduan</a>
      <a href="<?= base_url('cek-pengaduan.php') ?>" class="<?= nav_active('cek-pengaduan.php', $current) ?>">Cek Pengaduan</a>
      <a href="<?= base_url('informasi.php') ?>" class="<?= nav_active('informasi.php', $current) ?>">Informasi</a>
      <a href="<?= base_url('faq.php') ?>" class="<?= nav_active('faq.php', $current) ?>">FAQ</a>
      <a href="<?= base_url('kontak.php') ?>" class="<?= nav_active('kontak.php', $current) ?>">Kontak</a>
    </div>
    <div class="nav-actions">
      <?php if (is_staff()): ?>
        <a href="<?= base_url('admin/dashboard.php') ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-gauge"></i> Dashboard</a>
      <?php elseif (is_logged_in()): $u = current_user(); ?>
        <a href="<?= base_url('akun/dashboard.php') ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-user"></i> <?= h($u['nama']) ?></a>
        <a href="<?= base_url('admin/logout.php') ?>" class="btn btn-ghost btn-sm">Keluar</a>
      <?php else: ?>
        <a href="<?= base_url('login.php') ?>" class="btn btn-outline btn-sm">Masuk</a>
        <a href="<?= base_url('register.php') ?>" class="btn btn-primary btn-sm">Daftar</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
