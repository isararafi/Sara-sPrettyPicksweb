<?php
// Start the session only if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (adjust these details as needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_shop";
$port = 3308; // <-- Add this line

$conn = mysqli_connect($servername, $username, $password, $dbname, $port); // <-- Pass $port here
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function requireLogin() {
    // Check if the admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Use an absolute path to ensure the correct login.php is targeted
        header("Location: /online_shop/admin/login.php");
        exit();
    }
}
?>
