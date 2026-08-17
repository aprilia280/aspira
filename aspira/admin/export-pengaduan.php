<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

$filters = [
    'status' => trim($_GET['status'] ?? ''),
    'kategori' => trim($_GET['kategori'] ?? ''),
    'instansi' => trim($_GET['instansi'] ?? ''),
    'search' => trim($_GET['q'] ?? ''),
    'dari' => trim($_GET['dari'] ?? ''),
    'sampai' => trim($_GET['sampai'] ?? ''),
];
$data = get_pengaduan($filters);

add_log('Mengekspor laporan pengaduan ke Excel/CSV');

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="pengaduan-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fputs($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
fputcsv($out, ['No. Tiket', 'Judul', 'Kategori', 'Instansi', 'Pelapor', 'Email', 'Telepon', 'Lokasi', 'Tanggal', 'Status', 'Petugas']);
foreach ($data as $p) {
    fputcsv($out, [
        $p['no_tiket'], $p['judul'], $p['kategori'], $p['instansi'], $p['nama'], $p['email'], $p['telepon'],
        $p['lokasi'], date('d M Y H:i', strtotime($p['tanggal'])), $p['status'], $p['petugas'],
    ]);
}
fclose($out);
exit;
