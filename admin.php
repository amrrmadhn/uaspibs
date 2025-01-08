<?php
session_start();
require_once 'admin_functions.php';

// Debug connection
try {
    // Tambahkan ini di awal file untuk debug
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Test database connection
    $test = $pdo->query('SELECT 1');
    error_log("Database connection successful");
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login-admin.php");
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $submission_id = $_POST['submission_id'];
    $new_status = $_POST['new_status'];
    $note = $_POST['note'];

    try {
        $stmt = $pdo->prepare("UPDATE submissions SET status = ?, note = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$new_status, $note, $submission_id]);
        
        if($result) {
            $_SESSION['success'] = 'Status updated successfully';
            error_log("Status updated successfully for submission ID: " . $submission_id);
        } else {
            $_SESSION['error'] = 'Failed to update status';
            error_log("Failed to update status for submission ID: " . $submission_id);
        }
    } catch (PDOException $e) {
        error_log("Error updating status: " . $e->getMessage());
        $_SESSION['error'] = 'Error updating status: ' . $e->getMessage();
    }
    header("Location: admin.php");
    exit();
}


// Fetch all submissions with debug
try {
    // Simplified query first to test
    $query = "SELECT * FROM submissions ORDER BY tanggal_submit DESC";
    error_log("Executing query: " . $query);
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the number of submissions found
    error_log("Found " . count($submissions) . " submissions");
    
    if(empty($submissions)) {
        error_log("No submissions found in database");
    }
} catch (PDOException $e) {
    error_log("Error fetching submissions: " . $e->getMessage());
    $submissions = [];
    die("Error fetching submissions: " . $e->getMessage());
}
?>
