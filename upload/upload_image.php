<?php
// Konfigurasi database
$servername = "localhost"; // Ubah dengan server database Anda
$username = "root"; // Ubah dengan username database Anda
$password = ""; // Ubah dengan password database Anda
$dbname_warehouse = "warehouse"; // Nama database Anda

// Koneksi ke database
$connwarehouse = new mysqli($servername, $username, $password, $dbname_warehouse);

// Cek koneksi
if ($connwarehouse->connect_error) {
    die("Koneksi gagal: " . $connwarehouse->connect_error);
}

// Cek jika file ada

header("Content-Type: application/json");
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $id = $_POST['id']; // Ambil ID dari permintaan

    $targetDir = "../public/img/"; // Folder tempat menyimpan gambar
    $fileName = basename($_FILES['file']['name']); // Nama file asli
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION); // Mendapatkan ekstensi file

    // Membuat nama file unik menggunakan timestamp dan ekstensi
    $uniqueFileName = time() . '_' . uniqid() . '.' . $fileType; 

    $targetFilePath = $targetDir . $uniqueFileName; // Path lengkap untuk menyimpan file

    // Validasi format file (hanya gambar)
    $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'];

    if (in_array(strtolower($fileType), $allowedTypes)) {
        // Upload file ke server
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
            // Simpan nama file ke database (misalnya ke kolom 'evidence' di tabel Anda)
            $sql = "UPDATE incoming_check SET evidence='$uniqueFileName' WHERE id='$id'";
            if ($connwarehouse->query($sql) === TRUE) {
                echo json_encode($uniqueFileName); // Mengirimkan nama file kembali sebagai respons
            } else {
                echo json_encode("Error: " . $connwarehouse->error);
            }
        } else {
            echo json_encode("Error mengupload file.");
        }
    } else {
        echo json_encode("Format file tidak diperbolehkan.");
    }
} else {
    echo json_encode("Tidak ada file yang diupload.");
}

// Tutup koneksi
$connwarehouse->close();
?>
