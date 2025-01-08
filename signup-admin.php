<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

if(isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = array();
    
    // Validation
    if(strlen($username) < 4) {
        $errors[] = "Username harus minimal 4 karakter!";
    }
    
    if(strlen($password) < 6) {
        $errors[] = "Password harus minimal 6 karakter!";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Password tidak cocok!";
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    if($stmt->fetchColumn() > 0) {
        $errors[] = "Username sudah digunakan!";
    }
    
    // If no errors, proceed with registration
    if(empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            
            $_SESSION['success_message'] = "Admin berhasil didaftarkan! Silakan login.";
            header("Location: login-admin.php");
            exit();
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        }
    }
}
?>
