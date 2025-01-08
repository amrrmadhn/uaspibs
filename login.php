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
