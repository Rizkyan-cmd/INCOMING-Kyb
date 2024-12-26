<?php
include '../config.php'; // Pastikan koneksi database Anda diatur di sini

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query untuk mengambil daftar supplier
    $query = "SELECT DISTINCT nama FROM lajur_pengecekan";
 // Gantilah dengan nama tabel dan kolom yang sesuai
    $stmt = $connwarehouse->prepare($query);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $suppliers = [];

        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $suppliers]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    $connwarehouse->close();
}
?>
