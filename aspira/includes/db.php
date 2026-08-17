<?php
/**
 * ASPIRA - Koneksi database MySQL (PDO)
 * Ubah nilai di bawah ini sesuai konfigurasi server Anda.
 * Default sudah sesuai pengaturan bawaan XAMPP (user root, tanpa password).
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'aspira_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die(
                '<div style="font-family:sans-serif; max-width:640px; margin:60px auto; padding:24px; border:1px solid #f3c; border-radius:8px; background:#fff5f5; color:#7a1f1f;">'
                . '<h2 style="margin-top:0;">Tidak bisa terhubung ke database</h2>'
                . '<p>Pastikan MySQL sudah berjalan (mis. lewat XAMPP Control Panel) dan database <code>' . DB_NAME . '</code> sudah dibuat.</p>'
                . '<p>Jalankan <code>database/migrate.php</code> untuk membuat tabel &amp; data contoh secara otomatis.</p>'
                . '<p style="color:#999; font-size:12px;">Detail teknis: ' . htmlspecialchars($e->getMessage()) . '</p>'
                . '</div>'
            );
        }
    }
    return $pdo;
}
