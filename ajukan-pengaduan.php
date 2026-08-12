<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Ajukan Pengaduan';
$kategoriList = get_kategori();
usort($kategoriList, fn($a, $b) => ($a['nama'] === 'Lainnya') <=> ($b['nama'] === 'Lainnya'));
$settings = get_settings();
$instansiList = get_instansi_list();
$loggedUser = (is_logged_in() && !is_staff()) ? current_user() : null;
$error = '';
$success = '';
$newTicket = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $instansi = trim($_POST['instansi'] ?? '');
    $kategoriDetail = trim($_POST['kategori_detail'] ?? '');

    if (!$nama || !$email || !$kategori || !$judul || !$isi) {
        $error = 'Mohon lengkapi semua kolom bertanda wajib diisi.';
    } elseif ($kategori === 'Lainnya' && !$kategoriDetail) {
        $error = 'Mohon sebutkan kategori lainnya.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $upload = handle_upload('lampiran', 'pengaduan');
        if (!$upload['ok']) {
            $error = $upload['error'];
        } else {
            $ticket = insert_pengaduan([
                'judul' => $judul,
                'isi' => $isi,
                'kategori' => $kategori,
                'kategori_detail' => $kategori === 'Lainnya' ? $kategoriDetail : '',
                'lokasi' => $lokasi,
                'instansi' => $instansi,
                'nama' => $nama,
                'email' => $email,
                'telepon' => $telepon,
                'lampiran' => $upload['filename'],
            ]);
            $success = 'Pengaduan Anda berhasil dikirim.';
            $newTicket = $ticket;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:900px;">
    <h2 style="margin-bottom:2px;">Form Pengaduan</h2>
    <p style="color:var(--gray-500); font-size:13.5px;">Beranda / Ajukan Pengaduan</p>
  </div>

  <div class="container" style="max-width:900px;">
    <div class="detail-grid">
      <div class="card">
        <?php if ($success): ?>
          <div class="form-success">
            <strong>Berhasil!</strong> <?= h($success) ?> Nomor tiket Anda: <strong><?= h($newTicket) ?></strong>.
            Simpan nomor ini untuk mengecek status pengaduan Anda.
          </div>
          <a href="cek-pengaduan.php?tiket=<?= urlencode($newTicket) ?>" class="btn btn-primary">Lihat Status Pengaduan</a>
        <?php else: ?>
          <?php if ($error): ?><div class="form-error"><?= h($error) ?></div><?php endif; ?>
          <form method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label>Nama Lengkap</label>
              <input type="text" name="nama" placeholder="Masukkan nama lengkap" value="<?= h($_POST['nama'] ?? $loggedUser['nama'] ?? '') ?>" required>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan email" value="<?= h($_POST['email'] ?? $loggedUser['email'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label>No. Telepon</label>
                <input type="tel" name="telepon" placeholder="Masukkan nomor telepon" value="<?= h($_POST['telepon'] ?? $loggedUser['telepon'] ?? '') ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Kategori Pengaduan</label>
                <?php $sel = $_GET['kategori'] ?? ($_POST['kategori'] ?? ''); ?>
                <div class="custom-select" id="kategoriCustom">
                  <input type="hidden" name="kategori" id="kategoriValue" value="<?= h($sel) ?>">
                  <button type="button" class="custom-select-trigger" id="kategoriTrigger">
                    <span id="kategoriLabel"><?= $sel ? h($sel) : 'Pilih Kategori' ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                  </button>
                  <div class="custom-select-panel" id="kategoriPanel">
                    <div class="custom-select-option" data-value="">Pilih Kategori</div>
                    <?php foreach ($kategoriList as $k): ?>
                    <div class="custom-select-option" data-value="<?= h($k['nama']) ?>"><?= h($k['nama']) ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Judul Pengaduan</label>
                <input type="text" name="judul" placeholder="Masukkan judul pengaduan" value="<?= h($_POST['judul'] ?? '') ?>" required>
              </div>
            </div>
            <div class="form-group" id="kategoriLainnyaWrap" style="display:none;">
              <label>Sebutkan Kategori Lainnya</label>
              <input type="text" name="kategori_detail" id="kategoriLainnyaInput" placeholder="Contoh: Keamanan Lingkungan" value="<?= h($_POST['kategori_detail'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Instansi Terkait</label>
              <?php $selInstansi = $_POST['instansi'] ?? ''; ?>
              <div class="custom-select" id="instansiCustom">
                <input type="hidden" name="instansi" id="instansiValue" value="<?= h($selInstansi) ?>">
                <button type="button" class="custom-select-trigger" id="instansiTrigger">
                  <span id="instansiLabel"><?= $selInstansi ? h($selInstansi) : 'Pilih Instansi' ?></span>
                  <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="custom-select-panel" id="instansiPanel">
                  <div class="custom-select-option" data-value="">Pilih Instansi</div>
                  <?php foreach ($instansiList as $ins): ?>
                  <div class="custom-select-option" data-value="<?= h($ins) ?>"><?= h($ins) ?></div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Lokasi Kejadian</label>
              <input type="text" name="lokasi" placeholder="Contoh: Jl. Merdeka No. 10" value="<?= h($_POST['lokasi'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Isi Pengaduan</label>
              <textarea name="isi" placeholder="Ceritakan keluhan Anda secara detail..." required><?= h($_POST['isi'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
              <label>Lampiran (Opsional)</label>
              <div class="input-file">
                <label class="btn btn-outline btn-sm" style="cursor:pointer;">
                  <i class="fa-solid fa-paperclip"></i> Pilih File
                  <input type="file" name="lampiran">
                </label>
                <div class="file-name">Belum ada file dipilih</div>
                <div class="form-hint">Format: jpg, jpeg, png, pdf (Maks. 5MB)</div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Kirim Pengaduan</button>
          </form>
        <?php endif; ?>
      </div>

      <div class="card">
        <h3 style="font-size:15px;"><i class="fa-solid fa-lightbulb" style="color:#c9820b"></i> Tips Pengaduan</h3>
        <ul style="margin-top:10px;">
          <li style="padding:8px 0; font-size:13.5px; color:var(--gray-700); border-bottom:1px solid var(--gray-100);">Sampaikan pengaduan dengan jelas dan lengkap.</li>
          <li style="padding:8px 0; font-size:13.5px; color:var(--gray-700); border-bottom:1px solid var(--gray-100);">Pengaduan akan diverifikasi sebelum diproses.</li>
          <li style="padding:8px 0; font-size:13.5px; color:var(--gray-700);">Anda akan menerima notifikasi melalui email/WhatsApp.</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<script>
function toggleKategoriLainnya() {
  var value = document.getElementById('kategoriValue').value;
  var wrap = document.getElementById('kategoriLainnyaWrap');
  var input = document.getElementById('kategoriLainnyaInput');
  var isLainnya = value === 'Lainnya';
  wrap.style.display = isLainnya ? 'block' : 'none';
  input.required = isLainnya;
  if (!isLainnya) input.value = '';
}
document.addEventListener('DOMContentLoaded', toggleKategoriLainnya);

// Custom dropdown (selalu buka ke bawah, tidak seperti <select> bawaan browser)
function setupCustomSelect(wrapId, triggerId, panelId, hiddenId, labelId, placeholder, onChange) {
  var wrap = document.getElementById(wrapId);
  var trigger = document.getElementById(triggerId);
  var panel = document.getElementById(panelId);
  var hidden = document.getElementById(hiddenId);
  var label = document.getElementById(labelId);

  trigger.addEventListener('click', function (e) {
    e.stopPropagation();
    wrap.classList.toggle('open');
  });

  panel.querySelectorAll('.custom-select-option').forEach(function (opt) {
    if (opt.dataset.value === hidden.value) opt.classList.add('active');
    opt.addEventListener('click', function () {
      hidden.value = opt.dataset.value;
      label.textContent = opt.dataset.value ? opt.textContent : placeholder;
      panel.querySelectorAll('.custom-select-option').forEach(function (o) { o.classList.remove('active'); });
      opt.classList.add('active');
      wrap.classList.remove('open');
      if (onChange) onChange();
    });
  });

  document.addEventListener('click', function (e) {
    if (!wrap.contains(e.target)) wrap.classList.remove('open');
  });
}
setupCustomSelect('kategoriCustom', 'kategoriTrigger', 'kategoriPanel', 'kategoriValue', 'kategoriLabel', 'Pilih Kategori', toggleKategoriLainnya);
setupCustomSelect('instansiCustom', 'instansiTrigger', 'instansiPanel', 'instansiValue', 'instansiLabel', 'Pilih Instansi');

// Validasi manual karena hidden input tidak divalidasi browser secara otomatis
document.querySelector('form').addEventListener('submit', function (e) {
  if (!document.getElementById('kategoriValue').value) {
    e.preventDefault();
    alert('Mohon pilih Kategori Pengaduan.');
    document.getElementById('kategoriTrigger').scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
