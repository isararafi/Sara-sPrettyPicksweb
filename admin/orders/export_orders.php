<?php
require_once '../includes/config.php';
requireLogin();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, [
    'Order ID',
    'Customer Name',
    'Customer Email',
    'Total Amount',
    'Status',
    'Items Count',
    'Order Date',
    'Order Time'
]);

// Get search and filter parameters (same as index.php)
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

// Write data rows
while ($row = mysqli_fetch_assoc($result)) {
    $order_date = date('Y-m-d', strtotime($row['created_at']));
    $order_time = date('H:i:s', strtotime($row['created_at']));
    
    fputcsv($output, [
        $row['id'],
        $row['customer_name'],
        $row['customer_email'],
        number_format($row['total_amount'], 2),
        ucfirst($row['status']),
        $row['item_count'],
        $order_date,
        $order_time
    ]);
}

// Close the file
fclose($output);
exit();
?>
