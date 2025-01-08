<?php
require_once 'admin_functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Unauthorized');
}

if (!isset($_GET['submission_id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Missing submission ID');
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            sh.*,
            a.username as admin_name
        FROM status_history sh
        JOIN admins a ON sh.admin_id = a.id
        WHERE sh.submission_id = ?
        ORDER BY sh.created_at DESC
    ");
    $stmt->execute([$_GET['submission_id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($history);
} catch (PDOException $e) {
    error_log("Error fetching history: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    exit('Error fetching history');
}
?>