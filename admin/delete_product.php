<?php
include '../includes/db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$product_id])) {
        // Redirect to manage products with success message
        header("Location: manage_products.php?success=Product deleted successfully");
        exit();
    } else {
        // Redirect to manage products with error message
        header("Location: manage_products.php?error=Failed to delete product");
        exit();
    }
} else {
    header("Location: manage_products.php?error=Invalid product ID");
    exit();
}
?>
