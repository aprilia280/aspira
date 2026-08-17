// ASPIRA - main.js
// Interaksi ringan untuk halaman publik

document.addEventListener('DOMContentLoaded', () => {

  // Tampilkan nama file yang dipilih pada input file custom
  document.querySelectorAll('.input-file input[type=file]').forEach((input) => {
    input.addEventListener('change', () => {
      const box = input.closest('.input-file');
      const label = box.querySelector('.file-name');
      if (label) {
        label.textContent = input.files.length ? input.files[0].name : 'Belum ada file dipilih';
      }
    });
  });

  // Toggle tampilkan/sembunyikan password
  document.querySelectorAll('.toggle-eye').forEach((btn) => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling;
      if (!input) return;
      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';
      btn.innerHTML = isPassword
        ? '<i class="fa-solid fa-eye-slash"></i>'
        : '<i class="fa-solid fa-eye"></i>';
    });
  });

  // Validasi sederhana form konfirmasi password
  const regForm = document.getElementById('registerForm');
  if (regForm) {
    regForm.addEventListener('submit', (e) => {
      const pass = document.getElementById('password');
      const confirm = document.getElementById('konfirmasi_password');
      if (pass && confirm && pass.value !== confirm.value) {
        e.preventDefault();
        alert('Konfirmasi password tidak sama dengan password.');
      }
    });
  }

});
