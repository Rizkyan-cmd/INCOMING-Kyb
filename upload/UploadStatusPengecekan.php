<?php
include '/../config.php';  // Pastikan path konfigurasi sudah benar

header('Content-Type: application/json');

// Inisialisasi array dengan nilai Belum Cek dan Sudah Cek = 0
$response = [
    'labels' => ['Belum Cek ', 'Sudah Cek '], // Label untuk pie chart
    'values' => [0, 0]         // Nilai default untuk Belum Cek dan Sudah Cek
];

// Ambil tanggal yang dipilih dari query parameter (jika ada)
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); // Default ke tanggal hari ini jika tidak ada parameter

// Pastikan tanggal yang dipilih valid
if (!strtotime($selectedDate)) {
    echo json_encode(["error" => "Tanggal tidak valid"]);
    exit;
}

// Query untuk mengambil data berdasarkan no_part, sampling, dan cek_status
$query = "
    SELECT 
        no_part,
        smpling,
        ng,
        CASE 
            WHEN (smpling IS NULL OR ng IS NULL) THEN 'Belum Cek'  -- Jika sampling atau ng NULL
            ELSE 'Sudah Cek'  -- Jika ada nilai
        END AS status_cek
    FROM incoming_check 
    GROUP BY no_part, smpling, ng
";

$result = $connwarehouse->query($query);  // Gunakan query MySQLi

// Periksa jika ada data yang ditemukan
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Memeriksa status pengecekan berdasarkan NULL atau nilai
        if ($row['status_cek'] == 'Belum Cek') {
            $response['values'][0]++; // Belum Cek
        } else {
            $response['values'][1]++; // Sudah Cek
        }
    }
} else {
    // Jika query gagal atau tidak ada data
    $response['error'] = "Tidak ada data untuk tanggal yang dipilih";
}

// Kirimkan data dalam format JSON
echo json_encode($response);
?>
