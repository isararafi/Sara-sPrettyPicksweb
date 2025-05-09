<?php
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Get product image before deleting
$sql = "SELECT image FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

// Delete the product
$sql = "DELETE FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if(mysqli_stmt_execute($stmt)) {
    // Delete the product image if it exists
    if (!empty($product['image'])) {
        $image_path = "../../assets/images/products/" . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $_SESSION['message'] = "Product deleted successfully!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting product: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
}

header("Location: index.php");
exit(); 