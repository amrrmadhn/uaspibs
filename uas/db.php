<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mbkm');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function redirect($location) {
    header("Location: $location");
    exit();
}

function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function regenerateSession() {
    if (!isset($_SESSION['last_regeneration'])) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    } else if (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 300)) { // 5 minutes
        logout();
    }
    $_SESSION['last_activity'] = time();
}

function isAdmin() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['is_admin']) && 
           $_SESSION['is_admin'] === true;
}

function logout() {
    // Hanya hapus session user
    unset($_SESSION['user_id']);
    unset($_SESSION['full_name']);
    // Hapus session user lainnya jika ada
    
    // Jangan hapus session admin
    if(!isset($_SESSION['admin_logged_in'])) {
        session_destroy(); // Hanya destroy session jika bukan admin yang login
    }
}

function preventFormResubmission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_SESSION['form_token']) || $_SESSION['form_token'] !== $_POST['form_token']) {
            redirect('dashboard.php');
        }
        unset($_SESSION['form_token']);
    }
}

function generateFormToken() {
    return $_SESSION['form_token'] = md5(uniqid(mt_rand(), true));
}
?>