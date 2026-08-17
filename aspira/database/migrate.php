<?php
/**
 * ASPIRA - Skrip migrasi & seed database MySQL
 * -----------------------------------------------
 * Jalankan SEKALI saja untuk membuat tabel dan mengisi data contoh.
 *
 * Cara pakai XAMPP:
 *   1. Pastikan MySQL di XAMPP Control Panel sudah running.
 *   2. Buka terminal di folder ini lalu jalankan:
 *        php migrate.php
 *      ATAU buka lewat browser: http://localhost/aspira/database/migrate.php
 *   3. Setelah sukses, sebaiknya file ini dihapus/dipindah dari folder publik.
 */

require_once __DIR__ . '/../includes/db.php';

function out($msg) {
    echo (php_sapi_name() === 'cli') ? $msg . "\n" : $msg . "<br>";
}

try {
    // 0) Pastikan database-nya sendiri sudah ada sebelum konek ke dalamnya
    $bootstrap = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $bootstrap->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    out('Database "' . DB_NAME . '" siap.');

    $pdo = db();

    // 1) Jalankan schema.sql (buat tabel jika belum ada)
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    // Hilangkan statement CREATE DATABASE / USE karena koneksi sudah ke DB tujuan
    $schema = preg_replace('/CREATE DATABASE.*?;/is', '', $schema);
    $schema = preg_replace('/USE\s+\w+\s*;/is', '', $schema);
    foreach (array_filter(array_map('trim', explode(';', $schema))) as $stmt) {
        $pdo->exec($stmt);
    }
    out('Tabel berhasil dibuat / sudah tersedia.');

    // 1b) Tambahkan kolom baru ke instalasi lama tanpa menghapus data (aman dijalankan berulang)
    $col = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pengaduan' AND COLUMN_NAME = 'instansi'")->fetchColumn();
    if ($col == 0) {
        $pdo->exec("ALTER TABLE pengaduan ADD COLUMN instansi VARCHAR(150) DEFAULT '' AFTER lokasi");
        out('Kolom "instansi" berhasil ditambahkan ke tabel pengaduan.');
    }
    $col2 = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pengaduan' AND COLUMN_NAME = 'kategori_detail'")->fetchColumn();
    if ($col2 == 0) {
        $pdo->exec("ALTER TABLE pengaduan ADD COLUMN kategori_detail VARCHAR(150) DEFAULT '' AFTER kategori_id");
        out('Kolom "kategori_detail" berhasil ditambahkan ke tabel pengaduan.');
    }

    // 2) Seed users (skip jika sudah ada data)
    $count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count == 0) {
        $users = [
            ['Admin', 'admin@diskominfo.go.id', 'admin123', 'Super Admin', 'Aktif'],
            ['Verifikator', 'verifikator@mpp.go.id', 'verif123', 'Verifikator', 'Aktif'],
            ['Admin Kelurahan', 'kelurahan@mpp.go.id', 'kel123', 'Petugas', 'Aktif'],
            ['Admin Layanan', 'layanan@mpp.go.id', 'layanan123', 'Petugas', 'Nonaktif'],
        ];
        $stmt = $pdo->prepare('INSERT INTO users (nama, email, password, role, status) VALUES (?,?,?,?,?)');
        foreach ($users as $u) {
            $stmt->execute([$u[0], $u[1], password_hash($u[2], PASSWORD_DEFAULT), $u[3], $u[4]]);
        }
        out('Pengguna demo berhasil ditambahkan (lihat README untuk daftar login).');
    } else {
        out('Tabel users sudah berisi data, dilewati.');
    }

    // 2b) Ganti email admin lama (admin@mpp.go.id) ke domain diskominfo, aman dijalankan berulang
    $chk = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $chk->execute(['admin@mpp.go.id']);
    if ($chk->fetchColumn() > 0) {
        $pdo->prepare("UPDATE users SET email = 'admin@diskominfo.go.id' WHERE email = 'admin@mpp.go.id'")->execute();
        out('Email admin berhasil diganti ke admin@diskominfo.go.id.');
    }

    // 3) Seed kategori
    $count = $pdo->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
    if ($count == 0) {
        $kategori = [
            ['Administrasi', 'Masalah dokumen, surat, dan pelayanan administrasi.', 'file-text'],
            ['Infrastruktur', 'Jalan rusak, lampu jalan, drainase dan lainnya.', 'road'],
            ['Pelayanan Publik', 'Keluhan terkait pelayanan petugas atau sistem.', 'users'],
            ['Lingkungan', 'Sampah, kebersihan, pencemaran, dll.', 'leaf'],
            ['Lainnya', 'Keluhan atau saran lainnya.', 'dots'],
        ];
        $stmt = $pdo->prepare('INSERT INTO kategori (nama, deskripsi, icon) VALUES (?,?,?)');
        foreach ($kategori as $k) { $stmt->execute($k); }
        out('Kategori demo berhasil ditambahkan.');
    } else {
        out('Tabel kategori sudah berisi data, dilewati.');
    }

    // 3b) Tambahkan kategori baru yang belum ada (aman dijalankan berulang, tidak mengubah yang sudah ada)
    $kategoriBaru = [
        ['Administrasi Kependudukan', 'KTP, KK, akta, dan dokumen kependudukan lainnya.', 'file-text'],
        ['Pendidikan & Sekolah', 'Keluhan seputar pendidikan dan sekolah.', 'users'],
        ['Bantuan Sosial', 'Keluhan atau pertanyaan seputar bantuan sosial.', 'users'],
        ['Ketertiban Umum (Satpol PP)', 'Ketertiban umum dan penertiban wilayah.', 'dots'],
        ['Pungli', 'Laporan dugaan pungutan liar.', 'dots'],
        ['MBG', 'Keluhan atau masukan seputar program MBG.', 'dots'],
    ];
    $cekStmt = $pdo->prepare('SELECT COUNT(*) FROM kategori WHERE nama = ?');
    $insStmt = $pdo->prepare('INSERT INTO kategori (nama, deskripsi, icon) VALUES (?,?,?)');
    $ditambahkan = 0;
    foreach ($kategoriBaru as $k) {
        $cekStmt->execute([$k[0]]);
        if ($cekStmt->fetchColumn() == 0) {
            $insStmt->execute($k);
            $ditambahkan++;
        }
    }
    if ($ditambahkan > 0) {
        out($ditambahkan . ' kategori baru berhasil ditambahkan.');
    } else {
        out('Kategori baru sudah tersedia, dilewati.');
    }

    // 3c) Pakai ikon gambar khusus untuk kategori Administrasi Kependudukan
    $pdo->prepare("UPDATE kategori SET icon = 'idcard' WHERE nama = 'Administrasi Kependudukan'")->execute();
    $pdo->prepare("UPDATE kategori SET icon = 'graduation' WHERE nama = 'Pendidikan & Sekolah'")->execute();
    $pdo->prepare("UPDATE kategori SET icon = 'bansos' WHERE nama = 'Bantuan Sosial'")->execute();
    $pdo->prepare("UPDATE kategori SET icon = 'shield' WHERE nama = 'Ketertiban Umum (Satpol PP)'")->execute();
    $pdo->prepare("UPDATE kategori SET icon = 'pungli' WHERE nama = 'Pungli'")->execute();
    $pdo->prepare("UPDATE kategori SET icon = 'mbg' WHERE nama = 'MBG'")->execute();

    // 4) Seed pengaturan
    $count = $pdo->query('SELECT COUNT(*) FROM pengaturan')->fetchColumn();
    if ($count == 0) {
        $pdo->prepare('INSERT INTO pengaturan (id, nama_sistem, deskripsi, email_kontak, no_telepon, alamat) VALUES (1,?,?,?,?,?)')
            ->execute(['ASPIRA - Diskominfo', 'Sistem pengaduan masyarakat untuk pelayanan publik', 'info@diskominfo.go.id', '0800-1234-5678', 'Jl. Angkrek No. 103, Kel. Situ, Kec. Sumedang Utara, Kab. Sumedang, Jawa Barat']);
        out('Pengaturan sistem default berhasil ditambahkan.');
    } else {
        out('Tabel pengaturan sudah berisi data, dilewati.');
    }

    // 5) Seed contoh pengaduan (agar dashboard tidak kosong)
    $count = $pdo->query('SELECT COUNT(*) FROM pengaduan')->fetchColumn();
    if ($count == 0) {
        $katId = $pdo->query("SELECT id FROM kategori WHERE nama='Infrastruktur'")->fetchColumn();
        $katId2 = $pdo->query("SELECT id FROM kategori WHERE nama='Pelayanan Publik'")->fetchColumn();
        $contoh = [
            ['2024000123', 'Jalan rusak di depan kantor kelurahan', 'Jalan di depan kantor kelurahan sudah rusak parah dan membahayakan pengguna jalan.', $katId, 'Jl. Sudirman No. 10, Kota Contoh', '2024-06-19 10:20:00', 'Proses', 'Andi Pratama', 'andi.pratama@email.com', '0813-3456-7890', '', 'Admin Infrastruktur', 'Sedang ditangani oleh Dinas PU.'],
            ['2024000124', 'Lampu penerangan jalan mati', 'Lampu penerangan jalan di sekitar jalan utama tidak berfungsi sudah 3 hari.', $katId, 'Jl. Merdeka, Kota Contoh', '2024-06-19 09:35:00', 'Selesai', 'Siti Nurhaliza', 'siti.n@email.com', '0812-1111-2222', '', 'Dinas Perhubungan', 'Terima kasih atas laporan Anda. Lampu jalan sudah diperbaiki.'],
            ['2024000121', 'Pelayanan administrasi terlalu lama', 'Proses pengurusan surat keterangan di kelurahan memakan waktu lebih dari seminggu.', $katId2, 'Kantor Kelurahan Situ', '2024-06-18 08:00:00', 'Diverifikasi', 'Budi Santoso', 'budi.santoso@email.com', '0813-2222-3333', '', '-', 'Pengaduan Anda telah diterima dan sedang menunggu proses verifikasi.'],
        ];
        $stmt = $pdo->prepare('INSERT INTO pengaduan (no_tiket, judul, isi, kategori_id, lokasi, tanggal, status, nama, email, telepon, lampiran, petugas, tanggapan) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        foreach ($contoh as $c) { $stmt->execute($c); }
        $idAll = $pdo->query('SELECT id, no_tiket FROM pengaduan')->fetchAll(PDO::FETCH_KEY_PAIR);
        $riwayatStmt = $pdo->prepare('INSERT INTO riwayat_proses (pengaduan_id, tahap, waktu, oleh) VALUES (?,?,?,?)');
        $riwayatData = [
            '2024000123' => [
                ['Pengaduan Diterima', '2024-06-19 10:20:00', 'Sistem'],
                ['Diverifikasi', '2024-06-19 10:35:00', 'Admin Verifikator'],
                ['Ditugaskan ke Dinas PU', '2024-06-19 11:00:00', 'Admin Infrastruktur'],
            ],
            '2024000124' => [
                ['Pengaduan Diterima', '2024-06-19 09:35:00', 'Sistem'],
                ['Diverifikasi', '2024-06-19 10:00:00', 'Admin Verifikator'],
                ['Diproses', '2024-06-19 10:30:00', 'Dinas Perhubungan'],
                ['Selesai', '2024-06-20 14:28:00', 'Dinas Perhubungan'],
            ],
            '2024000121' => [
                ['Pengaduan Diterima', '2024-06-18 08:00:00', 'Sistem'],
            ],
        ];
        foreach (array_flip($idAll) as $tiket => $id) {
            foreach ($riwayatData[$tiket] ?? [] as $r) {
                $riwayatStmt->execute([$id, $r[0], $r[1], $r[2]]);
            }
        }
        out('Contoh data pengaduan berhasil ditambahkan.');
    } else {
        out('Tabel pengaduan sudah berisi data, dilewati.');
    }

    out('');
    out('Migrasi selesai. Silakan buka aplikasi dan login dengan akun demo di README.md.');
} catch (Throwable $e) {
    out('TERJADI KESALAHAN: ' . $e->getMessage());
}
