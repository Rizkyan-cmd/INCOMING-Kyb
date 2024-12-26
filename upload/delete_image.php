<?php
include_once('../config.php'); 

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query untuk mendapatkan path gambar
    $query = "SELECT evidence FROM incoming_check WHERE id = ?";
    $stmt2 = $connwarehouse->prepare($query); // Gunakan $conn sesuai dengan koneksi mysqli Anda
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $evidence = $row['evidence'];
        $imagePath = '../public/img/' . $evidence; // Pastikan path gambar benar

        // Log untuk debugging
        error_log("Path gambar yang ingin dihapus: " . $imagePath);

        // Periksa apakah gambar ada dan hapus
        if (file_exists($imagePath)) {
            if (unlink($imagePath)) {
                // Kosongkan field evidence di database
                $updateQuery = "UPDATE incoming_check SET evidence = '' WHERE id = ?";
                $updateStmt = $connwarehouse->prepare($updateQuery);
                $updateStmt->bind_param("i", $id);
                $updateStmt->execute();

                echo 'success'; // Respon sukses jika penghapusan berhasil
            } else {
                error_log('Gagal menghapus gambar: ' . $imagePath);
                echo 'error'; // Jika gagal menghapus gambar
            }
        } else {
            error_log('File gambar tidak ditemukan: ' . $imagePath);
            echo 'file_not_found'; // Jika file gambar tidak ditemukan
        }
    } else {
        echo 'error'; // Jika data tidak ditemukan di database
    }
}
?>
