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
