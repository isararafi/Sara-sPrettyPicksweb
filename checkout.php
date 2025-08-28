<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Ensure user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT p.id as product_id, p.name, p.price, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();

        // Add order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }

        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);

        $pdo->commit();

        header("Location: order_confirmation.php?id=$order_id");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Checkout failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Online Shop</title>
    <link rel="stylesheet" href="assets/css/checkout.css">
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
        <section class="checkout">
            <h2>Checkout</h2>
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <ul>
                    <?php foreach ($cart_items as $item): ?>
                        <li>
                            <?php echo htmlspecialchars($item['name']); ?>
                            (<?php echo $item['quantity']; ?> x $<?php echo $item['price']; ?>)
                            - $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
            </div>

            <form method="POST" class="checkout-form">
                <h3>Shipping Information</h3>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="zip">ZIP Code</label>
                    <input type="text" id="zip" name="zip" required>
                </div>

                <h3>Payment Method</h3>
                <div class="form-group">
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="checkbox" name="payment_methods[]" value="cod" checked>
                            <label class="payment-label">
                                <i class="fas fa-money-bill-wave"></i>
                                Cash on Delivery (COD)
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="checkbox" name="payment_methods[]" value="card">
                            <label class="payment-label">
                                <i class="fas fa-credit-card"></i>
                                Credit/Debit Card
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn">Place Order</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Online Shop. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>