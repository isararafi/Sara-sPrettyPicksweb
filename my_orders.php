<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'config/db.php';

// Ensure user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Get user's orders
$stmt = $pdo->prepare("
    SELECT o.id, o.total_amount, o.created_at, o.status,
           COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Sara's Pretty Picks</title>
    <link rel="stylesheet" href="assets/css/my-orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Sara's Pretty Picks</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="favorites.php">Favorites</a></li>
                    <li><a href="my_orders.php" class="active">My Orders</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="orders-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-shopping-bag"></i>
                    My Orders
                </h1>
                <p class="page-subtitle">Track your order history and view past purchases</p>
            </div>

            <?php if (empty($orders)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h2>No Orders Yet</h2>
                    <p>You haven't placed any orders yet. Start shopping to see your order history here!</p>
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3 class="order-number">Order #<?php echo $order['id']; ?></h3>
                                    <div class="order-meta">
                                        <span class="order-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                        </span>
                                        <span class="order-time">
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('g:i A', strtotime($order['created_at'])); ?>
                                        </span>
                                        <span class="order-items">
                                            <i class="fas fa-box"></i>
                                            <?php echo $order['item_count']; ?> item<?php echo $order['item_count'] != 1 ? 's' : ''; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <i class="fas fa-circle"></i>
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-summary">
                                <div class="order-total">
                                    <span class="total-label">Total:</span>
                                    <span class="total-amount">$<?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>
                                <div class="order-actions">
                                    <a href="order_confirmation.php?id=<?php echo $order['id']; ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Status Legend -->
                <div class="status-legend">
                    <h3>Order Status Guide</h3>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="status-badge status-processing">
                                <i class="fas fa-circle"></i>
                                Processing
                            </span>
                            <span class="legend-text">Your order is being prepared</span>
                        </div>
                        <div class="legend-item">
                            <span class="status-badge status-shipped">
                                <i class="fas fa-circle"></i>
                                Shipped
                            </span>
                            <span class="legend-text">Your order is on its way</span>
                        </div>
                        <div class="legend-item">
                            <span class="status-badge status-delivered">
                                <i class="fas fa-circle"></i>
                                Delivered
                            </span>
                            <span class="legend-text">Your order has been delivered</span>
                        </div>
                        <div class="legend-item">
                            <span class="status-badge status-cancelled">
                                <i class="fas fa-circle"></i>
                                Cancelled
                            </span>
                            <span class="legend-text">Your order was cancelled</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sara's Pretty Picks. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
