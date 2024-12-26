<?php
include '/../config.php';

header('Content-Type: application/json');

$response = [
    'ng' => 0,
    'ok' => 0
];

// Check if date parameter exists and is not today's date
$selectedDate = isset($_GET['date']) ? $_GET['date'] : null;
$today = date('Y-m-d');

// Validate date if provided
if ($selectedDate && !strtotime($selectedDate)) {
    echo json_encode(["error" => "Tanggal tidak valid"]);
    exit;
}

// Build query based on whether a specific date was selected
if ($selectedDate && $selectedDate != $today) {
    // Query for specific date
    $query = "
        SELECT 
            DATE(check_dt) AS day, 
            SUM(ng) AS total_ng, 
            SUM(ok) AS total_ok 
        FROM incoming_check 
        WHERE DAYOFWEEK(check_dt) BETWEEN 2 AND 6
          AND DATE(check_dt) = '$selectedDate'
        GROUP BY DATE(check_dt)
        ORDER BY DATE(check_dt) DESC
    ";
} else {
    // Query for current month's total
    $currentMonth = date('Y-m');
    $query = "
        SELECT 
            DATE(check_dt) AS day, 
            SUM(ng) AS total_ng, 
            SUM(ok) AS total_ok 
        FROM incoming_check 
        WHERE DAYOFWEEK(check_dt) BETWEEN 2 AND 6
          AND DATE_FORMAT(check_dt, '%Y-%m') = '$currentMonth'
        GROUP BY DATE(check_dt)
        ORDER BY DATE(check_dt) DESC
    ";
}

$result = $connwarehouse->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ng = (int)$row['total_ng'];
        $ok = (int)$row['total_ok'];

        if ($ng > 0) {
            $response['ng'] += $ng;
        }
        if ($ok > 0) {
            $response['ok'] += $ok;
        }
    }
    
    // Add period information to response
    if ($selectedDate && $selectedDate != $today) {
        $response['period'] = "Data untuk tanggal " . date('d-m-Y', strtotime($selectedDate));
    } else {
        $response['period'] = "Total data bulan " . date('F Y');
    }
} else {
    $response['error'] = "Tidak ada data untuk periode yang dipilih";
}

echo json_encode($response);
?>