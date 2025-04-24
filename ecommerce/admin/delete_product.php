<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete query
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: manage_products.php?msg=Product deleted successfully");
        exit();
    } else {
        header("Location: manage_products.php?error=Failed to delete product");
        exit();
    }
}
?>
