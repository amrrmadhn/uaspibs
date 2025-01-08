<?php
session_start();
$full_name = '';
if (isset($_SESSION['full_name'])) {
    $full_name = $_SESSION['full_name'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['nama_depan'] . ' ' . $_POST['nama_belakang'];
}
require_once 'db.php';
checkSession();
checkSessionTimeout();
regenerateSession();

$formToken = generateFormToken();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    try {
        // File upload handling
        $lampiran = '';
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $filename = $_FILES['lampiran']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['lampiran']['tmp_name'], $upload_dir . $new_filename);
                $lampiran = $new_filename;
            } else {
                throw new Exception('File type not allowed');
            }
        } else {
            throw new Exception('No file uploaded');
        }

        $stmt = $pdo->prepare("
            INSERT INTO submissions (
                user_id, full_name, nim, periode_mulai, periode_seleksi,
                klasifikasi_kegiatan, bidang, perusahaan_tujuan, no_telp_perusahaan,
                email_perusahaan, alamat_perusahaan, jobdesk, nama_pembimbing,
                jabatan_pembimbing, email_pembimbing, no_telp_pembimbing,
                dosen_pembimbing, periode_konversi, tujuan_matakuliah, lampiran,
                status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Submitted'
            )
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['nama_depan'] . ' ' . $_POST['nama_belakang'],
            $_POST['nim'],
            $_POST['periode-mulai'],
            $_POST['periode-seleksi'],
            $_POST['klasifikasi-kegiatan'],
            $_POST['bidang'],
            $_POST['perusahaan-tujuan'],
            $_POST['no-telp-perusahaan'],
            $_POST['email-perusahaan'],
            $_POST['alamat-perusahaan'],
            $_POST['jobdesk'],
            $_POST['nama-pembimbing'],
            $_POST['jabatan-pembimbing'],
            $_POST['email-pembimbing'],
            $_POST['no-telp-pembimbing'],
            $_POST['dosen-pembimbing'],
            $_POST['periode-konversi'],
            $_POST['tujuan-matakuliah'],
            $lampiran
        ]);

        header("Location: form-terisi.php");
        exit(); // Penting untuk menghentikan eksekusi script
    } catch (PDOException $e) {
        error_log("Submission error: " . $e->getMessage());
        $error_message = "Terjadi kesalahan dalam menyimpan data.";
    } catch (Exception $e) {
        error_log("Submission error: " . $e->getMessage());
        $error_message = $e->getMessage();
    }
}
?>
<!-- formmagang.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Formulir Pendaftaran</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <!-- Style CSS tetap sama seperti sebelumnya -->
    <style>
        body {
   
            font-family: 'Roboto', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        .header img {
            height: 50px;
        }
        .header .user-info {
            display: flex;
            align-items: center;
        }
        .header .user-info img {
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .breadcrumb {
            font-size: 14px;
            color: #888;
            margin: 10px 0;
        }
        .breadcrumb a {
            color: #888;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .form-title {
            font-size: 18px;
            font-weight: 500;
            margin: 20px 0;
        }
        .form-section {
            margin-bottom: 20px;
        }
        .form-section h3 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .form-group {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .form-group label {
            width: 100%;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group input[type="file"] {
            padding: 3px;
        }
        .form-group-half {
            width: 48%;
            margin-right: 4%;
        }
        .form-group-half:last-child {
            margin-right: 0;
        }
        .form-group-third {
            width: 31%;
            margin-right: 3.5%;
        }
        .form-group-third:last-child {
            margin-right: 0;
        }
        .form-group-full {
            width: 100%;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group .note {
            font-size: 12px;
            color: #888;
        }
        .submit-btn {
            display: flex;
            justify-content: flex-start;
            margin-top: 20px;
        }
        .submit-btn button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-btn button:hover {
            background-color: #0056b3;
        }
        @media (max-width: 768px) {
            .form-group-half,
            .form-group-third {
                width: 100%;
                margin-right: 0;
            }
        }
  
        </style>
    <script>

let inactivityTimer;

function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        window.location.href = '?logout=true';
    }, 300000); // 5 minutes
}

['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(
    event => document.addEventListener(event, resetTimer, false)
);

resetTimer();

        function validateForm(event) {
            let isValid = true;
            const requiredFields = [
                'periode-mulai', 'periode-seleksi', 'klasifikasi-kegiatan', 'bidang', 
                'perusahaan-tujuan', 'no-telp-perusahaan', 'email-perusahaan', 'alamat-perusahaan', 
                'jobdesk', 'nama-pembimbing', 'jabatan-pembimbing', 'email-pembimbing', 
                'no-telp-pembimbing', 'dosen-pembimbing', 'periode-konversi', 'tujuan-matakuliah'
            ];

            requiredFields.forEach(function(field) {
                const input = document.getElementById(field);
                const errorSpan = input.parentElement.querySelector('.error-message');
                
                if (errorSpan) {
                    errorSpan.remove();
                }
                
                if (!input.value.trim()) {
                    isValid = false;
                    const errorMessage = document.createElement('span');
                    errorMessage.className = 'error-message';
                    errorMessage.textContent = 'Field ini harus diisi';
                    errorMessage.style.color = 'red';
                    errorMessage.style.fontSize = '12px';
                    errorMessage.style.display = 'block';
                    errorMessage.style.marginTop = '5px';
                    input.parentElement.appendChild(errorMessage);
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert("Mohon isi semua field yang wajib diisi!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
    

        <div class="header">
            <img alt="Logo" height="50" src="assets/logo-upj.png" width="120"/>
            <div class="user-info">
                <!-- <img alt="User Avatar" height="40" src="https://storage.googleapis.com/a1aa/image/fYKfCB1hBLmrm0LtHH0kTkZZkqVa0LhVAk3EOKe5BLfxFeUgC.jpg" width="40"/> -->
    <span style="padding-right: 10px;"><?php echo htmlspecialchars($full_name); ?></span>
            </div>
        </div>

        <div class="breadcrumb">
            <a href="dashboard.php">Home</a> &gt; Magang Kerja
        </div>

        <div class="form-title">Daftar Magang Kerja</div>

        <form method="POST" action="form-terisi.php" enctype="multipart/form-data" onsubmit="return validateForm(event)">
        <input type="hidden" name="form_token" value="<?php echo $formToken; ?>">
            <div class="form-section">
                <h3>Formulir Pendaftaran</h3>
                <p>Sebelum kamu mendaftar, pastikan kamu sudah membaca syarat dan ketentuan yang berlaku.</p>
                
    <div class="form-group form-group-half">
    <label for="nama_depan">Nama Depan</label>
    <input id="nama_depan" name="nama_depan" type="text" placeholder="Masukkan Nama Depan" required />
    </div>

    <div class="form-group form-group-half">
        <label for="nama_belakang">Nama Belakang</label>
        <input id="nama_belakang" name="nama_belakang" type="text" placeholder="Masukkan Nama Belakang" required />
    </div>

    <div class="form-group form-group-half">
        <label for="nim">NIM</label>
        <input id="nim" name="nim" type="text" placeholder="Masukkan NIM" required />
    </div>

    
    
    <div class="form-group form-group-half">
        <label for="periode-mulai">
            Periode Mulai
        </label>
        <input id="periode-mulai" name="periode-mulai" type="text" placeholder="Contoh: Januari 2025"/>
    </div>

<div class="form-group form-group-half">
    <label for="periode-seleksi">
        Periode Seleksi
    </label>
    <input id="periode-seleksi" name="periode-seleksi" type="text" placeholder="Contoh: Februari 2025"/>
</div>
<div class="form-group form-group-full">
    <label for="lampiran">
        Lampiran
    </label>
    <input id="lampiran" name="lampiran" type="file" placeholder="Pilih file lampiran"/>
    <span class="note">
        Ekstensi diizinkan: pdf, jpg, png, jpeg.
    </span>
</div>
<div class="form-group form-group-half">
    <label for="klasifikasi-kegiatan">
        Klasifikasi Kegiatan
    </label>
    <select id="klasifikasi-kegiatan" name="klasifikasi-kegiatan">
        <option value="">Pilih</option>
        <option value="magang-kerja">Magang Kerja</option>
        <option value="penelitian">Penelitian</option>
        <option value="pengabdian-masyarakat">Pengabdian kepada Masyarakat</option>
        <option value="projek-studi">Projek Studi</option>
        <option value="praktik-industri">Praktik Industri</option>
        <option value="proyek-akhir">Proyek Akhir</option>
        <option value="pelatihan">Pelatihan</option>
        <option value="kegiatan-sosial">Kegiatan Sosial</option>
        <option value="kompetisi">Kompetisi</option>
        <option value="lainnya">Lainnya</option>
    </select>
</div>

<div class="form-group form-group-half">
    <label for="bidang">
        Bidang
    </label>
    <select id="bidang" name="bidang">
        <option value="">Pilih</option>
        <option value="it">Teknologi Informasi</option>
        <option value="keuangan">Keuangan</option>
        <option value="pemasaran">Pemasaran</option>
        <option value="manufaktur">Manufaktur</option>
        <option value="sumber-daya-manusia">Sumber Daya Manusia</option>
        <option value="kesehatan">Kesehatan</option>
        <option value="pendidikan">Pendidikan</option>
        <option value="logistik">Logistik</option>
        <option value="hukum">Hukum</option>
        <option value="lainnya">Lainnya</option>
    </select>
</div>

<div class="form-group form-group-half">
    <label for="perusahaan-tujuan">
        Perusahaan Tujuan
    </label>
    <input id="perusahaan-tujuan" name="perusahaan-tujuan" placeholder="Contoh: PT. Telkom Indonesia" type="text"/>
</div>

<div class="form-group form-group-half">
    <label for="no-telp-perusahaan">
        No. Telp Perusahaan
    </label>
    <input id="no-telp-perusahaan" name="no-telp-perusahaan" placeholder="Contoh: 02112345678" type="text"/>
</div>

<div class="form-group form-group-half">
    <label for="email-perusahaan">
        Email Perusahaan
    </label>
    <input id="email-perusahaan" name="email-perusahaan" placeholder="Contoh: info@pt.co.id" type="email"/>
</div>

<div class="form-group form-group-full">
    <label for="alamat-perusahaan">
        Alamat Perusahaan
    </label>
    <input id="alamat-perusahaan" name="alamat-perusahaan" placeholder="Isi dengan Alamat Lengkap Perusahaan" type="text"/>
</div>

<div class="form-group form-group-full">
    <label for="jobdesk">
        Jobdesk / Tugas
    </label>
    <textarea id="jobdesk" name="jobdesk" placeholder="Deskripsikan tugas yang akan kamu lakukan selama magang." rows="3"></textarea>
</div>

<div class="form-section">
    <h3>
        Informasi Pembimbing Lapangan
    </h3>
    <div class="form-group form-group-half">
        <label for="nama-pembimbing">
            Nama
        </label>
        <input id="nama-pembimbing" name="nama-pembimbing" placeholder="Isi Nama Lengkap Pembimbing Lapangan" type="text"/>
    </div>
    <div class="form-group form-group-half">
        <label for="jabatan-pembimbing">
            Jabatan
        </label>
        <input id="jabatan-pembimbing" name="jabatan-pembimbing" placeholder="Isi dengan Jabatan Pembimbing Lapangan" type="text"/>
    </div>
    <div class="form-group form-group-half">
        <label for="email-pembimbing">
            Email
        </label>
        <input id="email-pembimbing" name="email-pembimbing" placeholder="Contoh: johndoe@example.com" type="email"/>
    </div>
    <div class="form-group form-group-half">
        <label for="no-telp-pembimbing">
            No. Telp
        </label>
        <input id="no-telp-pembimbing" name="no-telp-pembimbing" placeholder="Contoh: 089123456789" type="text"/>
    </div>

    <div class="form-group form-group-half">
    <label for="dosen-pembimbing">
        Dosen Pembimbing
    </label>
    <select id="dosen-pembimbing" name="dosen-pembimbing">
        <option value="">Pilih Dosen Pembimbing</option>
        <option value="dr-andrew">Dr. Andrew</option>
        <option value="dr-maria">Dr. Maria</option>
        <option value="prof-john">Prof. John</option>
        <option value="dr-amelia">Dr. Amelia</option>
        <option value="dr-david">Dr. David</option>
        <option value="dr-susan">Dr. Susan</option>
        <option value="prof-peter">Prof. Peter</option>
        <option value="dr-emily">Dr. Emily</option>
        <option value="dr-michael">Dr. Michael</option>
        <option value="lainnya">Lainnya</option>
    </select>
</div>

<div class="form-group form-group-half">
    <label for="periode-konversi">
        Periode Konversi
    </label>
    <select id="periode-konversi" name="periode-konversi">
        <option value="">Pilih Periode</option>
        <option value="ganjil-2025">Ganjil 2024/2025</option>
        <option value="genap-2025">Genap 2024/2025</option>
        <option value="ganjil-2026">Ganjil 2025/2026</option>
        <option value="genap-2026">Genap 2025/2026</option>
        <option value="ganjil-2027">Ganjil 2026/2027</option>
        <option value="genap-2027">Genap 2026/2027</option>

    </select>
</div>

    <div class="form-group form-group-full">
        <label for="tujuan-matakuliah">
            Tujuan Matakuliah Konversi
        </label>
        <input id="tujuan-matakuliah" name="tujuan-matakuliah" placeholder="Pilih Matakuliah" type="text"/>
    </div>
    </div>
    <div class="submit-btn">
            <button type="submit" name="submit">
                Daftar Sekarang
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>
</body>
</html>