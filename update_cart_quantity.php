<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'config/db.php';

// Ensure user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_id = $_POST['cart_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    
    if (!$cart_id || !$quantity) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing cart_id or quantity']);
        exit();
    }
    
    // Validate quantity
    $quantity = (int)$quantity;
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    try {
        // Update cart quantity
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            // Get updated cart item with price
            $stmt = $pdo->prepare("
                SELECT c.quantity, p.price 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$cart_id, $_SESSION['user_id']]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $subtotal = $item['quantity'] * $item['price'];
            
            // Calculate new total
            $stmt = $pdo->prepare("
                SELECT SUM(c.quantity * p.price) as total 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $total = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'quantity' => $item['quantity'],
                'subtotal' => number_format($subtotal, 2),
                'total' => number_format($total, 2)
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Cart item not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
