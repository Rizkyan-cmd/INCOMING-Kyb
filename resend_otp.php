<?php
// Memulai session
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');
include 'config.php';

// Cek apakah NPK ada pada permintaan POST
if (isset($_POST['npk']) && !empty($_POST['npk'])) {
    $npk = $_POST['npk'];

    // Logika pengiriman OTP, misalnya memanggil fungsi untuk mengirim OTP
    $otp = rand(100000, 999999);  // Hasilkan OTP acak

    // Simpan OTP dalam sesi atau basis data (contoh menggunakan session)
    $_SESSION['otp'] = $otp;

    $expiry_date = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Menentukan masa berlaku OTP

    // Query untuk memperbarui data berdasarkan NPK
    $updateQuery = "UPDATE otp_authentic SET otp = ?, expiry_date = ? WHERE npk = ?";

    // Persiapkan dan bind parameter untuk query
    if ($stmt = $connwarehouse->prepare($updateQuery)) {
        $stmt->bind_param("iss", $otp, $expiry_date, $npk);  // 'i' untuk integer (OTP) dan 's' untuk string (NPK)

        // Eksekusi query
        if ($stmt->execute()) {
            // Respon JSON untuk keberhasilan pengiriman OTP
            echo json_encode([
                'success' => true,
                'message' => 'OTP berhasil dikirim dan data diperbarui.',
                'otp' => $otp // hanya untuk debugging, jangan tampilkan OTP di production
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memperbarui data.'
            ]);
        }

        // Tutup statement
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menyiapkan query.'
        ]);
    }

    // Tutup koneksi
    $connwarehouse->close();
} else {
    // Respon JSON jika NPK tidak ditemukan atau kosong
    echo json_encode([
        'success' => false,
        'message' => 'NPK tidak ditemukan atau tidak valid.'
    ]);
}