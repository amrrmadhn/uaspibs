<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $full_name = trim($first_name . ' ' . $last_name);
    $nip_nim_email = $_POST['nip_nim_email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($first_name) || empty($last_name) || empty($nip_nim_email) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua kolom harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $nip_nim_email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error_message = "Email sudah terdaftar!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, full_name, email, password) VALUES (:first_name, :last_name, :full_name, :email, :password)");
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':full_name', $full_name);
                $stmt->bindParam(':email', $nip_nim_email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->execute();

                echo "<script>alert('User berhasil terdaftar!'); window.location.href='login.php';</script>";
                exit;
            }
        } catch (PDOException $e) {
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<html>
 <head>
  <title>MBKM UPJ | Sign Up</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>

     </head>
 <body>
  <div class="container">
   <div class="left">
    <img alt="Kampus Merdeka Logo" height="100" src="path_to_your_logo.jpg" width="200"/>
    <h1>Merdeka Belajar - Kampus Merdeka</h1>
    <p>Universitas Pembangunan Jaya</p>
   </div>
   <div class="right">
    <h2>Selamat Datang!</h2>
    <p>Silahkan daftar untuk membuat akun dan masuk ke dalam Sistem MBKM</p>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <input placeholder="Nama Depan" type="text" name="first_name" required/>
        <input placeholder="Nama Belakang" type="text" name="last_name" required/>
        <input placeholder="NIP/NIM/Email" type="text" name="nip_nim_email" required/>
        <input placeholder="Password" type="password" name="password" required/>
        <input placeholder="Confirm Password" type="password" name="confirm_password" required/>
        <button type="submit" name="signup">Sign Up</button>
    </form>

    <div class="signup">
     <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
   </div>
  </div>
 </body>
</html>
