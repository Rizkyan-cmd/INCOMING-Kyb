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

$sql = "SELECT ic.*, ng.ng_desc, ng.deskripsi  
        FROM incoming_check ic
        LEFT JOIN incoming_ng ng ON ic.ng_kategori = ng.id
        WHERE MONTH(ic.check_dt) = ? AND YEAR(ic.check_dt) = ?";
$stmt = $connwarehouse->prepare($sql);
$stmt->bind_param("ii", $currentMonth, $currentYear); // Bind bulan dan tahun
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query gagal: " . $connwarehouse->error); // Tampilkan error query jika ada
}
$connwarehouse->close();
$currUrl = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Kayaba - Incoming</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="public/img/kyb-icon.ico" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="public/library/DataTables/datatables.min.js" rel="stylesheet">
    <link rel="stylesheet" href="public/library/sweetalert2/dist/sweetalert2.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="public/img/kayaba-logo.png" alt="Logo" class="me-3" style="width: 125px; height: auto;">
        </div>
        <div class="d-flex justify-content-center flex-grow-1">
            <nav class="d-flex align-items-center">
                <!-- Link Beranda - Selalu tampil untuk semua golongan -->
                <a href="dashboard.php"
                    class="nav-link mx-2 <?php echo strpos($currUrl, 'dashboard') !== false ? 'text-danger' : ''; ?>"
                    id="dashboard-link">
                    <span>Beranda</span>
                </a>

                <?php if ($_SESSION['golongan'] == '0' || $_SESSION['golongan'] == '1' || $_SESSION['golongan'] == '2' || $_SESSION['golongan'] == '3') { ?>
                    <!-- Golongan 0, 1, 2, dan 3 menampilkan Tables Receipt -->
                    <div class="nav-link dropdown mx-2">
                        <a href="#" class="nav-link <?php echo strpos($currUrl, 'Viewjudge') !== false ? 'text-danger' : ''; ?>"
                            data-bs-toggle="dropdown" role="button">
                            <span>Tables Receipt</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="Viewjudge.php" class="dropdown-item" id="tables-receipt-link">Data Receipt</a></li>
                            <li><a href="HistoryInspeksi.php" class="dropdown-item" id="tables-receipt-link">History Inspeksi</a></li>
                        </ul>
                    </div>
                <?php } ?>
                <!-- Golongan 4 hanya melihat Beranda, tidak perlu tambahan menu -->
            </nav>
        </div>
        <div class="row">
            <div class="d-flex justify-content-end w-100">
                <div class="dropdown">
                    <span class="dropdown-toggle me-2" id="userDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false" style="cursor: pointer;">
                        Hallo, <strong>
                            <?php
                            // Ambil nama depan saja
                            $firstName = explode(' ', $_SESSION['full_name'])[0];
                            echo htmlspecialchars($firstName);
                            ?>!
                        </strong>
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
        <div class="d-flex justify-content-center align-items-center mb-4">
            <div class="row w-100">
                <!-- Chart kedua di kanan -->
                <div class="col-md-6" style="max-width: 850px;">
                    <div class="card mb-4" style="height: 400px;">
                        <div class="card-body">
                            <h6 class="card-title text-center mb-3">Status Pengecekan</h6>
                            <div class="mb-3" style="height: 38px;">
                            </div>
                            <!-- <div class="chart-container position-relative" style="height: 250px;"> -->
                            <div class="chart-container position-relative"
                                style="height: 250px; width: 500px; float: left;">
                                <canvas id="additionalPieChart" style="max-width: 100%; max-height: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Chart pertama di kiri -->
                <div class="col-md-6" style="max-width: 850px;">
                    <div class="card mb-4" style="height: 400px;">
                        <div class="card-body">
                            <h6 class="card-title text-center mb-3">NG Ratio Periode</h6>
                            <div class="mb-3">
                                <input type="date" id="filterDate" class="form-control" />
                            </div>
                            <div class="chart-container position-relative"
                                style="height: 250px; width: 450px; float: left;">
                                <canvas id="pieChart" style="max-width: 100%; max-height: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="text-center mb-4"></h5>
                <div class="row">
                    <div class="col-md-12 mb-2">
                    <input type="date" id="filterDateHistory" class="form-control" />
                    </div>
                </div>
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
                        <tbody>
                            <?php
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
                                        echo "<td><img src='public/img/" . $imagePath . "' alt='Evidence' style='width: 100px; height: auto; cursor: pointer;' data-bs-toggle='modal' data-bs-target='#imageModal' onclick=\"showImage('public/img/$imagePath')\"></td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                    echo "<td>" . htmlspecialchars(date("d-m-Y", strtotime($row['check_dt']))) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['check_usr']) . "</td>";
                                    echo "<td><span class='btn " . ($row['ng'] == 0 ? "btn-success" : "btn-danger") . " disabled'>" . ($row['ng'] == 0 ? 'OK' : 'NG') . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='14' class='text-center'>Tidak ada data ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="public/library/jquery/dist/jquery.min.js"></script>
    <script src="public/library/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="public/library/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="assets/js/main.js"></script>

    <script>
      $(document).ready(function() {
        // Ketika pengguna memilih tanggal
        $('#filterDateHistory').change(function() {
            var selectedDate = $(this).val(); // Mendapatkan tanggal yang dipilih
            if (selectedDate) {
                filterByDate(selectedDate); // Panggil fungsi untuk filter berdasarkan tanggal
            }
        });

        // Fungsi untuk mengirim permintaan AJAX dan memperbarui data tabel berdasarkan tanggal
        function filterByDate(selectedDate) {
            console.log(selectedDate);
            $.ajax({
                url: 'upload/getHistory.php/', // PHP script untuk mengambil data yang difilter
                method: 'POST',
                data: {
                    filterDate: selectedDate
                }, // Mengirim data filter tanggal
                success: function(response) {
                    // Ganti isi tbody dengan data yang baru difilter
                    $('#receiptTable tbody').html(response); // Memastikan data yang muncul di tbody
                },
                error: function() {
                    alert("Terjadi kesalahan saat mengambil data.");
                }
            });
        }
    });

    function showImage(imagePath) {
        document.getElementById('modalImage').src = imagePath;
    }

    document.addEventListener('DOMContentLoaded', () => {
    const ctxAdditionalPieChart = document.getElementById('additionalPieChart').getContext('2d');

    // Fungsi untuk fetch data dari server
    fetch('upload/UploadStatusPengecekan.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error(data.error); // Jika ada error dari server
            return;
        }

        // Hitung total dari seluruh no_part
        const totalParts = data.values.reduce((acc, val) => acc + val, 0);

        // Buat chart
        new Chart(ctxAdditionalPieChart, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: ['#FFFF00', '#00ff00'], // Warna chart
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right', // Posisikan label di kanan chart
                        labels: {
                            generateLabels: (chart) => {
                                // Tambahkan total di akhir label legend
                                return chart.data.labels.map((label, i) => ({
                                    text: `${label}: ${chart.data.datasets[0].data[i]}`,
                                    fillStyle: chart.data.datasets[0].backgroundColor[i],
                                    hidden: false,
                                    index: i,
                                })).concat([
                                    {
                                        text: `Total Part :  ${totalParts}`, // Tambahkan total di sini
                                        fillStyle: '#ADD8E6', // Hilangkan warna untuk total
                                        hidden: false,
                                    }
                                ]);
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = ((value / total) * 100).toFixed(2); // Hitung persentase
                            return `${value} (${percentage}%)`; // Tampilkan angka dan persentase
                        },
                        color: '#000',
                        font: {
                            weight: 'bold',
                        },
                    }
                }
            },
            plugins: [ChartDataLabels] // Aktifkan plugin datalabels
        });
    })
    .catch(error => console.error('Error fetching data:', error));
    });

    document.addEventListener('DOMContentLoaded', function() {
    // Ambil elemen canvas dengan id 'pieChart'
    const ctx = document.getElementById('pieChart').getContext('2d');
    const filterDate = document.getElementById('filterDate'); // Pastikan elemen ini ada di HTML
    let pieChart = null; // Variabel untuk menyimpan instance chart

    // Fungsi untuk mengambil data berdasarkan tanggal yang dipilih
    function fetchData(chosenDate) {
        // Pastikan tanggal yang dipilih valid
        if (!chosenDate) {
            console.error('Tanggal tidak valid');
            return;
        }

        // Ambil data dari server (misalnya API)
        fetch(`upload/pieChart.php?date=${chosenDate}`)
            .then(response => response.json())
            .then(data => {
                console.log(data); // Menampilkan data di konsol untuk debugging
                const totalNg = data.ng; // Total NG
                const totalOk = data.ok; // Total OK

                const total = totalNg + totalOk;
                const ngPercentage = (totalNg / total) * 100;
                const okPercentage = (totalOk / total) * 100;

                // Jika chart sudah ada, hancurkan chart lama
                if (pieChart) {
                    pieChart.destroy();
                }

                // Buat pie chart baru
                pieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['NG', 'OK'],
                        datasets: [{
                            label: 'NG dan OK',
                            data: [totalNg, totalOk],
                            backgroundColor: ['#FF0000', '#00ff00'],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const value = tooltipItem.raw;
                                        const label = tooltipItem.label;
                                        let percentage = 0;

                                        if (label === 'NG') {
                                            percentage = ngPercentage;
                                        } else if (label === 'OK') {
                                            percentage = okPercentage;
                                        }

                                        return label + ': ' + value + ' (' + percentage
                                            .toFixed(2) + '%)';
                                    }
                                }
                            },
                            datalabels: {
                                formatter: function(value, ctx) {
                                    const percentage = value / total * 100;
                                    return value + ' (' + percentage.toFixed(2) + '%)';
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                padding: 10,
                                align: 'center',
                                anchor: 'center',
                                offset: 10
                            }
                        }
                    },
                    plugins: [
                        ChartDataLabels
                    ] // Pastikan plugin datalabels sudah di-import dan ditambahkan
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Set tanggal default ke hari ini pada elemen filterDate
    const today = new Date().toISOString().split('T')[0]; // Mengambil tanggal hari ini
    filterDate.value = today; // Mengatur nilai input tanggal ke hari ini

    // Event listener untuk memilih tanggal
    filterDate.addEventListener('change', function() {
        const chosenDate = filterDate.value;
        if (chosenDate) {
            fetchData(chosenDate);
        }
    });

    // Ambil data untuk tanggal default (hari ini)
    fetchData(today);
});

    function confirmLogout() {
        Swal.fire({
            title: 'Anda yakin ingin logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php'; // Ganti dengan URL logout Anda jika diperlukan
            }
        });
    }
    $(document).ready(function() {
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
    </script>

</body>

</html>