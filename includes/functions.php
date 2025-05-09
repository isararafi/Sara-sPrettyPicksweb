<?php
require_once __DIR__ . '/../config/db.php';

function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function get_products($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM products";
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_product($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_total_products() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    return $stmt->fetchColumn();
}

function get_total_users() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn();
}
?>