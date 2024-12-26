<?php
// Include your database connection
include '../config.php';

if (isset($_POST['filterDate'])) {
    $filterDate = $_POST['filterDate'];
    // Convert the date format to MySQL format (YYYY-MM-DD)
    $formattedDate = date('Y-m-d', strtotime($filterDate));  // Pastikan formatnya sesuai dengan MySQL
    
    // Fetch data from the database with the selected date
    $sql = "SELECT * FROM incoming_check WHERE check_dt = '$formattedDate'";
    $result = $connwarehouse->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['rcno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
            echo "<td>" . htmlspecialchars($row['item']) . "</td>";
            echo "<td>" . htmlspecialchars($row['no_part']) . "</td>";
            echo "<td>" . htmlspecialchars($row['qstok']) . "</td>";
            echo "<td>" . htmlspecialchars($row['smpling']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ng']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ok']) . "</td>";
            echo "<td>" . (($row['ng_desc']) ? htmlspecialchars($row['ng_desc'] . ' - ' . $row['deskripsi']) : '') . "</td>";
            echo "<td>" . htmlspecialchars($row['ng_detail']) . "</td>";

            if (!empty($row['evidence'])) {
                $imagePath = htmlspecialchars($row['evidence']);
                echo "<td><img src='public/img/" . $imagePath . "' alt='Evidence' style='width: 100px; height: auto;'></td>";
            } else {
                echo "<td></td>";
            }
            echo "<td>" . htmlspecialchars(date("d-m-Y", strtotime($row['check_dt']))) . "</td>";
            echo "<td>" . htmlspecialchars($row['check_usr']) . ' | ' . getNameFromUsers($connlembur, $row['check_usr']) . "</td>";
            echo "<td><span class='btn " . ($row['ng'] == 0 ? "btn-success" : "btn-danger") . " disabled'>" . ($row['ng'] == 0 ? 'OK' : 'NG') . "</span></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='11' class='text-center'>Tidak ada data ditemukan</td></tr>";
    }
}
$connwarehouse->close();
?>
