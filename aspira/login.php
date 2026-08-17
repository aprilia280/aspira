<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Masuk';
$error = '';

if (is_logged_in()) {
    header('Location: ' . (is_staff() ? 'admin/dashboard.php' : 'akun/dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $matched = verify_login($email, $password);
    if (!$matched) {
        $error = 'Email atau password salah.';
    } elseif ($matched['status'] !== 'Aktif') {
        $error = 'Akun Anda nonaktif. Hubungi Super Admin.';
    } else {
        unset($matched['password']);
        $_SESSION['user'] = $matched;
        add_log('Masuk ke sistem', $matched['nama']);
        header('Location: ' . ($matched['role'] === 'Masyarakat' ? 'akun/dashboard.php' : 'admin/dashboard.php'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk - ASPIRA</title>
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
    <h2>Masuk ke akun Anda</h2>
    <p class="sub">Email Perusahaan / Instansi</p>
    <?php if ($error): ?><div class="form-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Masukkan email" value="<?= h($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="password-wrap">
          <input type="password" name="password" placeholder="Masukkan password" required>
          <button type="button" class="toggle-eye"><i class="fa-solid fa-eye"></i></button>
        </div>
      </div>
      <div class="form-between">
        <label class="checkbox-row"><input type="checkbox" name="ingat"> Ingat Saya</label>
        <a href="#">Lupa Password?</a>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>
    <div class="auth-foot">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
    <div class="auth-foot" style="margin-top:14px; font-size:12px; color:var(--gray-400);">
      Demo: admin@diskominfo.go.id / admin123
    </div>
  </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
