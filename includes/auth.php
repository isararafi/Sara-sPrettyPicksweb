<?php
session_start();

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && 
           isset($_SESSION['admin_role']) && 
           $_SESSION['admin_role'] === 'admin';
}

// Redirect if not logged in
function adminLoginRequired() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Check if user is logged in (for customer side)
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Alias for is_logged_in() function (for compatibility)
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Redirect if user not logged in (for customer side)
function loginRequired() {
    if (!isUserLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
?>