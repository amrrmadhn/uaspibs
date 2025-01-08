<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['nip_nim_email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = "Semua kolom harus diisi!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT user_id, full_name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session data using correct column names
                $_SESSION['user_id'] = $user['user_id'];  // Changed from 'id' to 'user_id'
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                
                // Debug: Print session data
                echo "Session data setelah login:<br>";
                var_dump($_SESSION);
                
                redirect('dashboard.php');
            } else {
                $error_message = "Email atau Password salah!";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = "Terjadi kesalahan sistem.";
        }
    }
}
?>

<html>
 <head>
  <title>MBKM UPJ | Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
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
        display: flex;
        width: 100%;
        height: 100%;
        max-width: 1920px;
        max-height: 1080px;
        background-color: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 0px; /* Mengubah border-radius menjadi 0 untuk sudut lancip */
        overflow: hidden;
    }
    .left {
        background-color: #0033a0;
        color: white;
        padding: 40px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .left img {
        width: 80%;
        margin-bottom: 20px;
    }
    .left h1 {
        font-size: 24px;
        margin-bottom: 10px;
    }
    .left p {
        font-size: 16px;
    }
    .right {
        padding: 400px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .right h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }
    .right p {
        font-size: 16px;
        margin-bottom: 20px;
    }
    .right input[type="text"],
    .right input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 0px; /* Mengubah border-radius menjadi 0 untuk sudut lancip */
    }
    .right input[type="checkbox"] {
        margin-right: 10px;
    }
    .right .login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .right .login-options a {
        color::rgb(62, 65, 255);
        text-decoration: none;
    }
    .right .login-options a:hover {
        text-decoration: underline;
    }
    .right button {
        width: 100%;
        padding: 10px;
        background-color:rgb(62, 65, 255);
        color: white;
        border: none;
        border-radius: 0px; /* Mengubah border-radius menjadi 0 untuk sudut lancip */
        font-size: 16px;
        cursor: pointer;
        margin-bottom: 20px;
    }
    .right button:hover {
        background-color:rgb(31, 33, 202);
    }
    .right .signup {
        text-align: center;
    }
    .right .signup a {
        color: #0033a0;
        text-decoration: none;
    }
    .right .signup a:hover {
        text-decoration: underline;
        
    }
  </style>
 </head>
 <body>
  <div class="container">
   <!-- <div class="left">
    <img alt="Kampus Merdeka Logo" height="100" src="" width="200"/>
    <h1>Merdeka Belajar - Kampus Merdeka</h1>
    <p>Universitas Pembangunan Jaya</p>
   </div> -->

   <div class="right">
    <h2>Selamat Datang!</h2>
    <p>Silahkan login agar dapat masuk ke dalam Sistem MBKM</p>
    <form method="POST">
        <input placeholder="Email" type="text" name="nip_nim_email" required />
        <input placeholder="Password" type="password" name="password" required />
        <div class="login-options">
            <div>
                <input id="remember-me" type="checkbox" name="remember-me" />
                <label for="remember-me">Remember me</label>
            </div>
            <a href="#">Forgot Password?</a>
        </div>
        <button type="submit" name="login">Login</button>
    </form>
    <?php 
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    } 
    ?>
    <div class="signup">
     <p>Belum punya akun? <a href="signup.php">Sign Up</a></p>
     <p>Kembali ke Beranda? <a href="index.html">Home</a></p>
    </div>
   </div>
  </div>
 </body>
</html>
