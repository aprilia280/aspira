<?php
/**
 * Logo ASPIRA - memakai file gambar logo sendiri (assets/images/logo.png),
 * dipakai berulang di header publik, sidebar admin, dan halaman login/register.
 */
function render_logo($size = 38) {
    echo '<img src="' . base_url('assets/images/logo.png') . '" width="' . $size . '" height="' . $size . '" alt="Logo ASPIRA" class="brand-logo" style="object-fit: contain; flex-shrink: 0;">';
}
