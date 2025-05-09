<?php
// Start output buffering to prevent headers issues
ob_start();

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <h1 class="mb-4">Admin Dashboard</h1>
    <div class="alert alert-info">
        Welcome, Admin! Here you can manage products, orders, and users.
    </div>
</div>

<?php 
require_once 'includes/footer.php'; 
ob_end_flush();
?>