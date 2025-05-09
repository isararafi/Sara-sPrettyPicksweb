<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

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
    <title>Order Confirmation - Online Shop</title>
    <link rel="stylesheet" href="assets/css/order-confirmation.css">
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
        <section class="order-confirmation">
            <h2>Order Confirmation</h2>
            <div class="confirmation-message">
                <p>Thank you for your order!</p>
                <p>Your order #<?php echo $order['id']; ?> has been placed successfully.</p>
            </div>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                <p><strong>Order Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                
                <h4>Order Items</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <img src="assets/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-image">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="actions">
                <a href="products.php" class="btn">Continue Shopping</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Online Shop. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>