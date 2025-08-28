<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'config/db.php';

// Ensure user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

// Check if product already in cart
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['user_id'], $product_id]);

if ($stmt->rowCount() > 0) {
    // Update quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
} else {
    // Add to cart
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
}

header("Location: cart.php");
exit();
?>