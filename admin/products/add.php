<?php
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/";
        
        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $_SESSION['message'] = "Failed to create directory: $target_dir";
                $_SESSION['message_type'] = "danger";
                header("Location: add.php");
                exit();
            }
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $image = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $image;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Image uploaded successfully
            } else {
                $_SESSION['message'] = "Error uploading image to $target_file: " . error_get_last()['message'];
                $_SESSION['message_type'] = "danger";
                header("Location: add.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "File is not an image.";
            $_SESSION['message_type'] = "danger";
            header("Location: add.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "No image uploaded or upload error: " . $_FILES['image']['error'];
        $_SESSION['message_type'] = "danger";
        header("Location: add.php");
        exit();
    }
    
    $sql = "INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Prepare failed: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    } else {
        mysqli_stmt_bind_param($stmt, "ssdis", $name, $description, $price, $stock, $image);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Product added successfully! ID: " . mysqli_insert_id($conn);
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Execute failed: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
        mysqli_stmt_close($stmt);
    }
    
    header("Location: index.php");
    exit();
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Product</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>