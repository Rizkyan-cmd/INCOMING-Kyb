<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'config.php';

$response = array('status' => 'error', 'message' => 'Kesalahan dalam proses verifikasi OTP.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $npk = $_POST['npk'];

    // Ambil OTP dan masa berlakunya dari database
    $stmt = $connwarehouse->prepare("SELECT * FROM otp_authentic WHERE npk = ? AND otp = ? AND `expiry_date` > NOW()");
    $stmt->bind_param("ss", $npk, $otp);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // OTP valid, ambil data user
            $stmtUser = $connlembur->prepare("SELECT * FROM ct_users_hash WHERE npk = ?");
            $stmtUser->bind_param("s", $npk);

            if ($stmtUser->execute()) {
                $resultUser = $stmtUser->get_result();
                if ($resultUser->num_rows > 0) {
                    $dataUser = $resultUser->fetch_assoc();

                    // Set session untuk user yang berhasil login
                    $_SESSION['loggedin'] = true;
                    $_SESSION['npk'] = $dataUser['npk'];
                    $_SESSION['full_name'] = $dataUser['full_name'];
                    $_SESSION['golongan'] = $dataUser['golongan'];

                    // Update kolom `use` dari 2 menjadi 1 dan set `use_date`
                    $use_date = date('Y-m-d H:i:s'); // Tanggal dan waktu saat ini
                    $updateStmt = $connwarehouse->prepare("UPDATE otp_authentic SET `use` = 1, `use_date` = ? WHERE npk = ? AND otp = ?");
                    $updateStmt->bind_param("sss", $use_date, $npk, $otp);

                    if ($updateStmt->execute()) {
                        $response['status'] = 'success';
                        $response['message'] = 'OTP valid, status `use` dan `use_date` telah diperbarui.';
                    } else {
                        $response['message'] = 'Gagal memperbarui status `use`: ' . $updateStmt->error;
                    }

                    $updateStmt->close();
                } else {
                    $response['message'] = 'NPK tidak ditemukan.';
                }
            }
            $stmtUser->close();
        } else {
            $response['message'] = 'OTP tidak valid atau telah kadaluarsa.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Terjadi kesalahan pada eksekusi statement: ' . $stmt->error;
    }

    $stmt->close();
}

// Kembalikan respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
