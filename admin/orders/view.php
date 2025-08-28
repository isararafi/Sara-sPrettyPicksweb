<?php
// Start output buffering to prevent headers issues
ob_start();

require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Get order details with customer information
$sql = "SELECT o.*, u.username as customer_username, u.email as customer_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: index.php");
    exit();
}

// Get order items
$sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Order status updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating order status: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }
    mysqli_stmt_close($stmt);
    header("Location: view.php?id=" . $id);
    exit();
}
?>

<style>
    .order-header {
        background: linear-gradient(135deg, #9c27b0, #ec407a);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(156, 39, 176, 0.3);
    }
    .order-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .status-badge {
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .status-pending { background: linear-gradient(135deg, #ff9800, #ff5722); color: white; }
    .status-processing { background: linear-gradient(135deg, #2196f3, #1976d2); color: white; }
    .status-completed { background: linear-gradient(135deg, #4caf50, #388e3c); color: white; }
    .status-cancelled { background: linear-gradient(135deg, #f44336, #d32f2f); color: white; }
    .btn-custom {
        background: linear-gradient(135deg, #9c27b0, #ec407a);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(156, 39, 176, 0.3);
        color: white;
    }
    .product-item {
        background: linear-gradient(135deg, #f5f0f6, #f8e1e9);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }
    .product-item:hover {
        transform: translateX(5px);
    }
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>

<div class="container-fluid">
    <!-- Order Header -->
    <div class="order-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="fas fa-shopping-bag me-2"></i>Order #<?php echo htmlspecialchars($order['id']); ?>
                </h2>
                <p class="mb-0 opacity-75">
                    <i class="fas fa-calendar me-2"></i>
                    Placed on <?php echo date('F d, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <form method="POST" class="d-inline-block">
                    <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
                <div class="mt-2">
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-4">
            <div class="order-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h5>
                    <div class="mb-3">
                        <p class="mb-1">
                            <strong>Username:</strong><br>
                            <?php echo htmlspecialchars($order['customer_username']); ?>
                        </p>
                        <p class="mb-1">
                            <strong>Email:</strong><br>
                            <?php echo htmlspecialchars($order['customer_email']); ?>
                        </p>
                        <p class="mb-0">
                            <strong>Customer ID:</strong><br>
                            #<?php echo $order['user_id']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                    <div class="mb-3">
                        <p class="mb-1">
                            <strong>Order ID:</strong><br>
                            #<?php echo $order['id']; ?>
                        </p>
                        <p class="mb-1">
                            <strong>Order Date:</strong><br>
                            <?php echo date('F d, Y', strtotime($order['created_at'])); ?>
                        </p>
                        <p class="mb-1">
                            <strong>Order Time:</strong><br>
                            <?php echo date('g:i A', strtotime($order['created_at'])); ?>
                        </p>
                        <p class="mb-1">
                            <strong>Items Count:</strong><br>
                            <?php echo mysqli_num_rows($items_result); ?> item(s)
                        </p>
                        <p class="mb-0">
                            <strong>Total Amount:</strong><br>
                            <span class="h5 text-success">$<?php echo number_format($order['total_amount'], 2); ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="order-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="index.php" class="btn btn-custom">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                        <a href="javascript:window.print()" class="btn btn-outline-primary">
                            <i class="fas fa-print me-2"></i>Print Order
                        </a>
                        <a href="delete.php?id=<?php echo $order['id']; ?>" 
                           class="btn btn-outline-danger"
                           onclick="return confirm('Are you sure you want to delete this order?')">
                            <i class="fas fa-trash me-2"></i>Delete Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-md-8">
            <div class="order-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-box me-2"></i>Order Items (<?php echo mysqli_num_rows($items_result); ?> items)
                    </h5>
                    
                    <?php if (mysqli_num_rows($items_result) > 0): ?>
                        <?php 
                        $total_items = 0;
                        while($item = mysqli_fetch_assoc($items_result)): 
                            $total_items += $item['quantity'];
                        ?>
                            <div class="product-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="../../assets/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                             class="product-image">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                        <p class="mb-0 text-muted">Product ID: #<?php echo $item['product_id']; ?></p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="badge bg-primary">Qty: <?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <strong>$<?php echo number_format($item['price'], 2); ?></strong><br>
                                        <small class="text-muted">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?> total</small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        
                        <!-- Order Totals -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Total Items:</strong> <?php echo $total_items; ?></p>
                                    <p class="mb-0"><strong>Total Products:</strong> <?php echo mysqli_num_rows($items_result); ?></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h4 class="mb-0 text-success">
                                        Total: $<?php echo number_format($order['total_amount'], 2); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No items found for this order</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once '../includes/footer.php'; 
ob_end_flush();
?>