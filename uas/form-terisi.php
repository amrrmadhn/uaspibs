<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $nama_depan = $_POST['nama_depan'] ?? '';
    $nama_belakang = $_POST['nama_belakang'] ?? '';
    $full_name = $nama_depan . ' ' . $nama_belakang;
    $nim = $_POST['nim'] ?? '';

    // Handle file upload
    $lampiran = '';
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
        $allowed = array('pdf', 'jpg', 'jpeg', 'png');
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
        }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO submissions (
                user_id, full_name, nim, periode_mulai, periode_seleksi,
                klasifikasi_kegiatan, bidang, perusahaan_tujuan, no_telp_perusahaan,
                email_perusahaan, alamat_perusahaan, jobdesk, nama_pembimbing,
                jabatan_pembimbing, email_pembimbing, no_telp_pembimbing,
                dosen_pembimbing, periode_konversi, tujuan_matakuliah, lampiran
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt->execute([
            $user_id, $full_name, $nim,
            $_POST['periode-mulai'] ?? '',
            $_POST['periode-seleksi'] ?? '',
            $_POST['klasifikasi-kegiatan'] ?? '',
            $_POST['bidang'] ?? '',
            $_POST['perusahaan-tujuan'] ?? '',
            $_POST['no-telp-perusahaan'] ?? '',
            $_POST['email-perusahaan'] ?? '',
            $_POST['alamat-perusahaan'] ?? '',
            $_POST['jobdesk'] ?? '',
            $_POST['nama-pembimbing'] ?? '',
            $_POST['jabatan-pembimbing'] ?? '',
            $_POST['email-pembimbing'] ?? '',
            $_POST['no-telp-pembimbing'] ?? '',
            $_POST['dosen-pembimbing'] ?? '',
            $_POST['periode-konversi'] ?? '',
            $_POST['tujuan-matakuliah'] ?? '',
            $lampiran
        ]);

        // Fetch submissions for display
        $stmt = $pdo->prepare("
            SELECT * FROM submissions 
            WHERE user_id = ? 
            ORDER BY tanggal_submit DESC
        ");
        $stmt->execute([$user_id]);
        $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        die("Terjadi kesalahan dalam menyimpan data.");
    }
}

// Always fetch submissions for display
try {
    $stmt = $pdo->prepare("
        SELECT * FROM submissions 
        WHERE user_id = ? 
        ORDER BY tanggal_submit DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $submissions = [];
}

        // Handle submission deletion
if (isset($_POST['delete_submission'])) {
    $submission_id = $_POST['submission_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Only allow deletion of own submissions
        $stmt = $pdo->prepare("DELETE FROM submissions WHERE id = ? AND user_id = ?");
        $stmt->execute([$submission_id, $user_id]);
        $_SESSION['success'] = 'Submission deleted successfully';
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['error'] = 'Error deleting submission';
    }
    header("Location: form-terisi.php");
    exit();
}

// Fetch submissions for current user
try {
    $stmt = $pdo->prepare("
        SELECT * FROM submissions 
        WHERE user_id = ? 
        ORDER BY tanggal_submit DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $submissions = [];
}


// Rest of your HTML code remains the same, but use $submissions array instead of session data
?>

<!DOCTYPE html>
<html>
<head>
    <title>Status Pengajuan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #007bff;
        }
        .breadcrumb a {
            text-decoration: none;
            color: #007bff;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .breadcrumb i {
            margin-right: 5px;
        }
        .breadcrumb span {
            margin: 0 5px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 500;
            color: #333;
        }
        .header .right {
            font-size: 14px;
            color: #6c757d;
        }
        .card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .card h2 {
            font-size: 18px;
            font-weight: 500;
            color: #333;
            margin-bottom: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        table th {
            background-color: #343a40;
            color: #fff;
            font-weight: 500;
        }
        table td {
            background-color: #fff;
            color: #333;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-submitted { background: #ffc107; color: #000; }
        .status-process { background: #17a2b8; color: #fff; }
        .status-approved { background: #28a745; color: #fff; }
        .status-rejected { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <i class="fas fa-home"></i>
            <a href="dashboard.php">Home</a>
            <span>&gt;</span>
            <a href="#">Program Saya</a>
        </div>
        <div class="header">
            <h1>List Program Saya</h1>
            <div class="right">
                UPJ MBKM &gt; Program Saya
            </div>
        </div>
        <div class="card">
            <h2>Approval Kegiatan</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Program MBKM</th>
                            <th>Tahun Akademik</th>
                            <th>Periode Mulai</th>
                            <th>Status</th>
                            <th>Tanggal Update</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($submissions)): ?>
                            <?php foreach ($submissions as $key => $data): ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= htmlspecialchars($data['nim'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($data['klasifikasi_kegiatan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($data['periode_konversi'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($data['periode_mulai'] ?? '-') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($data['status'] ?? 'submitted') ?>">
                                            <?= htmlspecialchars(ucfirst($data['status'] ?? 'Submitted')) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($data['updated_at'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($data['note'] ?? '-') ?></td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                                            <input type="hidden" name="submission_id" value="<?= $data['id'] ?>">
                                            <button type="submit" name="delete_submission" class="btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No data available in table</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

