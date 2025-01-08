<?php
// Buat file baru check_database.php untuk memverifikasi isi database
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'admin_functions.php';

try {
    // Test koneksi
    $test = $pdo->query('SELECT 1');
    echo "Database connection successful<br>";
    
    // Cek tabel submissions
    $stmt = $pdo->query("SHOW TABLES LIKE 'submissions'");
    if($stmt->rowCount() > 0) {
        echo "Submissions table exists<br>";
        
        // Cek struktur tabel
        $stmt = $pdo->query("DESCRIBE submissions");
        echo "Table structure:<br>";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
        
        // Cek jumlah data
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM submissions");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total submissions: " . $count['total'] . "<br>";
        
        // Tampilkan beberapa data terakhir
        $stmt = $pdo->query("SELECT * FROM submissions ORDER BY tanggal_submit DESC LIMIT 5");
        echo "Latest submissions:<br>";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "ID: " . $row['id'] . ", User: " . $row['full_name'] . ", Status: " . $row['status'] . "<br>";
        }
    } else {
        echo "Submissions table does not exist<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>