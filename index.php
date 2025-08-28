<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
$products = get_products(8); // Get first 8 products
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sara's Pretty Picks - Home</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <header>
        <div class="container">
            <h1>Sara's Pretty Picks</h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Home</a></li>
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
        <section class="hero">
            <h2>Discover Sara's Pretty Picks</h2>
            <p>Shop unique, stylish products curated just for you!</p>
            <a href="products.php" class="btn hero-btn">Explore Now</a>
        </section>

        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card reveal">
                        <div class="product-image">
                            <img src="assets/<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-action btn-view">
                                <svg class="btn-icon" viewBox="0 0 24 24">
                                    <path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zM12 17c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z" />
                                </svg>
                                Details
                            </a>
                            <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn-action btn-cart">
                                <svg class="btn-icon" viewBox="0 0 24 24">
                                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.6L5.2 14c-.1.3-.2.6-.2 1 0 1.1.9 2 2 2h12v-2H7.4c-.1 0-.2-.1-.2-.2v-.1l.9-1.7h7.4c.8 0 1.4-.4 1.7-1l3.9-7c.1-.2.2-.4.2-.6 0-.6-.4-1-1-1H5.2l-1-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                                </svg>
                                Add
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>© <?php echo date('Y'); ?> Sara's Pretty Picks. All rights reserved.</p>
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const reveals = document.querySelectorAll('.reveal');

            for (let i = 0; i < reveals.length; i++) {
                const windowHeight = window.innerHeight;
                const revealTop = reveals[i].getBoundingClientRect().top;
                const revealPoint = 150;

                if (revealTop < windowHeight - revealPoint) {
                    reveals[i].classList.add('active');
                }
            }
        });
    </script>
</body>

</html>