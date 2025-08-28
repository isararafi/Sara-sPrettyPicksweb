<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Ensure user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.id, o.total_amount, o.created_at, 
           oi.product_id, oi.quantity, oi.price, 
           p.name, p.image
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($order_items)) {
    header("Location: index.php");
    exit();
}

$order = [
    'id' => $order_items[0]['id'],
    'total_amount' => $order_items[0]['total_amount'],
    'created_at' => $order_items[0]['created_at'],
    'items' => $order_items
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Sara's Pretty Picks</title>
    <link rel="stylesheet" href="assets/css/order-confirmation.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Critical CSS to ensure immediate styling */
        body {
            font-family: 'Inter', 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f0f6 0%, #f8e1e9 100%);
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .success-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .success-icon {
            font-size: 4rem;
            color: #4caf50;
            margin-bottom: 1.5rem;
        }
        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #9c27b0, #ec407a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .order-summary-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .item-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            margin-bottom: 1rem;
        }
        .item-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
        }
    </style>
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
                    <?php if (is_logged_in()): ?>
                        <li><a href="my_orders.php">My Orders</a></li>
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
        <div class="order-confirmation-wrapper">
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="success-title">Order Confirmed!</h1>
                <p class="success-subtitle">Thank you for your purchase. Your order has been successfully placed.</p>
            </div>

            <!-- Order Summary Card -->
            <div class="order-summary-card">
                <div class="order-header">
                    <div class="order-info">
                        <h2>Order #<?php echo $order['id']; ?></h2>
                        <p class="order-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                        </p>
                        <p class="order-time">
                            <i class="fas fa-clock"></i>
                            <?php echo date('g:i A', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div class="order-status">
                        <span class="status-badge">
                            <i class="fas fa-shipping-fast"></i>
                            Processing
                        </span>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-items">
                    <h3>Order Items</h3>
                    <div class="items-grid">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="item-card">
                                <div class="item-image">
                                    <img src="assets/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="item-details">
                                    <h4 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <div class="item-meta">
                                        <span class="item-price">$<?php echo number_format($item['price'], 2); ?></span>
                                        <span class="item-quantity">Qty: <?php echo htmlspecialchars($item['quantity']); ?></span>
                                    </div>
                                    <div class="item-subtotal">
                                        Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Total -->
                <div class="order-total">
                    <div class="total-row">
                        <span class="total-label">Total Items:</span>
                        <span class="total-value"><?php echo count($order['items']); ?></span>
                    </div>
                    <div class="total-row grand-total">
                        <span class="total-label">Order Total:</span>
                        <span class="total-value">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="next-steps-card">
                <h3>What's Next?</h3>
                <div class="steps-grid">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="step-content">
                            <h4>Confirmation Email</h4>
                            <p>You'll receive a confirmation email with your order details shortly.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="step-content">
                            <h4>Order Processing</h4>
                            <p>We'll process your order and prepare it for shipping within 1-2 business days.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="step-content">
                            <h4>Shipping Updates</h4>
                            <p>You'll receive tracking information once your order ships.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i>
                    Continue Shopping
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sara's Pretty Picks. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>