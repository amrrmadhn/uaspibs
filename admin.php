<?php
session_start();
require_once 'admin_functions.php';

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
