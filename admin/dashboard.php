<?php
// Start output buffering to prevent headers issues
ob_start();

require_once 'includes/header.php';

// Get basic statistics
$stats_sql = "SELECT COUNT(*) as total_orders FROM orders";
$stats_result = mysqli_query($conn, $stats_sql);
$total_orders = mysqli_fetch_assoc($stats_result)['total_orders'];

// Get product count
$product_count_sql = "SELECT COUNT(*) as total_products FROM products";
$product_result = mysqli_query($conn, $product_count_sql);
$total_products = mysqli_fetch_assoc($product_result)['total_products'];

// Get user count
$user_count_sql = "SELECT COUNT(*) as total_users FROM users WHERE role = 'customer'";
$user_result = mysqli_query($conn, $user_count_sql);
$total_users = mysqli_fetch_assoc($user_result)['total_users'];
?>

<style>
    .stats-card {
        background: linear-gradient(135deg, #9c27b0, #ec407a);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(156, 39, 176, 0.3);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-number {
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .stats-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    .welcome-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        padding: 30px;
    }
</style>

<div class="container-fluid">
    <div class="welcome-card">
        <h1 class="mb-3">
            <i class="fas fa-tachometer-alt me-2"></i>Welcome to Admin Dashboard
        </h1>
        <p class="lead text-muted">
            Manage your e-commerce store, products, customers, and orders from this central dashboard.
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="stats-card text-center">
                <div class="stats-number"><?php echo $total_products; ?></div>
                <div class="stats-label">Total Products</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card text-center">
                <div class="stats-number"><?php echo $total_users; ?></div>
                <div class="stats-label">Total Customers</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card text-center">
                <div class="stats-number"><?php echo $total_orders; ?></div>
                <div class="stats-label">Total Orders</div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'includes/footer.php'; 
ob_end_flush();
?>