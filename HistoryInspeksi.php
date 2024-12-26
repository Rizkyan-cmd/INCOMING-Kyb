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

// if (isset($_GET['rcno'])) {
//     $rcno = $_GET['rcno'];
$currentMonth = date('m'); // Bulan saat ini (format: 01-12)
$currentYear = date('Y');  // Tahun saat ini (format: 2024)

// $sql = "SELECT ic.*, ng.ng_desc, ng.deskripsi  
//         FROM incoming_check ic
//         LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
//         WHERE MONTH(ic.check_dt) = ? AND YEAR(ic.check_dt) = ?";
$sql = "SELECT ic.*, ng.ng_desc, ng.deskripsi  
        FROM incoming_check ic
        LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
        WHERE MONTH(ic.check_dt) = ? 
          AND YEAR(ic.check_dt) = ?
          AND ic.check_dt IS NOT NULL
          AND ic.smpling IS NOT NULL";

$stmt = $connwarehouse->prepare($sql);
$stmt->bind_param("ii", $currentMonth, $currentYear); // Bind bulan dan tahun
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query gagal: " . $connwarehouse->error); // Tampilkan error query jika ada
}
// }
$currUrl = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>History Inspeksi</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="public/img/kyb-icon.ico" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/card.css" rel="stylesheet">
    <link href="public/library/DataTables/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/library/sweetalert2/dist/sweetalert2.min.css">
  
</head>

<body>
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="public/img/kayaba-logo.png" alt="Logo" class="me-3" style="width: 125px; height: auto;">
            </div>
            <div class="d-flex justify-content-center flex-grow-1">
                <nav class="d-flex align-items-center">
                    <a href="dashboard.php"
                        class="nav-link mx-2 <?php echo strpos($currUrl, 'dashboard') !== false ? 'text-danger' : ''; ?>"
                        id="dashboard-link">
                        <span>Beranda</span>
                    </a>
                    <div class="nav-link dropdown mx-2">
                        <a href="#"
                            class="nav-link <?php echo strpos($currUrl, 'HistoryInspeksi') !== false ? 'text-danger' : ''; ?>"
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
            <div class="row">
                <div class="d-flex justify-content-end w-100">
                    <div class="dropdown">
                        <span class="dropdown-toggle me-2" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false" style="cursor: pointer;">
                            Hallo, <strong>
                                <?php
                                $firstName = explode(' ', $_SESSION['full_name'])[0];
                                echo htmlspecialchars($firstName);
                                ?>!
                            </strong>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" onclick="return confirmLogout(event);">
                                    <i class="bi bi-box-arrow-right me-2"></i>
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
                    <h5 class="text-center mb-4">History Data Hasil Inspeksi</h5>
                    <div class="container">
                        <div class="row">
                            <!-- Kolom Pilih Tanggal -->
                            <div class="col-md-6 mb-3">
                                <!-- <label for="filterDate" class="form-label">Pilih Tanggal:</label> -->
                                <input type="date" id="filterDate" class="form-control" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <!-- <label for="statusDropdown" class="form-label">Pilih Status:</label> -->
                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle w-100" type="button"
                                        id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Pilih Status
                                    </button>
                                    <ul class="dropdown-menu w-100" aria-labelledby="statusDropdown">
                                        <li>
                                            <button class="dropdown-item text-center" type="button"
                                                data-status="belum-dicek">
                                                DATA NG
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-center" type="button"
                                                data-status="sudah-dicek">
                                                DATA OK
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Beranda</a></li>
                                    <li class="breadcrumb-item active"><a href="Viewjudge.php">Receipt</a></li>
                                </ol>
                            </nav>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="receiptTable">
                                    <thead class="table-dark text-center">
                                        <tr>
                                            <th>No Receipt</th>
                                            <th>Supplier</th>
                                            <th>Produk</th>
                                            <th>No Part</th>
                                            <th>Quantity</th>
                                            <th>Sampling</th>
                                            <th>NG</th>
                                            <th>OK</th>
                                            <th>NG Kategori</th>
                                            <th>Detail NG</th>
                                            <th>Upload</th>
                                            <th>Tanggal Inspeksi</th>
                                            <th>Nama Pengecek</th>
                                            <th>Judge</th>
                                        </tr>
                                    </thead>
                                    <!-- Modal Bootstrap -->
                                    <div class="modal fade" id="imageModal" tabindex="-1"
                                        aria-labelledby="imageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-center w-100" id="imageModalLabel">Bukti
                                                        NG
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img id="modalImage" src="" alt="Preview"
                                                        style="max-width: 100%; height: auto;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <tbody>
                                        <?php
                                        $status = isset($_GET['status']) ? $_GET['status'] : '';

                                        $whereClause = '';
                                        if ($status === 'belum-dicek') {
                                            $whereClause = "AND ic.ng > 0";
                                        } elseif ($status === 'sudah-dicek') {
                                            $whereClause = "AND ic.ng = 0";
                                        }

                                        $sql = "SELECT ic.*, ng.ng_desc, ng.deskripsi  
                                        FROM incoming_check ic
                                        LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
                                        WHERE MONTH(ic.check_dt) = ? 
                                        AND YEAR(ic.check_dt) = ?
                                        AND ic.check_dt IS NOT NULL
                                        AND ic.smpling IS NOT NULL
                                        $whereClause";

                                        $stmt = $connwarehouse->prepare($sql);
                                        $month = date('m'); // Contoh pengisian data bulan
                                        $year = date('Y');  // Contoh pengisian data tahun
                                        $stmt->bind_param('ii', $month, $year);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

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
                                                    echo "<td>
                                                    <img src='public/img/" . $imagePath . "' alt='Evidence' style='width: 100px; height: auto; cursor: pointer;' 
                                                        data-bs-toggle='modal' data-bs-target='#imageModal' onclick=\"showImage('public/img/$imagePath')\">
                                                </td>";
                                                } else {
                                                    echo "<td></td>";
                                                }

                                                echo "<td>" . htmlspecialchars(date("d-m-Y", strtotime($row['check_dt']))) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['check_usr']) . ' | ' . getNameFromUsers($connlembur, $row['check_usr']) . "</td>";
                                                echo "<td>
                                                    <span class='btn " . ($row['ng'] == 0 ? "btn-success" : "btn-danger") . " disabled'>" . ($row['ng'] == 0 ? 'OK' : 'NG') . "</span>
                                                </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='11' class='text-center'>Tidak ada data ditemukan</td></tr>";
                                        }

                                        $connwarehouse->close();
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
    </main>
    <script src="public/library/jquery/dist/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="public/library/DataTables/datatables.min.js"></script>
    <script src="public/library/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        // Pisahkan fungsi untuk mengatur teks status dan logout
        function setStatusButtonText(status) {
        const dropdownButton = document.getElementById('statusDropdown');
            if (status === 'belum-dicek') {
                dropdownButton.textContent = 'DATA NG';
            } else if (status === 'sudah-dicek') {
                dropdownButton.textContent = 'DATA OK';
            } else {
                dropdownButton.textContent = 'Pilih Status';
            }
        }

        // Tambahkan variabel untuk tracking AJAX requests
        let activeAjaxRequests = 0;

        $(document).ajaxSend(function() {
            activeAjaxRequests++;
        });

        $(document).ajaxComplete(function() {
            activeAjaxRequests--;
        });

        // Tambahkan event listener untuk window load/refresh
        window.addEventListener('load', function() {
            // Reset status filter
            setStatusButtonText(null);
            
            // Clear URL parameters and reload page without parameters
            const url = new URL(window.location.href);
            if (url.search !== '') {
                url.search = '';
                window.location.href = url.toString(); // Reload halaman tanpa parameter
                return;
            }
            
            // Reset table to initial state
            if ($.fn.DataTable.isDataTable('#receiptTable')) {
                $('#receiptTable').DataTable().destroy();
            }
            
            // Initialize DataTable with default settings
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
            
            // Reset filter date
            $('#filterDate').val('');
        });

        // Pisahkan event handler untuk status dropdown
        document.querySelectorAll('.dropdown-item[data-status]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const status = this.getAttribute('data-status');
                const statusText = this.textContent.trim();
                
                // Update button text
                setStatusButtonText(status);
                
                // Tutup dropdown menggunakan Bootstrap 5 native API
                const dropdownEl = this.closest('.dropdown');
                const bsDropdown = bootstrap.Dropdown.getInstance(dropdownEl.querySelector('.dropdown-toggle'));
                if (bsDropdown) {
                    bsDropdown.hide();
                }
                
                // Update URL and make AJAX request
                const url = new URL(window.location.href);
                url.searchParams.set('status', status);
                
                $.ajax({
                    url: url.toString(),
                    method: 'GET',
                    success: function(response) {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(response, 'text/html');
                        const newTbody = doc.querySelector('#receiptTable tbody');
                        
                        // Destroy existing DataTable
                        if ($.fn.DataTable.isDataTable('#receiptTable')) {
                            $('#receiptTable').DataTable().destroy();
                        }
                        
                        // Update tbody content
                        document.querySelector('#receiptTable tbody').innerHTML = newTbody.innerHTML;
                        
                        // Reinitialize DataTable
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
                    },
                    error: function() {
                        alert("Terjadi kesalahan saat memperbarui data.");
                    }
                });
            });
        });

        // Reset filter date on page load
        $(document).ready(function() {
            $('#filterDate').val(''); // Reset date input
        });

        // Fungsi logout yang terpisah
        function confirmLogout(e) {
            if(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            Swal.fire({
                title: 'Apakah Anda yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
            return false;
        }

        function showImage(imagePath) {
            document.getElementById('modalImage').src = imagePath;
        }
        
        $(document).ready(function () {
            // Ketika pengguna memilih tanggal
            $('#filterDate').change(function () {
                var selectedDate = $(this).val(); // Mendapatkan tanggal yang dipilih
                if (selectedDate) {
                    filterByDate(selectedDate); // Panggil fungsi untuk filter berdasarkan tanggal
                }
            });

            // Fungsi untuk mengirim permintaan AJAX dan memperbarui data tabel berdasarkan tanggal
            function filterByDate(selectedDate) {
                $.ajax({
                    url: 'upload/getHistory.php/', // PHP script untuk mengambil data yang difilter
                    method: 'POST',
                    data: {
                        filterDate: selectedDate
                    }, // Mengirim data filter tanggal
                    success: function (response) {
                        // Ganti isi tbody dengan data yang baru difilter
                        $('#receiptTable tbody').html(response); // Memastikan data yang muncul di tbody
                    },
                    error: function () {
                        alert("Terjadi kesalahan saat mengambil data.");
                    }
                });
            }
        });

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

        // Initialize status button text on page load
        const urlParams = new URLSearchParams(window.location.search);
        const currentStatus = urlParams.get('status');
        setStatusButtonText(currentStatus);

        // Tambahkan event listener untuk tombol logout
        $(document).ready(function() {
            $('.dropdown-item[onclick*="confirmLogout"]').on('click', function(e) {
                confirmLogout(e);
            });
        });
    </script>
</body>

</html>