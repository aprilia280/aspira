<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/logo.php';

/* =========================================================
   Helper umum
   ========================================================= */
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Kembalikan kelas CSS badge sesuai status pengaduan */
function status_badge_class($status) {
    switch ($status) {
        case 'Selesai': return 'badge badge-success';
        case 'Proses': return 'badge badge-warning';
        case 'Ditolak': return 'badge badge-danger';
        case 'Diverifikasi': return 'badge badge-info';
        default: return 'badge badge-default';
    }
}

/* =========================================================
   KATEGORI
   ========================================================= */
function get_kategori() {
    $sql = "SELECT k.id, k.nama, k.deskripsi, k.icon,
                   (SELECT COUNT(*) FROM pengaduan p WHERE p.kategori_id = k.id) AS jumlah
            FROM kategori k ORDER BY k.id ASC";
    return db()->query($sql)->fetchAll();
}

function find_kategori_by_nama($nama) {
    $stmt = db()->prepare('SELECT * FROM kategori WHERE nama = ? LIMIT 1');
    $stmt->execute([$nama]);
    return $stmt->fetch() ?: null;
}

function insert_kategori($nama, $deskripsi, $icon) {
    $stmt = db()->prepare('INSERT INTO kategori (nama, deskripsi, icon) VALUES (?,?,?)');
    $stmt->execute([$nama, $deskripsi, $icon ?: 'dots']);
    return db()->lastInsertId();
}

function update_kategori($id, $nama, $deskripsi, $icon) {
    $stmt = db()->prepare('UPDATE kategori SET nama=?, deskripsi=?, icon=? WHERE id=?');
    $stmt->execute([$nama, $deskripsi, $icon ?: 'dots', $id]);
}

function delete_kategori($id) {
    $stmt = db()->prepare('DELETE FROM kategori WHERE id = ?');
    $stmt->execute([$id]);
}

/** Daftar instansi/dinas resmi - dipakai di form Ajukan Pengaduan & Tindak Lanjut supaya selalu sinkron. */
function get_instansi_list() {
    return [
        'Sekretariat Daerah (Setda)',
        'Sekretariat DPRD',
        'Inspektorat Daerah',
        'Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah (Bapperida)',
        'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)',
        'Badan Pengelolaan Pendapatan Daerah (Bappenda)',
        'Badan Pengelolaan Keuangan dan Aset Daerah (BPKAD)',
        'Badan Penanggulangan Bencana Daerah (BPBD)',
        'Dinas Pendidikan (Disdik)',
        'Dinas Kesehatan (Dinkes)',
        'Dinas Pekerjaan Umum dan Tata Ruang (PUTR)',
        'Dinas Perumahan, Kawasan Permukiman dan Pertanahan (Perkimtan)',
        'Dinas Sosial (Dinsos)',
        'Satuan Polisi Pamong Praja (Satpol PP), Pemadam Kebakaran, dan Penyelamatan',
        'Dinas Tenaga Kerja dan Transmigrasi (Disnakertrans)',
        'Dinas Lingkungan Hidup dan Kehutanan (DLHK)',
        'Dinas Kependudukan dan Pencatatan Sipil (Disdukcapil)',
        'Dinas Pengendalian Penduduk dan Keluarga Berencana (Dalduk KB)',
        'Dinas Perhubungan (Dishub)',
        'Dinas Komunikasi, Informatika, Sandi dan Statistik (Diskominfosanditik)',
        'Dinas Koperasi, Usaha Kecil Menengah, Perdagangan dan Perindustrian (DiskopUKMPP)',
        'Dinas Pertanian dan Ketahanan Pangan',
        'Dinas Perikanan dan Peternakan',
        'Dinas Pariwisata, Kebudayaan, Pemuda dan Olahraga (Disparbudpora)',
        'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu (DPMPTSP)',
        'Dinas Arsip dan Perpustakaan Daerah',
    ];
}

/* =========================================================
   USERS
   ========================================================= */
function get_users() {
    return db()->query('SELECT id, nama, email, telepon, role, status, created_at FROM users ORDER BY id ASC')->fetchAll();
}

function find_user_by_id($id) {
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function find_user_by_email($email) {
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    return $stmt->fetch() ?: null;
}

function insert_user($nama, $email, $passwordPlain, $role = 'Petugas', $status = 'Aktif', $telepon = null) {
    $stmt = db()->prepare('INSERT INTO users (nama, email, password, telepon, role, status) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$nama, $email, password_hash($passwordPlain, PASSWORD_DEFAULT), $telepon, $role, $status]);
    return db()->lastInsertId();
}

function update_user($id, $nama, $email, $role) {
    $stmt = db()->prepare('UPDATE users SET nama=?, email=?, role=? WHERE id=?');
    $stmt->execute([$nama, $email, $role, $id]);
}

function toggle_user_status($id) {
    $stmt = db()->prepare("UPDATE users SET status = IF(status='Aktif','Nonaktif','Aktif') WHERE id = ?");
    $stmt->execute([$id]);
}

function delete_user($id) {
    $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
}

/** Update profil sendiri (nama & telepon saja - email/role tidak diubah lewat sini demi keamanan). */
function update_profile_self($id, $nama, $telepon) {
    $stmt = db()->prepare('UPDATE users SET nama=?, telepon=? WHERE id=?');
    $stmt->execute([$nama, $telepon, $id]);
}

/** Ganti password akun sendiri setelah verifikasi password lama di pemanggil. */
function update_password_self($id, $newPasswordPlain) {
    $stmt = db()->prepare('UPDATE users SET password=? WHERE id=?');
    $stmt->execute([password_hash($newPasswordPlain, PASSWORD_DEFAULT), $id]);
}

/** Verifikasi login: cek email + password (password di-hash dengan password_hash). */
function verify_login($email, $passwordPlain) {
    $user = find_user_by_email($email);
    if (!$user) return null;
    if (!password_verify($passwordPlain, $user['password'])) return null;
    return $user;
}

/* =========================================================
   PENGADUAN
   ========================================================= */
function generate_ticket_number() {
    $year = date('Y');
    $stmt = db()->prepare("SELECT no_tiket FROM pengaduan WHERE no_tiket LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$year . '%']);
    $last = $stmt->fetchColumn();
    $next = 1;
    if ($last && preg_match('/^' . $year . '0*(\d+)$/', $last, $m)) {
        $next = (int)$m[1] + 1;
    }
    return $year . str_pad($next, 6, '0', STR_PAD_LEFT);
}

function _attach_riwayat(array $rows) {
    if (!$rows) return $rows;
    $ids = array_column($rows, 'id');
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM riwayat_proses WHERE pengaduan_id IN ($in) ORDER BY waktu ASC, id ASC");
    $stmt->execute($ids);
    $byPengaduan = [];
    foreach ($stmt->fetchAll() as $r) {
        $byPengaduan[$r['pengaduan_id']][] = [
            'tahap' => $r['tahap'],
            'waktu' => date('d M Y H:i', strtotime($r['waktu'])),
            'oleh' => $r['oleh'],
        ];
    }
    foreach ($rows as &$row) {
        $row['kategori'] = $row['kategori_nama'] ?: '-';
        $row['riwayat'] = $byPengaduan[$row['id']] ?? [];
    }
    unset($row);
    return $rows;
}

/**
 * Ambil daftar pengaduan. $filters bisa berisi: status, kategori (nama),
 * email, search (cari di no_tiket/judul/nama), dari, sampai (Y-m-d).
 */
function get_pengaduan(array $filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['status']) && $filters['status'] !== 'Semua Status') {
        $where[] = 'p.status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['kategori']) && $filters['kategori'] !== 'Semua Kategori') {
        $where[] = 'k.nama = ?';
        $params[] = $filters['kategori'];
    }
    if (!empty($filters['instansi']) && $filters['instansi'] !== 'Semua Instansi') {
        $where[] = 'p.instansi = ?';
        $params[] = $filters['instansi'];
    }
    if (!empty($filters['email'])) {
        $where[] = 'p.email = ?';
        $params[] = $filters['email'];
    }
    if (!empty($filters['search'])) {
        $where[] = '(p.no_tiket LIKE ? OR p.judul LIKE ? OR p.nama LIKE ?)';
        $like = '%' . $filters['search'] . '%';
        array_push($params, $like, $like, $like);
    }
    if (!empty($filters['dari'])) {
        $where[] = 'DATE(p.tanggal) >= ?';
        $params[] = $filters['dari'];
    }
    if (!empty($filters['sampai'])) {
        $where[] = 'DATE(p.tanggal) <= ?';
        $params[] = $filters['sampai'];
    }

    $sql = 'SELECT p.*, k.nama AS kategori_nama FROM pengaduan p LEFT JOIN kategori k ON k.id = p.kategori_id';
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY p.tanggal DESC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return _attach_riwayat($stmt->fetchAll());
}

function find_pengaduan($no_tiket) {
    $stmt = db()->prepare('SELECT p.*, k.nama AS kategori_nama FROM pengaduan p LEFT JOIN kategori k ON k.id = p.kategori_id WHERE p.no_tiket = ? LIMIT 1');
    $stmt->execute([$no_tiket]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $rows = _attach_riwayat([$row]);
    return $rows[0];
}

/** Simpan pengaduan baru (dari form publik). $data: judul, isi, kategori (nama), lokasi, nama, email, telepon, lampiran. */
function insert_pengaduan(array $data) {
    $kat = find_kategori_by_nama($data['kategori'] ?? '');
    $noTiket = generate_ticket_number();
    $now = date('Y-m-d H:i:s');

    $stmt = db()->prepare('INSERT INTO pengaduan
        (no_tiket, judul, isi, kategori_id, kategori_detail, lokasi, instansi, tanggal, status, nama, email, telepon, lampiran, petugas, tanggapan)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $noTiket,
        $data['judul'],
        $data['isi'],
        $kat['id'] ?? null,
        $data['kategori_detail'] ?? '',
        $data['lokasi'] ?: '-',
        $data['instansi'] ?? '',
        $now,
        'Diverifikasi',
        $data['nama'],
        $data['email'],
        $data['telepon'] ?? '',
        $data['lampiran'] ?? '',
        '-',
        'Pengaduan Anda telah diterima dan sedang menunggu proses verifikasi.',
    ]);
    $id = db()->lastInsertId();
    db()->prepare('INSERT INTO riwayat_proses (pengaduan_id, tahap, waktu, oleh) VALUES (?,?,?,?)')
        ->execute([$id, 'Pengaduan Diterima', $now, 'Sistem']);

    add_notifikasi('inbox', 'Pengaduan baru masuk', $noTiket . ' - ' . $data['judul']);
    add_log('Mengajukan pengaduan baru ' . $noTiket, $data['nama']);

    return $noTiket;
}

/** Perbarui field pengaduan tertentu berdasarkan no_tiket. $fields adalah pasangan kolom => nilai. */
function update_pengaduan($no_tiket, array $fields) {
    $allowed = ['status', 'petugas', 'tanggapan', 'prioritas', 'batas_waktu', 'lampiran', 'kategori_id', 'instansi'];
    $set = [];
    $params = [];
    foreach ($fields as $col => $val) {
        if (!in_array($col, $allowed, true)) continue;
        $set[] = "$col = ?";
        $params[] = $val;
    }
    if (!$set) return;
    $params[] = $no_tiket;
    $sql = 'UPDATE pengaduan SET ' . implode(', ', $set) . ' WHERE no_tiket = ?';
    db()->prepare($sql)->execute($params);
}

function add_riwayat($no_tiket, $tahap, $oleh = 'Sistem', $waktu = null) {
    $stmt = db()->prepare('SELECT id FROM pengaduan WHERE no_tiket = ?');
    $stmt->execute([$no_tiket]);
    $id = $stmt->fetchColumn();
    if (!$id) return;
    $waktu = $waktu ?: date('Y-m-d H:i:s');
    db()->prepare('INSERT INTO riwayat_proses (pengaduan_id, tahap, waktu, oleh) VALUES (?,?,?,?)')
        ->execute([$id, $tahap, $waktu, $oleh]);
}

function stats_ringkasan(array $filters = []) {
    $where = [];
    $params = [];
    if (!empty($filters['dari'])) { $where[] = 'DATE(p.tanggal) >= ?'; $params[] = $filters['dari']; }
    if (!empty($filters['sampai'])) { $where[] = 'DATE(p.tanggal) <= ?'; $params[] = $filters['sampai']; }
    if (!empty($filters['instansi']) && $filters['instansi'] !== 'Semua Instansi') { $where[] = 'p.instansi = ?'; $params[] = $filters['instansi']; }
    if (!empty($filters['kategori']) && $filters['kategori'] !== 'Semua Kategori') { $where[] = 'k.nama = ?'; $params[] = $filters['kategori']; }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    $base = 'FROM pengaduan p LEFT JOIN kategori k ON k.id = p.kategori_id';

    $total = db()->prepare("SELECT COUNT(*) $base $whereSql");
    $total->execute($params);
    $selesai = db()->prepare("SELECT COUNT(*) $base $whereSql " . ($where ? 'AND' : 'WHERE') . " p.status='Selesai'");
    $selesai->execute($params);
    $proses = db()->prepare("SELECT COUNT(*) $base $whereSql " . ($where ? 'AND' : 'WHERE') . " p.status IN ('Proses','Diverifikasi')");
    $proses->execute($params);
    $ditolak = db()->prepare("SELECT COUNT(*) $base $whereSql " . ($where ? 'AND' : 'WHERE') . " p.status='Ditolak'");
    $ditolak->execute($params);

    return [
        'total' => (int)$total->fetchColumn(),
        'selesai' => (int)$selesai->fetchColumn(),
        'proses' => (int)$proses->fetchColumn(),
        'ditolak' => (int)$ditolak->fetchColumn(),
    ];
}

/** Data grafik tren: jumlah pengaduan per hari, N hari terakhir (data asli dari database). */
function trend_pengaduan($days = 8, array $filters = []) {
    $where = ['p.tanggal >= (CURDATE() - INTERVAL ? DAY)'];
    $params = [$days];
    if (!empty($filters['kategori']) && $filters['kategori'] !== 'Semua Kategori') {
        $where[] = 'k.nama = ?';
        $params[] = $filters['kategori'];
    }
    if (!empty($filters['instansi']) && $filters['instansi'] !== 'Semua Instansi') {
        $where[] = 'p.instansi = ?';
        $params[] = $filters['instansi'];
    }
    $stmt = db()->prepare(
        "SELECT DATE(p.tanggal) d, COUNT(*) c FROM pengaduan p LEFT JOIN kategori k ON k.id = p.kategori_id
         WHERE " . implode(' AND ', $where) . "
         GROUP BY DATE(p.tanggal) ORDER BY d ASC"
    );
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    $map = [];
    foreach ($rows as $r) { $map[$r['d']] = (int)$r['c']; }

    $labels = [];
    $data = [];
    for ($i = $days; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i day"));
        $labels[] = date('d M', strtotime($d));
        $data[] = $map[$d] ?? 0;
    }
    return [$labels, $data];
}

/* =========================================================
   PENGATURAN SISTEM
   ========================================================= */
function get_settings() {
    $row = db()->query('SELECT * FROM pengaturan WHERE id = 1')->fetch();
    return $row ?: ['nama_sistem' => 'ASPIRA', 'deskripsi' => '', 'email_kontak' => '', 'no_telepon' => '', 'alamat' => ''];
}

function save_settings(array $data) {
    $stmt = db()->prepare(
        'INSERT INTO pengaturan (id, nama_sistem, deskripsi, email_kontak, no_telepon, alamat)
         VALUES (1, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE nama_sistem=VALUES(nama_sistem), deskripsi=VALUES(deskripsi),
           email_kontak=VALUES(email_kontak), no_telepon=VALUES(no_telepon), alamat=VALUES(alamat)'
    );
    $stmt->execute([$data['nama_sistem'], $data['deskripsi'], $data['email_kontak'], $data['no_telepon'], $data['alamat']]);
}

/* =========================================================
   INFORMASI / BERITA & PENGUMUMAN
   ========================================================= */
function get_informasi($onlyTayang = false) {
    $sql = 'SELECT * FROM informasi';
    if ($onlyTayang) $sql .= " WHERE status = 'Tayang'";
    $sql .= ' ORDER BY created_at DESC';
    return db()->query($sql)->fetchAll();
}

function find_informasi($id) {
    $stmt = db()->prepare('SELECT * FROM informasi WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function insert_informasi($judul, $isi, $status) {
    db()->prepare('INSERT INTO informasi (judul, isi, status) VALUES (?,?,?)')->execute([$judul, $isi, $status]);
}

function update_informasi($id, $judul, $isi, $status) {
    db()->prepare('UPDATE informasi SET judul=?, isi=?, status=? WHERE id=?')->execute([$judul, $isi, $status, $id]);
}

function delete_informasi($id) {
    db()->prepare('DELETE FROM informasi WHERE id = ?')->execute([$id]);
}

/* =========================================================
   LOG AKTIVITAS
   ========================================================= */
function add_log($aksi, $userNama = null) {
    $admin = current_admin();
    $nama = $userNama ?: ($admin['nama'] ?? 'Sistem');
    db()->prepare('INSERT INTO log_aktivitas (user_nama, aksi) VALUES (?,?)')->execute([$nama, $aksi]);
}

function get_logs($limit = 100) {
    $stmt = db()->prepare('SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT ' . (int)$limit);
    $stmt->execute();
    return $stmt->fetchAll();
}

/* =========================================================
   NOTIFIKASI
   ========================================================= */
function add_notifikasi($tipe, $judul, $deskripsi = '') {
    db()->prepare('INSERT INTO notifikasi (tipe, judul, deskripsi) VALUES (?,?,?)')->execute([$tipe, $judul, $deskripsi]);
}

function get_notifikasi($limit = 20) {
    $stmt = db()->prepare('SELECT * FROM notifikasi ORDER BY waktu DESC LIMIT ' . (int)$limit);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_unread_notifikasi() {
    return (int) db()->query('SELECT COUNT(*) FROM notifikasi WHERE dibaca = 0')->fetchColumn();
}

function mark_all_notifikasi_read() {
    db()->exec('UPDATE notifikasi SET dibaca = 1');
}

/* =========================================================
   UPLOAD LAMPIRAN
   ========================================================= */
/**
 * Tangani upload file dari $_FILES[$field]. Mengembalikan array
 * ['ok' => bool, 'filename' => string, 'error' => string].
 */
function handle_upload($field, $subdir = 'pengaduan') {
    if (empty($_FILES[$field]['name'])) {
        return ['ok' => true, 'filename' => '', 'error' => ''];
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'filename' => '', 'error' => 'Gagal mengunggah file.'];
    }
    $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return ['ok' => false, 'filename' => '', 'error' => 'Format file harus jpg, jpeg, png, atau pdf.'];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['ok' => false, 'filename' => '', 'error' => 'Ukuran file maksimal 5MB.'];
    }
    $dir = BASE_PATH . '/uploads/' . $subdir;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $safeName = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $safeName)) {
        return ['ok' => false, 'filename' => '', 'error' => 'Gagal menyimpan file di server.'];
    }
    return ['ok' => true, 'filename' => 'uploads/' . $subdir . '/' . $safeName, 'error' => ''];
}
