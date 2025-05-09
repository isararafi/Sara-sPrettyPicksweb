<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

try {
    // Add to favorites (ignore if already exists due to UNIQUE constraint)
    $stmt = $pdo->prepare("INSERT IGNORE INTO favorites (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
} catch (PDOException $e) {
    // Ignore duplicate entry errors
    if ($e->getCode() != 23000) {
        die("Error: " . $e->getMessage());
    }
}

header("Location: favorites.php");
exit();
?>