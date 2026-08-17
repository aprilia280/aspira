-- =========================================================
-- ASPIRA - Aplikasi Aspirasi dan Pengaduan Rakyat
-- Skema database MySQL / MariaDB
-- =========================================================

CREATE DATABASE IF NOT EXISTS aspira_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aspira_db;

-- ---------- Pengguna (admin & masyarakat yang login) ----------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  telepon VARCHAR(30) DEFAULT NULL,
  role ENUM('Super Admin','Verifikator','Petugas','Masyarakat') NOT NULL DEFAULT 'Petugas',
  status ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- Kategori pengaduan ----------
CREATE TABLE IF NOT EXISTS kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL UNIQUE,
  deskripsi VARCHAR(255) DEFAULT '',
  icon VARCHAR(30) NOT NULL DEFAULT 'dots'
) ENGINE=InnoDB;

-- ---------- Pengaduan ----------
CREATE TABLE IF NOT EXISTS pengaduan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  no_tiket VARCHAR(40) NOT NULL UNIQUE,
  judul VARCHAR(200) NOT NULL,
  isi TEXT NOT NULL,
  kategori_id INT DEFAULT NULL,
  kategori_detail VARCHAR(150) DEFAULT '',
  lokasi VARCHAR(255) DEFAULT '',
  instansi VARCHAR(150) DEFAULT '',
  tanggal DATETIME NOT NULL,
  status ENUM('Diverifikasi','Proses','Selesai','Ditolak') NOT NULL DEFAULT 'Diverifikasi',
  nama VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  telepon VARCHAR(30) DEFAULT '',
  lampiran VARCHAR(255) DEFAULT '',
  petugas VARCHAR(150) DEFAULT '-',
  prioritas ENUM('Rendah','Sedang','Tinggi') DEFAULT 'Sedang',
  batas_waktu DATE DEFAULT NULL,
  tanggapan TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
  INDEX idx_status (status),
  INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB;

-- ---------- Riwayat proses / tahapan tiap pengaduan ----------
CREATE TABLE IF NOT EXISTS riwayat_proses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pengaduan_id INT NOT NULL,
  tahap VARCHAR(255) NOT NULL,
  waktu DATETIME NOT NULL,
  oleh VARCHAR(150) NOT NULL,
  FOREIGN KEY (pengaduan_id) REFERENCES pengaduan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Pengaturan sistem (satu baris konfigurasi) ----------
CREATE TABLE IF NOT EXISTS pengaturan (
  id INT PRIMARY KEY DEFAULT 1,
  nama_sistem VARCHAR(150) NOT NULL DEFAULT 'ASPIRA',
  deskripsi VARCHAR(255) DEFAULT '',
  email_kontak VARCHAR(150) DEFAULT '',
  no_telepon VARCHAR(30) DEFAULT '',
  alamat VARCHAR(255) DEFAULT ''
) ENGINE=InnoDB;

-- ---------- Informasi / berita & pengumuman ----------
CREATE TABLE IF NOT EXISTS informasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  isi TEXT,
  status ENUM('Draft','Tayang') NOT NULL DEFAULT 'Draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- Log aktivitas admin ----------
CREATE TABLE IF NOT EXISTS log_aktivitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_nama VARCHAR(150) NOT NULL,
  aksi VARCHAR(255) NOT NULL,
  waktu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- Notifikasi (bell dropdown admin) ----------
CREATE TABLE IF NOT EXISTS notifikasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipe VARCHAR(30) NOT NULL DEFAULT 'info',
  judul VARCHAR(200) NOT NULL,
  deskripsi VARCHAR(255) DEFAULT '',
  dibaca TINYINT(1) NOT NULL DEFAULT 0,
  waktu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
