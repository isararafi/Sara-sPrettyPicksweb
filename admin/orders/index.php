<?php
// Start output buffering to prevent headers issues
ob_start();

require_once '../includes/header.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$date_filter = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';

// Build the SQL query with filters
$sql = "SELECT o.*, u.username as customer_name, u.email as customer_email,
               COUNT(oi.id) as item_count
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        LEFT JOIN order_items oi ON o.id = oi.order_id";

$where_conditions = [];
$params = [];
$param_types = "";

if (!empty($search)) {
    $where_conditions[] = "(u.username LIKE ? OR u.email LIKE ? OR o.id LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= "sss";
}

if (!empty($status_filter)) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
    $param_types .= "s";
}

if (!empty($date_filter)) {
    $where_conditions[] = "DATE(o.created_at) = ?";
    $params[] = $date_filter;
    $param_types .= "s";
}

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

// Execute the query
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get basic statistics
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue
FROM orders";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #9c27b0, #ec407a);
        color: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(156, 39, 176, 0.2);
    }
    .stats-summary {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e9ecef;
    }
    .search-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }
    .orders-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    .table-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
    }
    .table-body {
        padding: 0;
    }
    .order-row {
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        transition: background-color 0.2s ease;
    }
    .order-row:hover {
        background-color: #f8f9fa;
    }
    .order-row:last-child {
        border-bottom: none;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-processing { background-color: #cce5ff; color: #004085; }
    .status-completed { background-color: #d4edda; color: #155724; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }
    .btn-primary {
        background: linear-gradient(135deg, #9c27b0, #ec407a);
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #7b1fa2, #c2185b);
        transform: translateY(-1px);
    }
    .btn-outline-secondary {
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 10px 12px;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #9c27b0;
        box-shadow: 0 0 0 0.2rem rgba(156, 39, 176, 0.25);
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Order Management</h2>
                <p class="mb-0 opacity-75">Manage and track customer orders</p>
            </div>
            <div>
                <a href="javascript:window.print()" class="btn btn-light me-2">
                    <i class="fas fa-print me-1"></i>Print
                </a>
                <a href="export_orders.php" class="btn btn-light">
                    <i class="fas fa-download me-1"></i>Export
                </a>
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

    <!-- Statistics Summary -->
    <div class="stats-summary">
        <div class="row text-center">
            <div class="col-md-6">
                <h4 class="mb-1"><?php echo $stats['total_orders']; ?></h4>
                <p class="mb-0 text-muted">Total Orders</p>
            </div>
            <div class="col-md-6">
                <h4 class="mb-1">$<?php echo number_format($stats['total_revenue'], 0); ?></h4>
                <p class="mb-0 text-muted">Total Revenue</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-section">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Orders</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Customer name, email, or order ID" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" 
                       value="<?php echo htmlspecialchars($date_filter); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
        <?php if (!empty($search) || !empty($status_filter) || !empty($date_filter)): ?>
            <div class="mt-3">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Orders List -->
    <div class="orders-table">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-header">
                <h5 class="mb-0">Orders (<?php echo mysqli_num_rows($result); ?> found)</h5>
            </div>
            <div class="table-body">
                <?php while($order = mysqli_fetch_assoc($result)): ?>
                    <div class="order-row">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <strong>Order #<?php echo $order['id']; ?></strong>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1">
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                </div>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                            </div>
                            <div class="col-md-2">
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-1">
                                    <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                                <small class="text-muted"><?php echo $order['item_count']; ?> items</small>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-1">
                                    <strong><?php echo date('M d, Y', strtotime($order['created_at'])); ?></strong>
                                </div>
                                <small class="text-muted"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                            </div>
                            <div class="col-md-1 text-end">
                                <a href="view.php?id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h4>No orders found</h4>
                <p>
                    <?php if (!empty($search) || !empty($status_filter) || !empty($date_filter)): ?>
                        Try adjusting your search criteria.
                    <?php else: ?>
                        No orders have been placed yet.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once '../includes/footer.php'; 
ob_end_flush();
?>