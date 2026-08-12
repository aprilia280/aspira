# ASPIRA — Aplikasi Aspirasi dan Pengaduan Rakyat

Website pengaduan masyarakat dengan **sisi publik** (masyarakat) dan **panel admin**,
kini menggunakan **database MySQL/MariaDB sungguhan** (bukan lagi file JSON).

## Cara menjalankan (XAMPP)

1. **Salin folder ini** ke `htdocs` XAMPP, misalnya jadi `C:\xampp\htdocs\aspira`.
2. Buka **XAMPP Control Panel**, jalankan modul **Apache** dan **MySQL**.
3. **Buat database & tabel** — buka browser ke:
   ```
   http://localhost/aspira/database/migrate.php
   ```
   Skrip ini otomatis membuat database `aspira_db`, seluruh tabel, dan mengisi
   data contoh (kategori, akun admin, beberapa pengaduan). Aman dijalankan
   berulang kali — data yang sudah ada tidak akan ditimpa.
   Alternatif lewat terminal:
   ```bash
   cd C:\xampp\htdocs\aspira\database
   php migrate.php
   ```
4. Buka aplikasi di `http://localhost/aspira`.
5. **(Disarankan)** setelah migrasi sukses, hapus atau pindahkan
   `database/migrate.php` keluar dari folder publik.

### Kredensial database

Konfigurasi ada di `includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'aspira_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```
Ini sudah sesuai default XAMPP (`root` tanpa password). Jika MySQL Anda
memakai user/password lain, ubah nilai di file tersebut.

## Login demo (panel admin)

| Email                     | Password  | Role         |
|---------------------------|-----------|--------------|
| admin@diskominfo.go.id    | admin123  | Super Admin  |
| verifikator@mpp.go.id     | verif123  | Verifikator  |
| kelurahan@mpp.go.id       | kel123    | Petugas      |
| layanan@mpp.go.id         | layanan123| Petugas (Nonaktif) |

Password disimpan ter-hash (`password_hash`/`password_verify`), bukan plain text.

## Fitur

**Publik**
- Beranda dengan statistik real-time, kategori pengaduan
- Ajukan pengaduan lengkap dengan **upload lampiran sungguhan** (jpg/jpeg/png/pdf, maks 5MB)
- Cek status pengaduan via nomor tiket atau email
- Informasi layanan & **berita/pengumuman** (dikelola admin, data asli dari database)
- FAQ, Kontak (info kontak mengikuti Pengaturan Sistem)
- Registrasi & login akun

**Admin**
- Dashboard dengan grafik tren **data asli** (bukan angka contoh statis)
- Kelola pengaduan: filter status/kategori/pencarian real via database, **export ke CSV/Excel**
- Verifikasi pengaduan (Valid / Tidak Valid) dengan pencatatan riwayat proses
- Tindak lanjut: penugasan ke dinas, prioritas, batas waktu, **upload lampiran**
- Kelola kategori — tambah, **edit**, hapus, dengan ikon
- Kelola pengguna — tambah, **edit**, aktif/nonaktif, hapus, password ter-hash
- Laporan dengan filter tanggal/kategori real + export Excel
- Kelola Informasi (berita & pengumuman) — tambah/edit/hapus/status tayang
- Notifikasi sungguhan (pengaduan baru, verifikasi, selesai) dengan tandai-dibaca
- Log Aktivitas — mencatat otomatis setiap aksi admin (login, verifikasi, tindak
  lanjut, perubahan kategori/pengguna/pengaturan/informasi)
- Pengaturan sistem tersimpan di database dan **langsung memengaruhi** nama
  sistem, deskripsi, dan info kontak yang tampil di halaman publik

## Struktur folder

```
aspira/
├── database/
│   ├── schema.sql             Skema tabel MySQL
│   └── migrate.php            Skrip migrasi & seed (jalankan sekali)
├── includes/
│   ├── db.php                 Koneksi PDO ke MySQL
│   ├── functions.php          Semua query & helper (pengganti akses JSON)
│   ├── logo.php                Logo SVG ASPIRA (dipakai berulang)
│   ├── config.php, header.php, footer.php, admin-sidebar.php, admin-topbar.php
├── uploads/                   Lampiran pengaduan & tindak lanjut (upload asli)
├── admin/                     Panel admin (lihat daftar di atas)
├── assets/css/style.css       Tema warna biru ASPIRA
├── assets/js/main.js, admin.js
└── *.php                      Halaman publik (index, ajukan-pengaduan, dst.)
```

## Catatan keamanan

- Folder `uploads/` diberi `.htaccess` yang memblokir eksekusi PHP, jadi file
  yang diunggah tidak bisa dijalankan sebagai skrip.
- Validasi upload: ekstensi (jpg/jpeg/png/pdf) & ukuran (maks 5MB) dicek di server.
- Semua query menggunakan **prepared statements** (PDO) untuk mencegah SQL injection.
- Password memakai `password_hash()` (bcrypt) — tidak ada password plain text di database.
