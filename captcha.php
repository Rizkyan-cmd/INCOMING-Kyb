<?php
session_start();
header('Content-Type: image/png');

// Buat gambar baru
$image = imagecreatetruecolor(200, 80); // Ukuran gambar lebih besar untuk memberi ruang pada teks

// Warna: latar belakang acak, teks biru, dan titik merah
$background_color = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255)); // Latar belakang acak
$text_color = imagecolorallocate($image, 0, 0, 0); // Biru
$dotcolor = imagecolorallocate($image, 255, 0, 0); // Merah

// Isi gambar dengan warna latar belakang acak
imagefilledrectangle($image, 0, 0, 200, 80, $background_color);

// Tambahkan elemen visual di latar belakang (titik acak)
for ($i = 0; $i < 150; $i++) {
    imagesetpixel($image, rand(0, 200), rand(0, 80), $dotcolor);
}

// Buat kode random untuk CAPTCHA
$captcha_code = substr(str_shuffle("0123456789adbcdefghizklmnopqrstupwxyz"), 0, 6);

// Simpan kode CAPTCHA di sesi
$_SESSION['captcha'] = $captcha_code;

// Tentukan ukuran font dan posisi teks
$fontsize = 30; // Ukuran font lebih besar
$x = rand(10, 50); // Koordinat horizontal acak
$y = rand(30, 50); // Koordinat vertikal acak

// Gunakan font TrueType untuk menulis teks di gambar
$font_path = 'public/fonts/Poppins/Poppins-Bold.ttf'; // Tentukan path ke font TrueType Anda
$fontsize = 25;
imagettftext($image, $fontsize, 0, $x, $y, $text_color, $font_path, $captcha_code);

// Output gambar
imagepng($image);
imagedestroy($image);
?>
