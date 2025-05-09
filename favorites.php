<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Handle remove from favorites
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['remove'], $_SESSION['user_id']]);
}

// Get favorite items
$stmt = $pdo->prepare("
    SELECT f.id as favorite_id, p.id as product_id, p.name, p.price, p.image 
    FROM favorites f 
    JOIN products p ON f.product_id = p.id 
    WHERE f.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$favorite_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorites - Online Shop</title>
    <link rel="stylesheet" href="assets/css/favorites.css">
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
        <section class="favorites">
            <h2>Your Favorite Products</h2>
            <?php if (empty($favorite_items)): ?>
                <p>You don't have any favorite products yet. <a href="products.php">Browse products</a></p>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($favorite_items as $item): ?>
                        <div class="product-card">
                            <img src="assets/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price">$<?php echo htmlspecialchars($item['price']); ?></p>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $item['product_id']; ?>" class="btn">View Details</a>
                                <a href="add_to_cart.php?id=<?php echo $item['product_id']; ?>" class="btn">Add to Cart</a>
                                <a href="favorites.php?remove=<?php echo $item['favorite_id']; ?>" class="btn">Remove</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sara's Pretty Picks. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>