<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname_warehouse = "warehouse";
session_start();
if (!isset($_SESSION['npk'])) {
    header('Location: index.php'); // Redirect ke login
    exit(); // Hentikan eksekusi
}

$connwarehouse = new mysqli($servername, $username, $password, $dbname_warehouse);

// Cek koneksi
if ($connwarehouse->connect_error) {
    die("Koneksi gagal: " . $connwarehouse->connect_error);
}
$rcno = $_GET['rcno'];
// Memproses data yang diinput
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $smpling = $_POST['smpling'];
    $ok = $_POST['ok'];
    $ng = $_POST['ng'];
    $ng_kategori = $_POST['ng_kategori'];
    $ng_detail = $_POST['ng_detail'];
    // $deskripsi = $_POST['deskripsi'];
    $check_dt = $_POST['check_dt'];
    $evidence = $_POST['evidence'];
    $check_usr = $_SESSION['npk'];


    // Membuat statement prepared untuk update data
    $stmt2 = $connwarehouse->prepare("UPDATE incoming_check SET smpling = ?, ng = ?, ok = ?, ng_kategori = ?, ng_detail = ?, check_dt = ?, check_usr = ? WHERE id = ?");

    if (!$stmt2) {
        die("Gagal mempersiapkan statement: " . $connwarehouse->error);
    }

    // Simpan hanya data pada baris tertentu (berdasarkan ID)
    $stmt2->bind_param("sssssssi", $smpling, $ng, $ok, $ng_kategori, $ng_detail, $check_dt, $check_usr, $id);

    if (!$stmt2->execute()) {
        die("Gagal memperbarui data: " . $stmt2->error);
    }

    $stmt2->close();

    // Redirect untuk mencegah pengiriman ulang data
    header("Location: " . $_SERVER['PHP_SELF'] . "?rcno=$rcno");
    exit();
}
$ngDescData = [];
$queryNgDesc = "SELECT id, ng_desc, deskripsi FROM incoming_ng"; // Pastikan nama tabel dan kolom sesuai dengan struktur database Anda
$resultNgDesc = $connwarehouse->query($queryNgDesc);

if ($resultNgDesc->num_rows > 0) {
    while ($rowNgDesc = $resultNgDesc->fetch_assoc()) {
        $ngDescData[] = $rowNgDesc; // Simpan data dalam array
    }
}

// Query untuk mengambil data dari tabel incoming_check
$sql = "SELECT * FROM incoming_ng";
// $sql = "SELECT * FROM incoming_check WHERE rcno = '$rcno' AND ok = 0 AND ng = 0 ";
$sql = "SELECT * FROM incoming_check WHERE (rcno = '$rcno' OR rcno IS NULL) AND (ok = 0 OR ok IS NULL) AND (ng = 0 OR ng IS NULL)";

$result = $connwarehouse->query($sql);

$currUrl = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Mulai Inspeksi</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="public/img/kyb-icon.ico" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet"> -->
    <!-- <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet"> -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="public/css/form.css" rel="stylesheet">
    <link href="public/library/DataTables/datatables.min.css" rel="stylesheet">
    <script src="public/library/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="public/library/jquery/dist/jquery.min.js"></script>
</head>

<body>
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo Section (on the left) -->
            <div class="d-flex align-items-center">
                <img src="public/img/kayaba-logo.png" alt="Logo" class="me-3" style="width: 125px; height: auto;">
            </div>
            <!-- Navigation Section (centered) -->
            <div class="d-flex justify-content-center flex-grow-1">
                <nav class="d-flex align-items-center">
                    <a href="dashboard.php"
                        class="nav-link mx-2 <?php echo strpos($currUrl, "dashboard") !== false ? "text-danger" : ""; ?>"
                        id="dashboard-link">
                        <span>Beranda</span>
                    </a>
                    <div class="nav-link dropdown mx-2">
                        <a href="#"
                            class="nav-link <?php echo strpos($currUrl, "ViewInpeksi") !== false ? "text-danger" : ""; ?>"
                            data-bs-toggle="dropdown" role="button">
                            <span>Tables Receipt</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="Viewjudge.php" class="dropdown-item">Data Receipt</a></li>
                            <li><a href="HistoryInspeksi.php" class="dropdown-item">History Inspeksi</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="row">
                <div class="d-flex justify-content-end w-100">
                    <div class="dropdown">
                        <span class="dropdown-toggle me-2" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false" style="cursor: pointer;">
                            Hallo, <strong><?php
                            // Ambil nama depan saja
                            $firstName = explode(' ', $_SESSION['full_name'])[0];
                            echo htmlspecialchars($firstName);
                            ?>!</strong>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" onclick="confirmLogout()">
                                    <i class="bi bi-box-arrow-right me-2"></i> <!-- Ikon Logout -->
                                    Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="main">
        <div class="card mt-3">
            <div class="card-body">
                <center>
                    <h2>Data Inspeksi</h2>
                </center>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Beranda</a></li>
                        <li class="breadcrumb-item active"><a href="Viewjudge.php">Receipt</a></li>

                    </ol>
                </nav>
                <form method="POST" action="" enctype="multipart/form-data" id="receiptForm">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="receiptTable">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th data-orderable="false">Produk</th>
                                    <th data-orderable="false">No Part</th>
                                    <th data-orderable="false">Quantity</th>
                                    <th data-orderable="false">Sampling</th>
                                    <th data-orderable="false">NG</th>
                                    <th data-orderable="false">OK</th>
                                    <th data-orderable="false">NG Kategori</th>
                                    <th data-orderable="false">Detail NG</th>
                                    <th data-orderable="false">Tanggal Inspeksi</th>
                                    <th data-orderable="false">Upload </th>
                                    <th data-orderable="false">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $evidence = $row['evidence'];
                                        $buttonClass = empty($evidence) ? 'btn-secondary' : 'btn-succes'; // Ubah warna tombol jika ada gambar
                                        $cameraTitle = empty($evidence) ? 'Empty' : 'Gambar Ada'; // Tooltip untuk status gambar
                                        echo "<tr>
                                        <td>{$row['item']}</td>
                                        <td>{$row['no_part']}</td>
                                        <td>{$row['qstok']}<input type='hidden' class='form-control qstok' id='qstok_{$row['id']}' name='qstok[]' value='" . htmlspecialchars($row['qstok']) . "'>
                                            <input type='hidden' class='form-control' name='id[]' value='" . htmlspecialchars($row['id']) . "'>
                                        </td>
                                        <td><input type='text' class='form-control smpling' name='smpling[]' id='smpling_{$row['id']}' value='" . htmlspecialchars($row['smpling']) . "'></td>
                                        <td><input type='text' class='form-control ng' name='ng[]' id='ng_{$row['id']}' value='" . htmlspecialchars($row['ng']) . "'></td>
                                        <td><input disabled type='text' class='form-control ok' name='ok[]' id='ok_{$row['id']}' value = '" . htmlspecialchars($row['ok']) . "' readonly></td>
                                        <td>
                                            <select name='ng_kategori[]' class='form-control ng_kategori' id='ng_kategori_{$row['id']}'>
                                                <option value='' selected disabled>-Pilih-</option>";
                                        foreach ($ngDescData as $ngDesc) {
                                            $selected = ($row['ng_kategori'] == $ngDesc['id']) ? 'selected' : '';
                                            echo "<option value='{$ngDesc['id']}' $selected>{$ngDesc['ng_desc']}</option>";
                                        }
                                        echo "</select>
                                        </td>
                                        <td><input type='text' class='form-control ng_detail' name='ng_detail[]' id='ng_detail_{$row['id']}' value='" . htmlspecialchars($row['ng_detail']) . "'></td>
                                        <td>
                                            <input type='hidden' name='check_dt[]' value='" . (empty($row['check_dt']) ? date("d-m-Y") : htmlspecialchars(date("d-m-Y", strtotime($row['check_dt'])))) . "'>
                                            <span>" . (empty($row['check_dt']) ? date("d-m-Y") : htmlspecialchars(date("d-m-Y", strtotime($row['check_dt'])))) . "</span>
                                        </td>
                                        <td>
                                            <input type='hidden' name='evidence[]' value='" . htmlspecialchars($evidence) . "' id='evidence_{$row['id']}'>
                                            <button type='button' class='btn {$buttonClass} openCameraButton' data-bs-toggle='modal' data-bs-target='#cameraModal' data-id='{$row['id']}' id='cameraButton_{$row['id']}'>
                                                <i class='bi bi-camera' title='{$cameraTitle}'></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button type='submit' class='btn btn-primary btn-sm' name='save' data-id='{$row['id']}'>Simpan</button>
                                        </td>
                                    </tr>";
                                    }
                                }
                                $connwarehouse->close();
                                ?>
                            </tbody>

                        </table>
                    </div>
                </form>
            </div>
            <!-- Modal Kamera -->
            <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cameraModalLabel">Sertakan gambar NG produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <button class="btn btn-primary" id="openCameraBtn">Ambil Gambar</button>
                                <!-- <button class="btn btn-secondary" id="uploadImageBtn" >Unggah Gambar</button> -->
                                <input type="file" class="form-control mt-3" accept="image/*" capture="camera"
                                    id="fileUpload" style="inline-block;">
                            </div>
                            <video id="cameraPreview" width="100%" autoplay style="display:none;"></video>
                            <img id="imagePreview" src="" style="display:none; max-width: 100%;" alt="Preview Gambar">
                        </div>
                        <div class="modal-footer">
                            <button id="deleteImageBtn" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="saveImageBtn" data-id="">Upload </button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="public/library/DataTables/datatables.min.js"></script>
            <!-- <script src="public/library/jquery/dist/jquery.min.js"></script> -->
            <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="public/library/sweetalert2/dist/sweetalert2.all.min.js"></script>
            <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
            <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="assets/vendor/chart.js/chart.umd.js"></script>
            <script src="assets/vendor/echarts/echarts.min.js"></script>
            <script src="assets/vendor/quill/quill.js"></script>
            <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
            <script src="assets/vendor/tinymce/tinymce.min.js"></script>
            <script src="assets/vendor/php-email-form/validate.js"></script>
            <script src="assets/js/main.js"></script>

            <script>
                const video = document.getElementById('cameraPreview');
                const imagePreview = document.getElementById('imagePreview');
                const deleteImageBtn = document.getElementById('deleteImageBtn'); // Tombol Hapus
                const modalCameraBtn = document.getElementsByClassName('openCameraBtn');
                const fileUpload = document.getElementById('fileUpload');
                const saveImageBtn = document.getElementById('saveImageBtn');

                // Membuka kamera saat tombol "Ambil Gambar" ditekan
                document.getElementById('openCameraBtn').addEventListener('click', function () {
                    navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                        .then(function (stream) {
                            video.srcObject = stream;
                            video.style.display = 'block'; // Tampilkan video
                            imagePreview.style.display = 'none'; // Sembunyikan gambar pratinjau
                            deleteImageBtn.style.display = 'none'; // Sembunyikan tombol hapus
                        })
                        .catch(function (err) {
                            console.error('Error accessing the camera: ', err);
                            Swal.fire('Gagal', 'Kamera tidak dapat diakses', 'error');
                        });
                });

                // Fungsi untuk menghentikan kamera
                function stopCamera() {
                    const stream = video.srcObject;
                    if (stream) {
                        const tracks = stream.getTracks();
                        tracks.forEach(track => track.stop());
                    }
                    video.srcObject = null;
                }

                // Menghentikan kamera saat modal ditutup
                $('#cameraModal').on('hidden.bs.modal', function () {
                    stopCamera();
                });
                // Fungsi untuk membuka modal kamera dan menetapkan ID
                $(document).on('click', '.openCameraButton', function () {
                    const id = $(this).attr("data-id");
                    $("#saveImageBtn").attr("data-id", id);

                    // var id = $(this).data('id'); // Ambil ID item terkait
                    //     var evidence = $('#evidence_' + id).val(); // Ambil nilai evidence (URL gambar)

                    //     // Periksa apakah gambar ada
                    //     if (evidence !== '') {
                    //         // Tampilkan gambar di preview
                    //         $('#cameraImagePreview').attr('src', evidence); // Set src untuk gambar preview
                    //         $('#cameraImagePreview').show(); // Tampilkan gambar jika ada
                    //         // Tampilkan modal jika gambar ada
                    //         $('#cameraModal').modal('show'); // Pastikan modal ditampilkan
                    //     } else {
                    //         $('#cameraImagePreview').hide(); // Sembunyikan gambar jika tidak ada
                });
                // Membuka kamera
                const modalCameraBtns = document.getElementsByClassName('openCameraButton');

                Array.from(modalCameraBtns).forEach(function (modalCameraBtn) {
                    modalCameraBtn.addEventListener('click', function () {
                        console.log("")
                        const id = this.getAttribute('data-id');
                        const evidenceInput = document.getElementById(`evidence_${id}`);
                        const imagePreview = document.getElementById('imagePreview');

                        // Cek apakah ada data evidence
                        const evidenceValue = evidenceInput.value;
                        if (evidenceValue) {
                            // Tampilkan gambar pratinjau
                            imagePreview.src = "./public/img/" +
                                evidenceValue; // Atur src untuk menampilkan gambar
                            imagePreview.style.display = 'block'; // Tampilkan gambar pratinjau
                        } else {
                            imagePreview.style.display = 'none'; // Sembunyikan jika tidak ada data
                        }

                    });
                });

                // // Membuka dialog file upload
                // document.getElementById('uploadImageBtn').addEventListener('click', function () {
                //     fileUpload.click(); // Buka dialog file upload
                // });
                // Menyimpan gambar yang diambil dari kamera
                saveImageBtn.addEventListener('click', function () {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    // Pastikan video sudah aktif
                    if (video.videoWidth && video.videoHeight) {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);

                        // Konversi ke file blob
                        canvas.toBlob(function (blob) {
                            if (!blob) {
                                console.error('Gagal menghasilkan blob dari canvas.');
                                return;
                            }

                            const randomName = 'image_' + Date.now() + '.png';
                            const file = new File([blob], randomName, {
                                type: 'image/png'
                            });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            fileUpload.files = dataTransfer.files; // Pastikan file terupload ke input file

                            // Tampilkan gambar pratinjau
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                imagePreview.src = e.target.result;
                                imagePreview.style.display = 'block';
                                deleteImageBtn.style.display = 'block'; // Tampilkan tombol hapus
                            };
                            reader.readAsDataURL(file);

                            // Menghentikan aliran kamera
                            const stream = video.srcObject;
                            if (stream) {
                                const tracks = stream.getTracks();
                                tracks.forEach(track => track.stop());
                            }
                            video.style.display = 'none';
                            openCameraBtn.style.display = 'block';
                            fileUpload.style.display = 'block';

                            // Pastikan peringatan tidak muncul setelah mengambil gambar
                            imageCaptured = true; // Tandai bahwa gambar telah diambil
                        }, 'image/png');
                    } else {
                        if (!fileUpload.files.length) {
                            console.error('Video tidak memiliki ukuran yang valid.');
                            Swal.fire('Gagal', 'Belum ada gambar', 'error');
                        }
                    }
                });
                // Fungsi untuk mengupload gambar via AJAX
                saveImageBtn.addEventListener('click', function () {
                    const formData = new FormData();
                    const evidenceField = document.getElementById('evidence_' + $("#saveImageBtn").attr("data-id"));
                    const file = fileUpload.files[0];

                    if (file) {
                        formData.append('file', file);
                        formData.append('id', $("#saveImageBtn").attr("data-id"));

                        $.ajax({
                            url: '/Incoming/upload/upload_image.php',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                if (response.includes("Error")) {
                                    Swal.fire('Gagal', 'Gambar tidak berhasil diupload', 'error');
                                } else {
                                    Swal.fire('Berhasil', 'Gambar berhasil diupload', 'success');
                                    evidenceField.value =
                                        response; // Simpan path gambar ke field evidence

                                    // Tutup modal setelah gambar berhasil diupload
                                    $('#cameraModal').modal('hide');

                                    // Menghapus backdrop modal agar halaman tidak gelap
                                    $('body').removeClass('modal-open');
                                    $('.modal-backdrop').remove();
                                }
                            },
                            error: function () {
                                Swal.fire('Gagal', 'Gambar gagal diupload', 'error');
                            }
                        });
                        //} else if (!imageCaptured) { // Cek jika gambar belum diambil
                        Swal.fire('Peringatan', 'Tidak ada gambar yang diupload', 'warning');
                    }
                });
                // Menangani unggahan file
                fileUpload.addEventListener('change', function (event) {
                    if (event.target.files.length > 0) {
                        const file = event.target.files[0];
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                            video.style.display = 'none'; // Sembunyikan kamera
                            openCameraBtn.style.display = 'none'; // Sembunyikan tombol ambil gambar
                            deleteImageBtn.style.display = 'block'; // Tampilkan tombol hapus
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Fungsi untuk menghapus gambar dan mengatur ulang input file
                deleteImageBtn.addEventListener('click', function () {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                    video.style.display = 'block'; // Tampilkan kamera lagi jika ada
                    openCameraBtn.style.display = 'block'; // Tampilkan tombol ambil gambar
                    deleteImageBtn.style.display = 'none'; // Sembunyikan tombol hapus

                    // Menghapus file yang terpilih pada input file
                    fileUpload.value = ''; // Mengatur ulang value input file
                });


                $(document).ready(function () {
                    // Fungsi untuk membuka modal kamera dan menetapkan ID
                    $(document).on('click', '.openCameraButton', function () {
                        const id = $(this).attr("data-id");
                        $("#saveImageBtn").attr("data-id", id); // Set data-id untuk penyimpanan gambar
                        const evidenceInput = document.getElementById(`evidence_${id}`);
                        const imagePreview = document.getElementById('imagePreview');
                        const deleteImageBtn = document.getElementById('deleteImageBtn'); // Tombol Hapus

                        // Cek apakah ada data evidence
                        const evidenceValue = evidenceInput.value;
                        if (evidenceValue) {
                            // Tampilkan gambar pratinjau
                            imagePreview.src = "./public/img/" +
                                evidenceValue; // Atur src untuk menampilkan gambar
                            imagePreview.style.display = 'block'; // Tampilkan gambar pratinjau
                            deleteImageBtn.style.display = 'block'; // Tampilkan tombol hapus
                        } else {
                            imagePreview.style.display = 'none'; // Sembunyikan gambar jika tidak ada data
                            deleteImageBtn.style.display =
                                'none'; // Sembunyikan tombol hapus jika tidak ada gambar
                        }
                    });

                    $(document).on('click', '#deleteImageBtn', function () {
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Gambar ini akan dihapus!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Tidak, batalkan!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const id = $("#saveImageBtn").attr(
                                    "data-id"); // Ambil ID gambar dari data-id
                                const evidenceInput = document.getElementById(
                                    `evidence_${id}`); // Ambil field evidence
                                const imagePreview = document.getElementById(
                                    'imagePreview'); // Ambil preview gambar
                                const cameraPreview = document.getElementById(
                                    'cameraPreview'); // Ambil preview kamera
                                const deleteImageBtn = document.getElementById(
                                    'deleteImageBtn'); // Ambil tombol hapus
                                const openCameraBtn = document.getElementById(
                                    "openCameraBtn"); // Ambil tombol "Ambil Gambar"

                                // Cek jika gambar sudah diupload ke server atau belum
                                if (!evidenceInput
                                    .value) { // Jika field evidence kosong, gambar belum diupload ke server
                                    // Langsung hapus gambar yang belum terkirim
                                    imagePreview.src = ''; // Kosongkan gambar preview
                                    imagePreview.style.display =
                                        'none'; // Sembunyikan gambar preview
                                    cameraPreview.style.display =
                                        'none'; // Sembunyikan preview kamera
                                    evidenceInput.value = ''; // Kosongkan field evidence
                                    deleteImageBtn.style.display =
                                        'none'; // Sembunyikan tombol hapus
                                    openCameraBtn.style.display =
                                        'inline-block'; // Tampilkan tombol "Ambil Gambar"
                                    Swal.fire('Berhasil', 'Gambar berhasil dihapus', 'success');
                                } else {
                                    // Kirim permintaan AJAX untuk menghapus gambar jika sudah terupload
                                    $.ajax({
                                        url: '/Incoming/upload/delete_image.php',
                                        type: 'POST',
                                        data: {
                                            id: id
                                        }, // Kirim ID gambar
                                        success: function (response) {
                                            console.log("Response dari server:",
                                                response
                                            ); // Log respons dari server untuk debugging
                                            if (response == "success") {
                                                // Kosongkan preview gambar
                                                imagePreview.src = '';
                                                imagePreview.style.display =
                                                    'none'; // Sembunyikan gambar preview
                                                cameraPreview.style.display =
                                                    'none'; // Sembunyikan preview kamera

                                                // Sembunyikan tombol hapus
                                                deleteImageBtn.style.display = 'none';

                                                // Kosongkan field evidence di form
                                                evidenceInput.value = '';

                                                // Tampilkan tombol "Ambil Gambar"
                                                openCameraBtn.style.display =
                                                    'inline-block';

                                                Swal.fire('Berhasil',
                                                    'Gambar berhasil dihapus',
                                                    'success');
                                            } else if (response == "file_not_found") {
                                                Swal.fire('Gagal',
                                                    'Gambar tidak ditemukan',
                                                    'error');
                                            } else {
                                                Swal.fire('Gagal',
                                                    'Gambar gagal dihapus', 'error');
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            console.error("Error dalam AJAX:", status,
                                                error); // Log jika ada kesalahan AJAX
                                            Swal.fire('Gagal',
                                                'Terjadi kesalahan dalam penghapusan gambar',
                                                'error');
                                        }
                                    });
                                }
                            }
                        });
                    });
                });

                //TOMBOL INSPEKSI
                $("button[name='save']").click(function (event) {
                    event.preventDefault(); // Mencegah submit default

                    var row = $(this).closest("tr");
                    var form = $(this).closest("form");

                    // Validasi sampling
                    var smpling = row.find("input[name='smpling[]']").val();
                    if (!smpling || smpling <= 1) {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Pastikan kolom sampling terisi sesuai dengan ketentuan',
                            icon: 'warning',
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                        return;
                    }

                    // Ambil nilai dari input di baris yang dipilih
                    var id = row.find("input[name='id[]']").val();
                    var smpling = row.find("input[name='smpling[]']").val();
                    var ng = row.find("input[name='ng[]']").val();
                    var ok = row.find("input[name='ok[]']").val();
                    var ng_kategori = row.find("select[name='ng_kategori[]']").val();
                    var ng_detail = row.find("input[name='ng_detail[]']").val();
                    var check_dt = row.find("input[name='check_dt[]']").val();
                    var evidence = row.find("input[name='evidence[]']").val();

                    // Mengubah format tanggal check_dt dari d-m-Y ke Y-m-d
                    var dateParts = check_dt.split('-');
                    var formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]; // Y-m-d format

                    // Validasi kolom NG dan Evidence
                    var isValid = true;
                    console.log(ng)
                    if (parseFloat(ng) > 0 && !evidence) {
                        isValid = false;
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Upload gambar apabila terjadi NG.',
                            icon: 'warning',
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    }

                    if (isValid) {
                        // Buat form data untuk dikirimkan via AJAX
                        var formData = new FormData();
                        formData.append('save', true);
                        formData.append('id', id);
                        formData.append('smpling', smpling);
                        formData.append('ng', ng);
                        formData.append('ok', ok);
                        formData.append('ng_kategori', ng_kategori);
                        formData.append('ng_detail', ng_detail);
                        formData.append('check_dt', formattedDate); // Kirim tanggal dengan format Y-m-d
                        formData.append('evidence', evidence);
                        console.log({
                            id: id,
                            smpling: smpling,
                            ng: ng,
                            ok: ok,
                            ng_kategori: ng_kategori,
                            ng_detail: ng_detail,
                            check_dt: formattedDate, // Menampilkan tanggal yang telah diformat
                            evidence: evidence
                        });
                        // Submit data melalui AJAX
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: 'Data berhasil disimpan!',
                                    icon: 'success',
                                    showConfirmButton: true,  // Pastikan tombol OK terlihat
                                    confirmButtonText: 'OK', // Label untuk tombol OK
                                    allowOutsideClick: false, // Tidak menutup dialog jika klik di luar
                                    allowEscapeKey: false, // Tidak menutup dengan tombol Escape
                                    focusConfirm: true, //// Fokuskan pada tombol OK sehingga bisa ditekan Enter
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        row.remove(); // Menghapus baris tabel
                                        // location
                                        //     .reload(); // Refresh halaman setelah data disimpan
                                    }
                                });
                            },
                            error: function () {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Gagal menyimpan data!',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });

                function confirmLogout() {
                    Swal.fire({
                        title: 'Apakah Anda yakin ingin logout?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, logout!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'logout.php'; // Change to your logout URL
                        }
                    });
                }
                $(document).ready(function () {
                    $('#receiptTable').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": false,
                        "info": true,
                        "autoWidth": false,
                        "language": {
                            "emptyTable": "Tidak ada data Receipt"

                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function () {
                    // Ambil semua baris dalam tabel
                    const rows = document.querySelectorAll('tr');
                    // Loop melalui setiap baris untuk menambahkan event listener pada setiap input
                    rows.forEach(row => {
                        const ngInput = row.querySelector('.ng');
                        const qstokInput = row.querySelector('.qstok');
                        const okSpan = row.querySelector('.ok');
                        const ngKategoriSelect = row.querySelector('.ng_kategori');
                        const uploadButton = row.querySelector('.openCameraButton'); // Tombol upload
                        const ngDetailInput = row.querySelector('.ng_detail'); // Kolom ng_detail

                        // Pastikan elemen yang diperlukan ada
                        if (ngInput && qstokInput && okSpan && ngKategoriSelect && uploadButton && ngDetailInput) {
                            // Fungsi untuk mengupdate kolom ok berdasarkan ng dan qstok
                            function updateOkValue() {
                                const ngValue = parseInt(ngInput.value) || 0;  // Pastikan nilai ng adalah angka
                                const qstokValue = parseInt(qstokInput.value) || 0;  // Pastikan nilai qstok adalah angka

                                // Jika ng = 0, ok diisi dengan nilai qstok
                                if (ngValue === 0) {
                                    okSpan.textContent = qstokValue; // Mengubah konten span
                                    ngKategoriSelect.disabled = true;  // Menonaktifkan ng_kategori
                                    uploadButton.disabled = true;  // Menonaktifkan tombol upload
                                    ngDetailInput.disabled = true;  // Menonaktifkan ng_detail
                                } else {
                                    // Jika ng > 0, ok diisi dengan hasil perhitungan qstok - ng
                                    okSpan.textContent = qstokValue - ngValue; // Mengubah konten span
                                    ngKategoriSelect.disabled = false;  // Mengaktifkan ng_kategori
                                    uploadButton.disabled = false;  // Mengaktifkan tombol upload
                                    ngDetailInput.disabled = false;  // Mengaktifkan ng_detail
                                }
                            }

                            // Event listener untuk perubahan nilai di kolom ng
                            ngInput.addEventListener('input', updateOkValue);

                            // Pastikan kolom ok terisi otomatis saat halaman pertama kali dimuat
                            updateOkValue();  // Memastikan bahwa kolom 'ok' terisi pada load pertama
                        }
                    });
                });


                document.addEventListener('DOMContentLoaded', function () {
                    getOk(); // Panggil fungsi untuk inisialisasi saat halaman pertama dimuat
                });

                function getOk() {
                    const rows = document.querySelectorAll('tr');

                    // Loop melalui setiap baris untuk menambahkan event listener pada setiap input
                    rows.forEach(row => {
                        const ngInput = row.querySelector('.ng');
                        const qstokInput = row.querySelector('.qstok');
                        const okSpan = row.querySelector('.ok'); // Mengambil elemen span untuk 'ok'

                        // Pastikan elemen yang diperlukan ada
                        if (ngInput && qstokInput && okSpan) {
                            // Fungsi untuk mengupdate kolom ok berdasarkan ng dan qstok
                            function updateOkValue() {
                                const ngValue = parseInt(ngInput.value) || 0;  // Pastikan nilai ng adalah angka
                                const qstokValue = parseInt(qstokInput.value) || 0;  // Pastikan nilai qstok adalah angka

                                // Jika ng = 0, ok diisi dengan nilai qstok
                                if (ngValue === 0) {
                                    okSpan.value = qstokValue; // Ubah konten span (bukan .value)
                                } else {
                                    // Jika ng > 0, ok diisi dengan hasil perhitungan qstok - ng
                                    okSpan.value = qstokValue - ngValue; // Ubah konten span (bukan .value)
                                }
                            }

                            // Event listener untuk perubahan nilai di kolom ng
                            ngInput.addEventListener('input', updateOkValue); // Menggunakan 'input' untuk setiap perubahan

                            // Event listener untuk perubahan nilai di kolom qstok
                            qstokInput.addEventListener('input', updateOkValue); // Menggunakan 'input' juga untuk kolom qstok

                            // Pastikan kolom ok terisi otomatis saat halaman pertama kali dimuat
                            updateOkValue();  // Memastikan bahwa kolom 'ok' terisi pada load pertama
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    // Fungsi untuk membatasi input hanya angka
                    function validateNumericInput(event) {
                        // Cek jika input bukan angka
                        let value = event.target.value;
                        let isValid = /^[0-9]*\.?[0-9]*$/.test(value); // Hanya angka dan titik desimal yang diperbolehkan

                        // Jika bukan angka, hapus input
                        if (!isValid) {
                            event.target.value = value.replace(/[^0-9\.]/g, ''); // Menghapus karakter yang bukan angka
                        }
                    }

                    // Pilih elemen input untuk sampling dan ng
                    const smplingInputs = document.querySelectorAll('.smpling');
                    const ngInputs = document.querySelectorAll('.ng');

                    // Tambahkan event listener untuk setiap input
                    smplingInputs.forEach(input => {
                        input.addEventListener('input', validateNumericInput);
                    });

                    ngInputs.forEach(input => {
                        input.addEventListener('input', validateNumericInput);
                    });
                });

                $(document).ready(function () {
                    // Fungsi untuk memeriksa status gambar dan mengubah warna tombol
                    function updateButtonColor() {
                        $('input[name="evidence[]"]').each(function () {
                            var evidence = $(this).val(); // Mendapatkan nilai evidence
                            var id = $(this).attr('id').split('_')[1]; // Mengambil ID dari elemen hidden
                            var button = $('#cameraButton_' + id); // Menentukan elemen tombol berdasarkan ID

                            // Jika evidence ada, ganti warna tombol
                            if (evidence !== '') {
                                button.removeClass('btn-secondary').addClass('btn-success'); // Ganti warna tombol ke merah
                                button.find('i').attr('title', 'Gambar Ada'); // Tooltip
                            } else {
                                button.removeClass('btn-success').addClass('btn-secondary'); // Kembalikan warna tombol ke default
                                button.find('i').attr('title', 'Empty'); // Tooltip jika gambar kosong
                            }
                        });
                    }
                    updateButtonColor();
                    setInterval(updateButtonColor, 1000); // Setiap 3 detik

                    // Ketika tombol kamera diklik
                    $('.openCameraButton').on('click', function () {
                        var id = $(this).data('id'); // Ambil ID item terkait
                        var evidence = $('#evidence_' + id).val(); // Ambil nilai evidence (URL gambar)

                        // Periksa apakah gambar ada
                        //     if (evidence !== '') {
                        //         // Tampilkan gambar di preview
                        //         $('#cameraImagePreview').attr('src', evidence); // Set src untuk gambar preview
                        //         $('#cameraImagePreview').show(); // Tampilkan gambar jika ada

                        //         // Tampilkan modal jika gambar ada
                        //         $('#cameraModal').modal('show'); // Pastikan modal ditampilkan
                        //     } else {
                        //         $('#cameraImagePreview').hide(); // Sembunyikan gambar jika tidak ada
                        //     }
                    });
                });

            </script>
</body>

</html>