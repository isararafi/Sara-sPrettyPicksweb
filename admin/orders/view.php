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
$sql = "SELECT o.*, u.username as customer_username 
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

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                    <form method="POST" class="d-flex align-items-center">
                        <select name="status" class="form-select me-2" onchange="this.form.submit()">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($order['customer_username']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo $order['status'] == 'completed' ? 'success' : 
                                        ($order['status'] == 'pending' ? 'warning' : 
                                         ($order['status'] == 'processing' ? 'primary' : 'secondary')); 
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </p>
                            <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                    </div>

                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = mysqli_fetch_assoc($items_result)): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../../assets/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                     style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Back to Orders</a>
                <a href="delete.php?id=<?php echo $order['id']; ?>" 
                   class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this order?')">
                    Delete Order
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>