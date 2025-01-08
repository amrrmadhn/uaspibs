<?php
// Koneksi ke database
$host = 'localhost';       // Ganti dengan host jika perlu
$dbname = 'mbkm';          // Ganti dengan nama database Anda
$username = 'root';        // Ganti dengan username database Anda
$password = '';            // Ganti dengan password database Anda jika ada

try {
    // Membuat objek PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>