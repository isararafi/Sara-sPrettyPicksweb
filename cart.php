<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Ensure user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['remove'], $_SESSION['user_id']]);
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Online Shop</title>
    <link rel="stylesheet" href="assets/css/cart.css">
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
    <section class="cart">
    <h2>Your Shopping Cart</h2>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="products.php">Browse products</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th class="product-col">Product</th>
                    <th class="price-col">Price</th>
                    <th class="quantity-col">Quantity</th>
                    <th class="subtotal-col">Subtotal</th>
                    <th class="action-col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td class="product-col">
                            <div class="cart-product">
                                <img src="assets/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="cart-image">
                                <span class="cart-product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td class="price-col">$<?php echo htmlspecialchars($item['price']); ?></td>
                        <td class="quantity-col">
                            <div class="quantity-controls">
                                <button class="qty-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                                <span class="qty-display" id="qty-<?php echo $item['cart_id']; ?>"><?php echo htmlspecialchars($item['quantity']); ?></span>
                                <button class="qty-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                            </div>
                        </td>
                        <td class="subtotal-col" id="subtotal-<?php echo $item['cart_id']; ?>">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td class="action-col"><a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="btn btn-remove">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total-label">Total</td>
                    <td class="total-amount" id="cart-total">$<?php echo number_format($total, 2); ?></td>
                    <td class="checkout-col"><a href="checkout.php" class="btn btn-checkout">Checkout</a></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sara's Pretty Picks . All rights reserved.</p>
        </div>
    </footer>

    <script>
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) return;
            
            fetch('update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_id=${cartId}&quantity=${newQuantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`qty-${cartId}`).textContent = data.quantity;
                    document.getElementById(`subtotal-${cartId}`).textContent = `$${data.subtotal}`;
                    document.getElementById('cart-total').textContent = `$${data.total}`;
                } else {
                    alert('Error updating quantity: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating quantity');
            });
        }
    </script>
</body>
</html>