<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = get_products();

// Map specific search terms to product categories
$search_mappings = [
    'phone cases' => 'Phone Case',
    'mobile cases' => 'Phone Case',
    'mobile covers' => 'Phone Case',
    'lights' => 'Lights',
    'fairy lights' => 'Lights',
    'hijab' => 'Hijab',
    'hijabs' => 'Hijab'
];

$effective_query = $search_query;
foreach ($search_mappings as $keyword => $category) {
    if (strcasecmp($search_query, $keyword) === 0) {
        $effective_query = $category;
        break;
    }
}

if ($effective_query) {
    $products = array_filter($products, function($product) use ($effective_query) {
        return stripos($product['name'], $effective_query) !== false || 
               stripos($product['description'], $effective_query) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Online Shop</title>
    <link rel="stylesheet" href="assets/css/products.css">
    <style>
        .search-container {
            margin: 20px 0;
            text-align: left;
            max-width: 400px;
            position: relative;
        }
        .search-container input {
            width: 100%;
            padding: 12px 20px 12px 40px; /* Adjusted padding for icon */
            border: none;
            border-radius: 25px;
            background-color: #f8e1f4; /* Soft pink for background */
            color: #4b2e4a; /* Dark purple for text */
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .search-container input:focus {
            outline: none;
            background-color: #ffffff; /* White for focus state */
            box-shadow: 0 4px 8px rgba(139, 69, 133, 0.2); /* Subtle purple shadow */
            transform: scale(1.02);
        }
        .search-container input::placeholder {
            color: #8b4585; /* Muted purple for placeholder */
            font-style: italic;
        }
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: #8b4585; /* Matches placeholder color */
        }
        .no-products {
            text-align: center;
            color: #d68cb0; /* Soft pinkish-purple for error message */
            margin: 20px 0;
            font-size: 18px;
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
        <section class="products">
            <h2>All Products</h2>
            <div class="search-container">
                <svg class="search-icon" viewBox="0 0 24 24">
                    <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <div class="product-grid" id="productGrid">
                <?php if (empty($products)): ?>
                    <p class="no-products">Product not found</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-description="<?php echo htmlspecialchars($product['description']); ?>">
                            <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-action btn-view">
                                    <svg class="btn-icon" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zM12 17c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"/></svg>
                                    Details
                                </a>
                                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn-action btn-cart">
                                    <svg class="btn-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.6L5.2 14c-.1.3-.2.6-.2 1 0 1.1.9 2 2 2h12v-2H7.4c-.1 0-.2-.1-.2-.2v-.1l.9-1.7h7.4c.8 0 1.4-.4 1.7-1l3.9-7c.1-.2.2-.4.2-.6 0-.6-.4-1-1-1H5.2l-1-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    Add
                                </a>
                                <a href="add_to_favorites.php?id=<?php echo $product['id']; ?>" class="btn-action btn-fav">
                                    <svg class="btn-icon" viewBox="0 0 24 24"><path d="M12 21.4l-1.4-1.3C5.4 15.4 2 12.3 2 8.5 2 5.4 4.4 3 7.5 3c1.7 0 3.4.8 4.5 2.1C13.1 3.8 14.8 3 16.5 3 19.6 3 22 5.4 22 8.5c0 3.8-3.4 6.9-8.6 11.6L12 21.4z"/></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>© 2025 Sara's Pretty Picks. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            let hasMatch = false;

            // Map specific search terms to product categories
            const searchMappings = {
                'phone cases': 'phone case',
                'mobile cases': 'phone case',
                'mobile covers': 'phone case',
                'lights': 'lights',
                'fairy lights': 'lights',
                'hijab': 'hijab',
                'hijabs': 'hijab'
            };

            let effectiveSearch = searchTerm;
            for (let keyword in searchMappings) {
                if (searchTerm === keyword) {
                    effectiveSearch = searchMappings[keyword];
                    break;
                }
            }

            productCards.forEach(card => {
                const productName = card.querySelector('h3').textContent.toLowerCase();
                const productDescription = card.getAttribute('data-description').toLowerCase();
                if (productName.includes(effectiveSearch) || productDescription.includes(effectiveSearch)) {
                    card.style.display = 'block';
                    hasMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });

            let noProductsMsg = document.querySelector('.no-products');
            if (!hasMatch && searchTerm) {
                if (!noProductsMsg) {
                    noProductsMsg = document.createElement('p');
                    noProductsMsg.className = 'no-products';
                    noProductsMsg.textContent = 'Product not found';
                    document.getElementById('productGrid').prepend(noProductsMsg);
                }
            } else if (noProductsMsg) {
                noProductsMsg.remove();
            }
        });
    </script>
</body>
</html>