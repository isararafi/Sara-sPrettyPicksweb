<?php
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete order items first
    $sql = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // Then delete the order
    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // If everything is successful, commit the transaction
    mysqli_commit($conn);
    
    $_SESSION['message'] = "Order deleted successfully!";
    $_SESSION['message_type'] = "success";
} catch (Exception $e) {
    // If there's an error, rollback the transaction
    mysqli_rollback($conn);
    
    $_SESSION['message'] = "Error deleting order: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: index.php");
exit(); 