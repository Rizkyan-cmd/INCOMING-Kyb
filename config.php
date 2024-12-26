<?php
// config.php

// Database connection settings
$servername = "localhost";  // Usually localhost in XAMPP
$username = "root";         // Default username in XAMPP
$password = "";             // Default password is empty

// Database names
$dbname_lembur = "lembur";
$dbname_warehouse = "warehouse";

// Create connections
$connlembur = new mysqli($servername, $username, $password, $dbname_lembur);
$connwarehouse = new mysqli($servername, $username, $password, $dbname_warehouse);

// Check connections
if ($connlembur->connect_error) {
    die("Connection to lembur database failed: " . $connlembur->connect_error);
}

if ($connwarehouse->connect_error) {
    die("Connection to warehouse database failed: " . $connwarehouse->connect_error);
}

function getNameFromUsers($connlembur, $npk)
{
    $sql = "SELECT full_name FROM ct_users_hash WHERE npk = ?";
    $stmt = $connlembur->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $npk);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['full_name']; // Kembalikan nama pengguna
        } else {
            return "Nama tidak ditemukan untuk NPK: " . htmlspecialchars($npk);
        }
    } else {
        return "Terjadi kesalahan pada query: " . htmlspecialchars($connlembur->error);
    }
}

?>
