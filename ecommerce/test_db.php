<?php
$host = "localhost"; 
$dbname = "ecommerce"; // Replace with your database name
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP (leave empty)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connected successfully!";
} catch (PDOException $e) {
    die("Failed to connect to the database: " . $e->getMessage());
}
?>
