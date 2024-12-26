<?php
include 'config.php';
$current_page = basename($_SERVER['PHP_SELF']);
session_start(); // Start the session
if (!isset($_SESSION['npk'])) {
    header('Location: index.php');
    exit();
}
$servername = "localhost"; // Ganti dengan server Anda
$username = "root"; // Ganti dengan username Anda
$password = ""; // Ganti dengan password Anda
$dbname_warehouse = "warehouse"; // Nama database

$connwarehouse = new mysqli($servername, $username, $password, $dbname_warehouse);

if ($connwarehouse->connect_error) {
    die("Koneksi gagal: " . $connwarehouse->connect_error);
}

if (isset($_GET['rcno'])) {
    $rcno = $_GET['rcno'];
    // $sql = "SELECT ic.*, ng.ng_desc, ng.deskripsi FROM incoming_check ic
    //         LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
    //         WHERE rcno = '$rcno' ";
    $sql = "SELECT ic.*, ng.ng_desc, ng.deskripsi 
    FROM incoming_check ic
    LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
    WHERE rcno = '$rcno' 
    AND ic.ng IS NOT NULL 
    AND ic.ok IS NOT NULL 
    AND ic.smpling IS NOT NULL
    AND ic.check_usr IS NOT NULL
    AND ic.ng_detail IS NOT NULL
    ";
    $result = $connwarehouse->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['check_user_name'] = getNameFromUsers($connlembur, $row['check_usr']);
            $data[] = $row; // Tambahkan setiap baris data ke array
        }
    }
    echo json_encode($data); // Return semua data dalam format JSON

    exit(); // Stop script execution
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        // Get data from form
        $rcno = $_POST['rcno'];
        $orno = $_POST['orno'];
        $item = $_POST['item'];
        $qstok = $_POST['qstok'];
        $dsca = $_POST['dsca'];
        $nama = $_POST['nama'];
        $smpling = $_POST['smpling'];
        $ok = $_POST['ok'];
        $ng = $_POST['ng'];
        $ng_kategori = $_POST['ng_kategori'];
        $ng_detail = $_POST['ng_detail'];
        $check_dt = $_POST['check_dt'];
        $status = $_POST['status'];
        $check_usr = $_POST['check_usr'];

        $sql = "UPDATE incoming_check SET rcno='$rcno', orno='$orno', item='$item', 
                qstok=$qstok, dsca='$dsca', nama='$nama', smpling='$smpling' , ok='$ok' , ng='$ng' , ng_kategori='$ng_kategori', ng_detail='$ng_detail' , check_dt='$check_dt', check_usr='$check_usr' WHERE id=$id";

        if ($connwarehouse->query($sql) === TRUE) {
            $_SESSION['message'] = "Data berhasil diperbarui!";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $connwarehouse->error;
            $_SESSION['message_type'] = 'error';
        }
    }
    header("Location: Viewjudge.php");
    exit();
}

if (isset($_SESSION['message'])) {
    echo "<script>Swal.fire('{$_SESSION['message']}', '', '{$_SESSION['message_type']}');</script>";
    unset($_SESSION['message'], $_SESSION['message_type']); // Clear message after display
}

// Queery card
$today = date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));
$sql = "SELECT ic.*, ng.ng_desc FROM incoming_check ic
        LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
        WHERE ic.trdt BETWEEN ? AND ?
        GROUP BY ic.rcno";

$stmt = $connwarehouse->prepare($sql);
$stmt->bind_param('ss', $startOfWeek, $endOfWeek);
$stmt->execute();

$result = $stmt->get_result();
$currUrl = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Incoming</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="public/img/kyb-icon.ico" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/card.css" rel="stylesheet">
    <link href="public/library/DataTables/datatables.min.css" rel="stylesheet">
    <link href="public/library/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <script src="public/library/sweetalert2/dist/sweetalert2.all.min.js"></script>
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
                        class="nav-link mx-2 <?php echo strpos($currUrl, 'dashboard') !== false ? 'text-danger' : ''; ?>"
                        id="dashboard-link">
                        <span>Beranda</span>
                    </a>
                    <div class="nav-link dropdown mx-2">
                        <a href="#"
                            class="nav-link <?php echo strpos($currUrl, 'Viewjudge') !== false ? 'text-danger' : ''; ?>"
                            data-bs-toggle="dropdown" role="button">
                            <span>Tables Receipt</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="Viewjudge.php" class="dropdown-item" id="tables-receipt-link">Data Receipt</a>
                            <li><a href="HistoryInspeksi.php" class="dropdown-item">History Inspeksi</a></li>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <!-- <a href="#" class="nav-link ms-auto me-3" onclick="confirmLogout()">
                <button class="btn btn-danger" type="submit">Keluar</button>
            </a> -->
            <div class="row">
                <div class="d-flex justify-content-end w-100">
                    <div class="dropdown">
                        <span class="dropdown-toggle me-2" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false" style="cursor: pointer;">
                            Hallo, <strong> <?php
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
        <div class="pagetitle"> </div>
        <div class="col-lg-12">
            <div class="card mt-3">
                <div class="card-body">
                    <h1>Data Receipt</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Beranda</a></li>
                            <!-- <li class="breadcrumb-item active"><a href="Viewjudge.php">Receipt</a></li> -->
                        </ol>
                    </nav>
                    <?php if ($_SESSION['golongan'] == '3') { ?>
                        <div class="position-absolute top-0 end-0 m-4 d-flex align-items-center">
                            <!-- Tampilkan menu Lajur untuk golongan 3 -->
                            <button class="btn btn-light mx-2" data-bs-toggle="modal" data-bs-target="#lajurModal"
                                id="lajur-link">
                                Lajur
                            </button>
                            <div class="dropdown mx-2">
                                <button class="btn btn-light dropdown-toggle" type="button" id="statusDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Status
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                    <li><a class="dropdown-item" href="#" data-status="belum-dicek">Belum dicek</a></li>
                                    <li><a class="dropdown-item" href="#" data-status="sudah-dicek">Sudah dicek</a></li>
                                </ul>
                            </div>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari"
                                onkeyup="searchData()">
                        </div>
                    <?php } ?>


                    <?php
                    if ($result->num_rows > 0) {
                        echo "<div class='row'>";
                        $displayed_rcnos = [];
                        while ($row = $result->fetch_assoc()) {
                            if (!in_array($row['rcno'], $displayed_rcnos)) {
                                $displayed_rcnos[] = $row['rcno'];

                                // Subquery untuk mendapatkan status judge berdasarkan rcno
                                $rcno = $row['rcno'];
                                $subquery = "SELECT ic.ok, ng.ng_desc
                                FROM incoming_check ic
                                LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
                                WHERE ic.rcno = ?
                                ORDER BY ic.trdt DESC"; // Mengurutkan berdasarkan timestamp terbaru
                    
                                $stmt = $connwarehouse->prepare($subquery);
                                $stmt->bind_param('s', $rcno);
                                $stmt->execute();
                                $subresult = $stmt->get_result();

                                // Tentukan ikon berdasarkan subquery
                                $status_icon = 'bi-exclamation-circle'; // Default: ada yang belum dicek
                                $status_class = 'text-danger';
                                $all_ok = true; // Flag untuk mengecek apakah semua 'judge' = OK
                    
                                while ($subrow = $subresult->fetch_assoc()) {
                                    if (empty($subrow['ok'])) {
                                        $all_ok = false; // Jika ada yang bukan 'OK'
                                        break;
                                    }
                                }

                                // Set ikon jika semua OK
                                if ($all_ok) {
                                    $status_icon = 'bi-check-circle';
                                    $status_class = 'text-success';
                                }

                                $formatted_trdt = date('Y-m-d', strtotime($row['trdt'])); //
                                // Output kartu dengan status ditambahkan
                                $status = $all_ok ? 'sudah-dicek' : 'belum-dicek'; // Tentukan status
                                echo "<div class='col-md-4 mb-5 card-container {$status}'>
                                    <div class='card'>
                                        <div class='card-body'>
                                            <h5 class='card-title'><strong>#</strong> {$row['rcno']} ({$row['orno']})</h5>
                                            <p class='card-text'>{$row['nama']}<strong> | </strong>{$row['bpid']}</p>
                                            <!--<p class='card-text'>{$row['trdt']}</p>-->
                                            <p class='card-text'>{$formatted_trdt}</p> <!-- Menampilkan tanggal saja -->
                                            <button class='btn btn-info' onclick='viewDetailData(\"{$row['rcno']}\")'>Detail</button>";
                                if (!$all_ok) {
                                    echo "<a class='btn btn-secondary' href='ViewInpeksi.php?rcno={$row['rcno']}'>Mulai Inspeksi</a>";
                                }
                                echo "</div>
                                        <div class='position-absolute top-0 end-0 m-3'>
                                            <i class='bi {$status_icon} {$status_class}' style='font-size: 24px;'></i>
                                        </div>
                                    </div>
                                </div>";
                            }
                        }
                        echo "</div>";
                    } else {
                        echo "<p>No data found.</p>";
                    }
                    ?>

                    <!--MODAL DETAILS-->
                    <div class="modal fade" id="dataDetailModal" tabindex="-1" aria-labelledby="dataDetailModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-center w-100">
                                    <h5 class="modal-title text-center w-100" id="dataDetailModalLabel">Data Hasil
                                        Inspeksi</h5>
                                    <button type="button" class="btn-close btn-lg fs-2" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped w-100" id="dataDetailTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th data-orderable="false">No Part</th>
                                                    <th data-orderable="false">Produk</th>
                                                    <th data-orderable="false">Quantity</th>
                                                    <th data-orderable="false">Sampling</th>
                                                    <th data-orderable="false">NG</th>
                                                    <th data-orderable="false">OK</th>
                                                    <th data-orderable="false">Bukti NG</th>
                                                    <th data-orderable="false">Kategori</th>
                                                    <th data-orderable="false">Detail NG</th>
                                                    <th data-orderable="false">Tanggal Inspeksi</th>
                                                    <th data-orderable="false">Nama Pengecek</th>
                                                    <th data-orderable="false">Judge</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dataDetailContent"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Bootstrap -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center w-100" id="imageModalLabel">Bukti NG </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="modalImage" src="" alt="Preview" style="max-width: 100%; height: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>
    </main>
    <!-- Modal Bootstrap -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center w-100" id="imageModalLabel">Bukti NG
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Preview" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Lajur -->
    <div class="modal fade" id="lajurModal" tabindex="-1" aria-labelledby="lajurModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lajurModalLabel">Form Lajur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="lajurForm">
                        <div class="mb-3">
                            <label for="supplierName" class="form-label">Nama Supplier</label>
                            <div>
                                <select class="form-selectpicker" id="supplierName" name="supplierName[]" multiple
                                    data-live-search="true" required title="Pilih Supplier">
                                    <option value="" disabled>Pilih Nama Supplier</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="supplierStatus" class="form-label">Status</label>
                            <select class="form-select" id="supplierStatus" name="supplierStatus" required>
                                <option value="" disabled selected>Pilih Lajur</option>
                                <option value="1">Merah</option>
                                <option value="2">Hijau</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" form="lajurForm">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="public/library/jquery/dist/jquery.min.js"></script>
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="public/library/DataTables/datatables.min.js"></script>
    <script src="public/library/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Inisialisasi Bootstrap-Select
        document.addEventListener('DOMContentLoaded', function () {
            $('.selectpicker').selectpicker();
        });
        $(document).ready(function () {
            // Memanggil endpoint PHP untuk mendapatkan data supplier
            $.ajax({
                url: 'upload/getLajur.php', // Gantilah dengan path yang sesuai untuk endpoint Anda
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    if (response.success) {
                        // Mengisi dropdown Nama Supplier dengan data yang diterima
                        const supplierDropdown = $('#supplierName');
                        supplierDropdown.empty(); // Mengosongkan dropdown terlebih dahulu
                        supplierDropdown.append(
                            '<option value="" disabled>Pilih Nama Supplier</option>'
                        ); // Menambahkan pilihan default
                        response.data.forEach(function (supplier) {
                            supplierDropdown.append('<option value="' + supplier.nama + '">' + supplier.nama + '</option>');
                        });

                        // Refresh selectpicker agar data baru dikenali
                        supplierDropdown.selectpicker('refresh');
                    } else {
                        console.log('Error fetching supplier data:', response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Request failed:', xhr);
                }
            });
        });


        // Menangani pemilihan item pada dropdown
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (e) {
                // Mengambil status yang dipilih dari teks item
                const status = e.target.innerText;

                // Mengubah teks tombol dropdown dengan status yang dipilih
                document.getElementById('statusDropdown').innerText = status;
            });
        });

        function viewDetailData(rcno) {
            if ($.fn.DataTable.isDataTable("#dataDetailTable")) {
                $("#dataDetailTable").DataTable().destroy();
            }

            fetch('Viewjudge.php?rcno=' + rcno)
                .then(response => response.json())
                .then(data => {
                    const content = document.getElementById('dataDetailContent');
                    content.innerHTML = ''; // Clear table content before adding new data
                    data.forEach(item => {
                        console.log(item);
                        let judgeStatus;
                        let buttonClass;

                        // Hanya tampilkan jika item dalam status "OK" atau "NG" dan sudah diperiksa (smpling != 0)
                        if (item.smpling != 0 && (item.ok != 0 || item.ng != 0)) {
                            // Tentukan status dan buttonClass berdasarkan kondisi
                            if (item.ng == 0 && item.ok != 0) {
                                judgeStatus = 'OK';
                                buttonClass = 'btn btn-success'; // Button dengan class btn-success
                            } else if (item.ng != 0) {
                                judgeStatus = 'NG';
                                buttonClass = 'btn btn-danger'; // Button dengan class btn-danger
                            }
                            // Cek apakah tanggal ada, jika tidak ada, set dengan "Belum ada tanggal"
                            let checkDate = item.check_dt ? new Date(item.check_dt).toLocaleDateString() : '';
                            // Menambahkan baris ke dalam tabel jika sudah memenuhi kondisi
                            content.innerHTML += `
                    <tr>
                        <td>${item.no_part}</td>
                        <td>${item.item}</td>
                        <td>${item.qstok}</td>
                        <td>${item.smpling}</td>
                        <td>${item.ng}</td>
                        <td>${item.ok}</td>
                        <td>
                            ${item.evidence ?
                                    `<img src="public/img/${item.evidence}" style="max-width: 50px; height: auto; cursor: pointer;" onclick="openModal('public/img/${item.evidence}')">`
                                    : ''
                                }
                        </td>
                        <td>${item.ng_desc ? item.ng_desc + ' - ' + item.deskripsi : ''}</td>
                        <td>${item.ng_detail}</td>
                        <td>${checkDate}</td>
                        <td>${item.check_usr} - ${item.check_user_name}</td>
                        <td><button class="${buttonClass}" disabled>${judgeStatus}</button></td>
                    </tr>
                `;
                        }
                    });

                    $('#dataDetailTable').DataTable({
                        "columnDefs": [{
                            "targets": 0, // Kolom pertama (indeks 0)
                            "orderable": false // Menonaktifkan sorting pada kolom pertama
                        }],
                        "order": []
                    });
                    const modal = new bootstrap.Modal(document.getElementById('dataDetailModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal mendapatkan data.', 'error');
                });
        }

        function searchData() {
            var input, filter, rows, rcno, nama, ok, i, txtValue1, txtValue2;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            rows = document.querySelectorAll('.col-md-4'); // Ambil semua elemen dengan kelas .col-md-4

            for (i = 0; i < rows.length; i++) {
                rcno = rows[i].querySelector('.card-title').textContent; // Dapatkan RCNO
                nama = rows[i].querySelector('.card-text').textContent; // Dapatkan Nama

                txtValue1 = rcno.toUpperCase();
                txtValue2 = nama.toUpperCase();

                // Menyembunyikan row jika pencarian tidak cocok
                if (txtValue1.indexOf(filter) > -1 || txtValue2.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        // Menangani perubahan status yang dipilih di dropdown
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function () {
                const selectedStatus = this.getAttribute('data-status'); // Ambil status dari data-status
                const cards = document.querySelectorAll('.card-container');

                // Menyembunyikan semua kartu terlebih dahulu
                cards.forEach(card => {
                    card.style.display = 'none';
                });

                // Menampilkan kartu sesuai status yang dipilih
                if (selectedStatus) {
                    document.querySelectorAll(`.card-container.${selectedStatus}`).forEach(card => {
                        card.style.display = 'block';
                    });
                } else {
                    // Jika tidak ada filter, tampilkan semua kartu
                    cards.forEach(card => {
                        card.style.display = 'block';
                    });
                }
            });
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

        function openModal(imageSrc) {
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }
    </script>
</body>

</html>