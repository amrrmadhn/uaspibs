<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan saat login. Silakan coba lagi.";
    }
}
?>
