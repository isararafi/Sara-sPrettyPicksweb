<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];
$product = get_product($product_id);

if (!$product) {
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Online Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Online Shop</h1>
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
        <section class="product-detail">
            <div class="product-image">
            <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" 
            alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="product-actions">
                    <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn">Add to Cart</a>
                    <a href="add_to_favorites.php?id=<?php echo $product['id']; ?>" class="btn">Add to Favorites</a>
                </div>
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