<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Daftar';
$error = '';
$success = false;

if (is_logged_in()) {
    header('Location: ' . (is_staff() ? 'admin/dashboard.php' : 'akun/dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $setuju = isset($_POST['setuju']);

    $exists = find_user_by_email($email) !== null;

    if (!$nama || !$email || !$password) {
        $error = 'Mohon lengkapi semua kolom bertanda wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sama dengan password.';
    } elseif (!$setuju) {
        $error = 'Anda harus menyetujui Syarat & Ketentuan.';
    } elseif ($exists) {
        $error = 'Email sudah terdaftar. Silakan masuk.';
    } else {
        $newId = insert_user($nama, $email, $password, 'Masyarakat', 'Aktif', $telepon ?: null);
        $_SESSION['user'] = find_user_by_id($newId);
        unset($_SESSION['user']['password']);
        add_log('Mendaftar akun baru', $nama);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar - ASPIRA</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-box">
    <a href="index.php" class="brand">
      <?php render_logo(36); ?>
      <span>ASPIRA</span>
    </a>
    <h2>Buat akun baru</h2>
    <p class="sub">Daftar untuk mulai mengajukan pengaduan</p>
    <?php if ($success): ?>
      <div class="form-success">Akun berhasil dibuat dan Anda sudah masuk!</div>
      <a href="akun/dashboard.php" class="btn btn-primary btn-block">Ke Dashboard Saya</a>
    <?php else: ?>
      <?php if ($error): ?><div class="form-error"><?= h($error) ?></div><?php endif; ?>
      <form method="post" id="registerForm">
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" placeholder="Masukkan nama lengkap" value="<?= h($_POST['nama'] ?? '') ?>" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" value="<?= h($_POST['email'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>No. Telepon</label>
            <input type="tel" name="telepon" placeholder="Masukkan nomor telepon" value="<?= h($_POST['telepon'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Password</label>
            <input type="password" id="password" name="password" placeholder="Buat password" required>
          </div>
          <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Konfirmasi password" required>
          </div>
        </div>
        <div class="form-group">
          <label class="checkbox-row"><input type="checkbox" name="setuju"> Saya setuju dengan <a href="#" style="color:var(--blue-600); font-weight:600;">Syarat &amp; Ketentuan</a></label>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
      </form>
    <?php endif; ?>
    <div class="auth-foot">Sudah punya akun? <a href="login.php">Masuk di sini</a></div>
  </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
