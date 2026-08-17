// ASPIRA - admin.js
// Helper Chart.js dan interaksi panel admin

function aspiraLineChart(canvasId, labels, data, label) {
  const el = document.getElementById(canvasId);
  if (!el || typeof Chart === 'undefined') return;
  const ctx = el.getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 240);
  gradient.addColorStop(0, 'rgba(47,111,237,0.28)');
  gradient.addColorStop(1, 'rgba(47,111,237,0)');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: label || 'Pengaduan',
        data,
        borderColor: '#2f6fed',
        backgroundColor: gradient,
        fill: true,
        tension: 0.35,
        pointRadius: 3,
        pointBackgroundColor: '#2f6fed',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, grid: { color: '#eef1f6' } },
        x: { grid: { display: false } }
      }
    }
  });
}

function aspiraDoughnutChart(canvasId, labels, data, colors) {
  const el = document.getElementById(canvasId);
  if (!el || typeof Chart === 'undefined') return;
  new Chart(el.getContext('2d'), {
    type: 'doughnut',
    data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 0 }] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: { legend: { display: false } }
    }
  });
}

function aspiraBarChart(canvasId, labels, data, color) {
  const el = document.getElementById(canvasId);
  if (!el || typeof Chart === 'undefined') return;
  new Chart(el.getContext('2d'), {
    type: 'bar',
    data: { labels, datasets: [{ data, backgroundColor: color || '#2f6fed', borderRadius: 6, maxBarThickness: 28 }] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, grid: { color: '#eef1f6' } }, x: { grid: { display: false } } }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // Live filter tabel di sisi klien (kategori & status)
  const filterKategori = document.getElementById('filterKategori');
  const filterStatus = document.getElementById('filterStatus');
  const searchInput = document.getElementById('tableSearch');
  const rows = () => document.querySelectorAll('#dataTable tbody tr');

  function applyFilter() {
    const kat = filterKategori ? filterKategori.value : '';
    const stat = filterStatus ? filterStatus.value : '';
    const q = searchInput ? searchInput.value.toLowerCase() : '';
    rows().forEach((row) => {
      const rowKat = row.dataset.kategori || '';
      const rowStat = row.dataset.status || '';
      const text = row.textContent.toLowerCase();
      const matchKat = !kat || kat === 'Semua Kategori' || rowKat === kat;
      const matchStat = !stat || stat === 'Semua Status' || rowStat === stat;
      const matchSearch = !q || text.includes(q);
      row.style.display = (matchKat && matchStat && matchSearch) ? '' : 'none';
    });
  }
  [filterKategori, filterStatus, searchInput].forEach((el) => {
    if (el) el.addEventListener('input', applyFilter);
  });

  // Highlight kartu radio verifikasi yang dipilih
  document.querySelectorAll('.radio-card input[type=radio]').forEach((radio) => {
    radio.addEventListener('change', () => {
      document.querySelectorAll('.radio-card').forEach((c) => c.style.borderColor = 'var(--gray-200)');
      radio.closest('.radio-card').style.borderColor = 'var(--blue-500)';
    });
  });

  // Nama file pada input file custom di panel admin
  document.querySelectorAll('.input-file input[type=file]').forEach((input) => {
    input.addEventListener('change', () => {
      const box = input.closest('.input-file');
      const label = box.querySelector('.file-name');
      if (label) label.textContent = input.files.length ? input.files[0].name : 'Belum ada file dipilih';
    });
  });

  // Konfirmasi hapus
  document.querySelectorAll('[data-confirm]').forEach((el) => {
    el.addEventListener('click', (e) => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });
});
