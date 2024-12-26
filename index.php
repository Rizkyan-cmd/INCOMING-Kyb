<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'config.php';

$show_otp_modal = false;
if (isset($_POST['login'])) {
    $npk = $_POST['npk'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    // Periksa CAPTCHA
    if ($captcha === $_SESSION['captcha']) {
        // Mempersiapkan statement untuk mencari NPK di database
        $stmt = $connlembur->prepare("SELECT * FROM ct_users_hash WHERE npk = ?");

        // Periksa jika query gagal disiapkan
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($connlembur->error));
        }

        $stmt->bind_param("s", $npk);

        // Eksekusi statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Menggunakan password_verify untuk memeriksa password yang di-hash
            if (password_verify($password, $row['pwd'])) {
                // Menghasilkan OTP acak
                $otp = rand(100000, 999999); // Menghasilkan OTP 6 digit
                $expiry_date = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Menentukan masa berlaku OTP

                // Ambil nomor telepon dari database
                $phone_number = "6289502233411"; // Nomor telepon tetap (atau ambil dari DB)

                // Cek apakah OTP sudah ada di database
                $stmt_otp_check = $connwarehouse->prepare("SELECT * FROM otp_authentic WHERE npk = ? AND phone_number = ?");

                if (!$stmt_otp_check) {
                    die("Error preparing statement: " . $connwarehouse->error);
                }

                $stmt_otp_check->bind_param("ss", $npk, $phone_number);
                $stmt_otp_check->execute();
                $otp_result = $stmt_otp_check->get_result();

                if ($otp_result->num_rows > 0) {
                    // Jika OTP sudah ada, perbarui OTP yang ada dan set use = 2, use_date = NULL
                    $stmt_otp_update = $connwarehouse->prepare("UPDATE otp_authentic SET otp = ?, expiry_date = ?, send = 2, `use` = 2, use_date = NULL WHERE npk = ? AND phone_number = ?");

                    if (!$stmt_otp_update) {
                        die("Error preparing update statement: " . $connwarehouse->error);
                    }

                    $stmt_otp_update->bind_param("ssss", $otp, $expiry_date, $npk, $phone_number);
                    $stmt_otp_update->execute();

                    if ($stmt_otp_update->error) {
                        die("Error executing update statement: " . $stmt_otp_update->error);
                    }

                    $stmt_otp_update->close();
                } else {
                    // Jika OTP belum ada, simpan OTP baru dan set use = 2, use_date = NULL
                    $stmt_otp = $connwarehouse->prepare("INSERT INTO otp_authentic (npk, phone_number, otp, expiry_date, send, `use`, use_date) VALUES (?, ?, ?, ?, ?, 2, NULL)");

                    if (!$stmt_otp) {
                        die("Error preparing statement: " . $connwarehouse->error);
                    }

                    $send = 2; // Menandai OTP sebagai telah dikirim
                    $stmt_otp->bind_param("sssss", $npk, $phone_number, $otp, $expiry_date, $send);
                    $stmt_otp->execute();

                    if ($stmt_otp->error) {
                        die("Error executing statement: " . $stmt_otp->error);
                    }

                    $stmt_otp->close();
                }

                // Set show_otp_modal untuk memunculkan modal OTP
                $show_otp_modal = true;
            } else {
                echo "<div class='alert alert-danger'>Password salah.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>NPK tidak ditemukan.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Captcha salah.</div>";
    }
}

// Menggunakan OTP (ubah `use` menjadi 1 dan set use_date saat OTP digunakan)
if (isset($_POST['otp'])) {
    $entered_otp = $_POST['otp'];
    $npk = $_POST['npk']; // NPK yang sama dengan saat login
    $phone_number = "6289502233411"; // Nomor telepon tetap (atau ambil dari DB)

    // Periksa apakah OTP yang dimasukkan valid
    $stmt_otp_verify = $connwarehouse->prepare("SELECT * FROM otp_authentic WHERE npk = ? AND otp = ? AND 'expiry_date' > NOW() AND `use` = 2");
 
    if (!$stmt_otp_verify) {
        die("Error preparing statement: " . $connwarehouse->error);
    }

    $stmt_otp_verify->bind_param("ss", $npk, $entered_otp);
    $stmt_otp_verify->execute();
    $otp_verify_result = $stmt_otp_verify->get_result();

    if ($otp_verify_result->num_rows > 0) {
        // Jika OTP valid, update kolom `use` menjadi 1 dan set use_date ke tanggal dan jam sekarang
        $stmt_otp_use = $connwarehouse->prepare("UPDATE otp_authentic SET `use` = 1, use_date = NOW() WHERE id =?");

        if (!$stmt_otp_use) {
            die("Error preparing update statement: " . $connwarehouse->error);
        }
        $current_datetime = date('Y-m-d H:i:s');
        $stmt_otp_use->bind_param("ss", $npk, $entered_otp);
        $stmt_otp_use->execute();

        if ($stmt_otp_use->error) {
            die("Error executing statement: " . $stmt_otp_use->error);
        }

        $stmt_otp_use->close();
        echo "<div class='alert alert-success'>OTP valid dan digunakan.</div>";
    } else {
        echo "<div class='alert alert-danger'>OTP tidak valid atau telah kedaluwarsa.</div>";
    }

    $stmt_otp_verify->close();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INCOMING - KAYABA</title>
    <link rel="stylesheet" href="public/bootstrap/css/bootstrap.min.css">
    <link href="public/img/kyb-icon.ico" rel="icon">
    <link href="public/library/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="public/library/sweetalert2/dist/sweetalert2.all.min.js">
    <link href="assets/css/login.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <section id="sect-form" class="col-lg-5 col-md-8 col-sm-10 m-auto">
            <div class="card">
                <div class="card-body p-4">
                    <div class="logo-container mb-4">
                        <img src="public/img/kayaba-logo.png" alt="logo kayaba">
                    </div>
                    <form class="row g-3" method="POST" id="loginForm">
                        <div class="col-12 mb-3">
                            <label for="npk" class="form-label">NPK<span class="text-danger"> *</span></label>
                            <div class="input-group">
                                <input type="text" name="npk" class="form-control" id="npk" placeholder="Masukan NPK "
                                    required maxlength="8" autocomplete="off">
                                <span class="input-group-text"><i class="bi-person-fill"></i></span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password" class="form-label">Password<span class="text-danger"> *</span></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Masukan Password" required maxlength="50">
                                <span class="input-group-text" id="togglePassword"><i class="bi-eye-fill"></i></span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="captcha.php" alt="Captcha Code" id="captchaImage" class="me-2"
                                    style="height: 40px;">
                                <input type="text" class="form-control" id="captcha" name="captcha"
                                    placeholder="Masukan Captcha" maxlength="6" required>
                            </div>
                            <small class="form-text text-muted">
                                Captcha tidak terbaca? <a href="javascript:void(0);"
                                    onclick="refreshCaptcha()">Ulangi</a>
                            </small>
                        </div>
                        <div class="col-12">
                            <button type="submit" id="login" name="login" value="login"
                                class="btn btn-dark w-100">Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal OTP -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel">Masukkan Kode OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="otpForm">
                        <div class="mb-3">
                            <label class="form-label">Ketik kode verifikasi yang dikirim ke nomor WhatsApp
                                Anda:<strong>+628******2473</strong></label>
                            <div class="otp-container mt-3">
                                <input type="text" class="form-control otp-field" maxlength="1" required>
                                <input type="text" class="form-control otp-field" maxlength="1" required>
                                <input type="text" class="form-control otp-field" maxlength="1" required>
                                <input type="text" class="form-control otp-field" maxlength="1" required>
                                <input type="text" class="form-control otp-field" maxlength="1" required>
                                <input type="text" class="form-control otp-field" maxlength="1" required>
                            </div>
                            <input type="hidden" name="otp" id="otp">
                            <div id="otpError" class="text-danger mt-2" style="display: none;">Kode OTP harus terdiri
                                dari 6 digit.</div>
                        </div>
                        <div id="otpTimer" class="mb-3">Kode OTP akan kedaluwarsa dalam <span id="timer">300</span>
                            detik</div>
                        <div id="resendOtp" class="mt-2" style="display: none;">
                            <button type="button" class="btn btn-secondary" id="resendOtpButton">Kirim ulang Kode
                                OTP</button>
                        </div>
                        <input type="hidden" name="npk" id="npkOtp" value="<?php echo isset($npk) ? $npk : ''; ?>">
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-dark">Masuk</button>
                        </div>
                        <div id="otpMessage" class="mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="public/library/jquery/dist/jquery.min.js"></script>
    <script src="public/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="public/library/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script>
        
    let timer;
    let countdown = 300;

    function startTimer() {
        document.getElementById("timer").innerText = countdown;
        timer = setInterval(() => {
            countdown--;
            document.getElementById("timer").innerText = countdown;

            if (countdown <= 0) {
                clearInterval(timer);
                document.getElementById("resendOtp").style.display = "block";
                document.getElementById("otpTimer").style.display = "none";
            }
        }, 1000);
    }

    document.querySelectorAll('.otp-field').forEach(function(input, index) {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value && index < 5) {
                document.querySelectorAll('.otp-field')[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                document.querySelectorAll('.otp-field')[index - 1].focus();
            }
        });
    });

    document.getElementById("otpForm").onsubmit = function(e) {
        e.preventDefault();
        let otpValue = '';
        document.querySelectorAll('.otp-field').forEach(function(input) {
            otpValue += input.value;
        });
        document.getElementById('otp').value = otpValue;

        if (!/^\d{6}$/.test(otpValue)) {
            document.getElementById("otpError").style.display = "block";
            return;
        } else {
            document.getElementById("otpError").style.display = "none";
        }

        // Add OTP verification code here
    };

    document.getElementById("resendOtpButton").onclick = function() {
        // Add code to resend OTP here
        countdown = 300;
        document.getElementById("resendOtp").style.display = "none";
        document.getElementById("otpTimer").style.display = "block";
        startTimer();
        // Kirimkan permintaan AJAX untuk mendapatkan OTP baru
        $.ajax({
            url: 'resend_otp.php', // URL untuk mengirimkan permintaan OTP baru
            type: 'POST',
            data: {
                npk: document.getElementById('npkOtp').value, // Kirimkan NPK dari form
            },
            success: function(response) {
                // Pastikan response berisi status yang diinginkan
                if (response.success) {
                    // OTP berhasil dikirim
                    document.getElementById("otpMessage").innerHTML =
                        "Kode OTP yang baru telah dikirim, tolong periksa kembali.";
                    document.getElementById("otpMessage").style.color = "green";
                } else {
                    // Cek jika status bukan success, berikan pesan yang sesuai
                    document.getElementById("otpMessage").innerHTML = "Terjadi kesalahan: " + response
                        .message;
                    document.getElementById("otpMessage").style.color = "red";
                }
            },
            error: function(xhr, status, error) {
                // Menangani jika request gagal atau ada kesalahan server
                if (xhr.status === 500) {
                    document.getElementById("otpMessage").innerHTML =
                        "Terjadi kesalahan pada server. Silakan coba lagi nanti.";
                } else {
                    document.getElementById("otpMessage").innerHTML =
                        "Terjadi kesalahan. Silakan coba lagi.";
                }
                document.getElementById("otpMessage").style.color = "red";
            }
        });
    };

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function(e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    function refreshCaptcha() {
        document.getElementById('captchaImage').src = 'captcha.php?' + Math.random();
    }

    $(document).ready(function() {
    <?php if (isset($show_otp_modal) && $show_otp_modal == true): ?>
    $('#otpModal').modal('show');
    <?php endif; ?>

    $('#otpForm').on('submit', function(e) {
        const npk = $("#npkOtp").val();
        const otp = $("#otp").val();
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'verify_otp.php',
            data: {
                npk: npk,
                otp: otp,
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                        window.location.href = 'dashboard.php';
                   
                } else {
                    // Menampilkan SweetAlert jika OTP salah
                    Swal.fire({
                        icon: 'error',
                        title: 'OTP Salah',
                        text: response.message ||
                            'Kode OTP yang Anda masukkan salah. Silakan coba lagi.',
                    });
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr);
                console.log(textStatus);
                // Menampilkan SweetAlert jika ada kesalahan dalam proses verifikasi
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Terjadi kesalahan dalam proses verifikasi OTP. Silakan coba lagi nanti.',
                });
            }
        });
    });
});

    startTimer();
    </script>
</body>

</html>