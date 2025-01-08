function redirect($location) {
    header("Location: $location");
    exit();
}

function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function logout() {
    session_unset();
    session_destroy();
    redirect('login.php');
}
?>