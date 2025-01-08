<?php
session_start();
include 'db.php'; // Pastikan file db.php sudah benar dan terhubung

// Pastikan mahasiswa sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Ambil ID pengguna yang sudah login
$user_id = $_SESSION['user_id'];

// Ambil status pendaftaran magang dari database
try {
    // Query untuk mendapatkan status pendaftaran magang
    $stmt = $pdo->prepare("SELECT submitted, status FROM magang WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $submitted = $result['submitted'];  // Status apakah form sudah disubmit
        $status = $result['status'];        // Status pendaftaran (Pending, Approved, Rejected)
    } else {
        $submitted = false;
        $status = ''; // Jika tidak ada data, statusnya kosong
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran Magang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
            width: 50%;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .status {
            font-weight: bold;
            font-size: 20px;
            color: #0033a0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Status Pendaftaran Magang</h1>

        <?php
        if (!$submitted) {
            // Jika form belum disubmit
            echo "<p>Kamu belum submit form.</p>";
        } else {
            // Jika form sudah disubmit, tampilkan status pendaftaran
            echo "<p>Status Pendaftaran Magang: <span class='status'>$status</span></p>";
        }
        ?>

    </div>
</body>
</html>
