<?php
session_start();
require_once 'db.php';

// Handle logout
if (isset($_GET['logout'])) {
    logout(); // Menggunakan fungsi logout dari db.php
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE user_id = ? ORDER BY tanggal_submit DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $submissions = [];
}

// Simpan nama pengguna dalam variable
$full_name = $_SESSION['full_name'] ?? 'User';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - MBKM UPJ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        
   body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        .header {
            background-color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
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
        .header .user-info span {
            font-weight: 500;
        }
        .user-info {
    position: relative;
    cursor: pointer;
}

.profile-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 4px;
    min-width: 200px;
    z-index: 1000;
}

.profile-dropdown.show {
    display: block;
}

.profile-dropdown a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.3s;
}

.profile-dropdown a:hover {
    background-color: #f5f5f5;
}

.profile-dropdown i {
    margin-right: 10px;
    color: rgb(62, 65, 255);
    width: 20px;
    text-align: center;
}

.divider {
    height: 1px;
    background-color: #eee;
    margin: 5px 0;
}

.user-info img:hover {
    opacity: 0.8;
    transition: opacity 0.3s;
}
        .navbar {
            background-color:rgb(62, 65, 255);
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .navbar a {
            text-decoration: none;
            color: #fff;
            margin: 0 10px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .content {
            padding: 30px;
        }
        .welcome {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .welcome img {
            height: 120px;
            margin-right: 30px;
        }
        .welcome .text {
            flex: 1;
        }
        .welcome .text h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .welcome .text p {
            margin: 10px 0 0;
            color: #666;
        }
        .welcome .navigation {
            display: flex;
            align-items: center;
        }
        .welcome .navigation a {
            text-decoration: none;
            color::rgb(62, 65, 255);
            margin: 0 10px;
        }
        .programs {
            margin-bottom: 30px;
        }
        .programs h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .programs .program-list {
            display: flex;
            overflow-x: auto;
        }
        .programs .program-item {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-right: 20px;
            flex: 0 0 220px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .programs .program-item img {
            height: 120px;
            margin-bottom: 10px;
        }
        .programs .program-item h3 {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
        }
        .registration {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .registration img {
            height: 120px;
            margin-right: 30px;
        }
        .registration .text {
            flex: 1;
        }
        .registration .text p {
            margin: 0;
            color: #666;
        }
        .registration .text a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color:rgb(62, 65, 255);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .footer {
            background-color: #fff;
            padding: 15px 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
        }
        .footer a {
            text-decoration: none;
            color:rgb(62, 65, 255);
        }
        .footer a:hover {
            text-decoration: underline;
        }
        
        .program-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .program-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .program-item a {
            text-decoration: none;
            color: inherit;
        }

        .program-item img {
            transition: transform 0.3s ease;
        }

        .program-item:hover img {
            transform: scale(1.1);
        }

        .program-item h3 {
            transition: color 0.3s ease;
        }

        .program-item:hover h3 {
            color:rgb(62, 65, 255);
        }

        .registration .text a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color:rgb(62, 65, 255);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .registration .text a:hover {
            background-color:rgb(0, 18, 179);
            transform: translateY(-5px);
        }
        </style>
</head>
<body>
<div class="header">
    <img alt="Universitas Pembangunan Jaya Logo" height="50" src="assets/logo-upj.png" width="120"/>
    <!-- <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
    </div> -->
    <div class="user-info" onclick="toggleProfileMenu()">
        <!-- <img alt="User Profile Picture" height="40" src="<?php echo isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'assets/default-profile.png'; ?>" width="40"/> -->
        <span><?php echo htmlspecialchars($full_name); ?></span>
        <div class="profile-dropdown" id="profileDropdown">
            <!-- <a href="edit-profile.php">
                <i class="fas fa-user-edit"></i>
                Edit Profil
            </a>
            <div class="divider"></div> -->
            <a href="?logout=true">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
</div>  
    
    <div class="navbar">
        <div class="breadcrumb">
            <a href="dashboard.php">Home</a>
            <a href="form-terisi.php">Program Saya</a>
        </div>
        
        <!-- <a href="?logout=true">Logout</a> -->
    </div>

    <div class="content">
        <div class="welcome">
            <img alt="Welcome Illustration" height="120" src="assets/waving-hand.png" width="120"/>
            <div class="text">
                <h2>Halo, <?php echo htmlspecialchars($full_name); ?>!</h2>
                <p>Didalam platform ini, kamu bisa mendapatkan informasi seputar program Merdeka Belajar Kampus Merdeka (MBKM) di Universitas Pembangunan Jaya.</p>
            </div>
            <div class="navigation">
                <a href="#">Sebelumnya</a>
                <a href="#">Selanjutnya</a>
            </div>
        </div>

   <div class="programs">
    <h2>
     Program
     <span style="color:rgb(62, 65, 255);">
      Kami
     </span>
    </h2>
    <div class="program-list">


<div class="program-item">
  <a href="formmagang.php" target="_blank">
    <img alt="Praktek bekerja" height="120" src="assets/working-time.png" width="120"/>
    <h3>
      Magang
    </h3>
  </a>
</div>


    </div>
   </div>

    <div class="registration">
    <img alt="Daftar MBKM" height="120" src="assets/register.png" width="120"/>
        <div class="text">
            <p>
            Daftarkan diri kamu sekarang juga dan temukan pengalaman belajar yang menarik di program MBKM Universitas Pembangunan Jaya.
            </p>
            <a href="formmagang.php" target="_blank">
            Daftar Sekarang
            </a>
        </div>
    </div>

   </div>
    <div class="footer">
        <p>Copyright © 2025 - Ujian Akhir Semester Kelompok 6 PIBS Pembuatan Form MBKM - SIF207 - <a href="https://www.sif.upj.ac.id/">Sistem Informasi Universitas Pembangunan Jaya</a> .</p>
    </div>
</body>
<script>
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('show');
}

// Close the dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.closest('.user-info')) {
        const dropdowns = document.getElementsByClassName('profile-dropdown');
        for (let dropdown of dropdowns) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    }
}
</script>
</html>