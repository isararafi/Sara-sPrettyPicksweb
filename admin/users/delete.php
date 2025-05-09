<?php
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Check if customer has any orders
$sql = "SELECT COUNT(*) as order_count FROM orders WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['order_count'] > 0) {
    $_SESSION['message'] = "Cannot delete customer with existing orders.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// Delete the customer
$sql = "DELETE FROM users WHERE id = ? AND role = 'customer'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if(mysqli_stmt_execute($stmt)) {
    $_SESSION['message'] = "Customer deleted successfully!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting customer: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
}

header("Location: index.php");
exit(); 