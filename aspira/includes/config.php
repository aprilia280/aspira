<?php
/**
 * ASPIRA - Aplikasi Aspirasi dan Pengaduan Rakyat
 * Konfigurasi dasar aplikasi
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));

// Deteksi base URL relatif agar link antar folder (admin/, akun/, dan root) tetap benar
function base_url($path = '') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $sub = basename($scriptDir);
    $prefix = in_array($sub, ['admin', 'akun'], true) ? '../' : '';
    return $prefix . ltrim($path, '/');
}

/** Ada pengguna (staf ATAU masyarakat) yang sedang login. */
function is_logged_in() {
    return isset($_SESSION['user']);
}

/** Data pengguna yang sedang login (siapa pun rolenya), atau null. */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/** True jika yang login adalah staf (Super Admin/Verifikator/Petugas), bukan masyarakat biasa. */
function is_staff() {
    $u = current_user();
    return $u && $u['role'] !== 'Masyarakat';
}

/** Kompatibel dengan kode admin lama: hanya mengembalikan data jika yang login adalah staf. */
function current_admin() {
    return is_staff() ? current_user() : null;
}

/** Wajib login sebagai staf untuk mengakses halaman di /admin/. */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
    if (!is_staff()) {
        header('Location: ' . base_url('akun/dashboard.php'));
        exit;
    }
}

/** Wajib login (peran apa pun) untuk mengakses halaman akun masyarakat di /akun/. */
function require_user_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}
