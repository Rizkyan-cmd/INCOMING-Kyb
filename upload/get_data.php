<?php
session_start(); // Mulai session

// Koneksi ke database
$servername = "localhost"; // Ganti dengan server Anda
$username = "root"; // Ganti dengan username Anda
$password = ""; // Ganti dengan password Anda
$dbname_warehouse = "warehouse"; // Nama database

// Membuat koneksi
$connwarehouse = new mysqli($servername, $username, $password, $dbname_warehouse);

// Cek koneksi
if ($connwarehouse->connect_error) {
    die("Koneksi gagal: " . $connwarehouse->connect_error);
}

// Cek apakah formulir disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $rcno = $_POST['rcno'];
    $orno = $_POST['orno'];
    $item = $_POST['item'];
    $qstok = $_POST['qstok'];
    $smpling = $_POST['smpling'];
    $dsca = $_POST['dsca'];
    $nama = $_POST['nama'];

    // Query untuk memasukkan data ke dalam tabel incoming_check
    $sql = "INSERT INTO incoming_check (rcno, orno, item, qstok, smpling, dsca, nama) 
            VALUES ('$rcno', '$orno', '$item', $qstok, '$smpling', '$dsca', '$nama')";

    if ($connwarehouse->query($sql) === TRUE) {
        $_SESSION['message'] = "Data berhasil disimpan!"; // Set session message
        $_SESSION['msg_type'] = "success"; // Set session message type
        header("Location: Viewjudge.php"); // Redirect ke Viewjudge.php
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $connwarehouse->error; // Set error message
        $_SESSION['msg_type'] = "error"; // Set session message type
        header("Location: Viewjudge.php"); // Redirect ke Viewjudge.php
        exit();
    }
}

// Tutup koneksi
$connwarehouse->close();
?>
