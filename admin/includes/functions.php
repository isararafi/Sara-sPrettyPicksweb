<?php
// Ensure session is started
session_start();

function get_cart_quantity() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

// Other existing functions like get_products(), is_logged_in(), etc.
?>