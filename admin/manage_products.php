<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $features = $_POST['features'];
        
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/uploads/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
            $image_path = 'assets/uploads/' . $image_name;
        }
        
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, features, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $features, $image_path]);
        $success = "Product added successfully!";
    }
    
    if (isset($_POST['update_product'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $features = $_POST['features'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/uploads/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
            $image_path = 'assets/uploads/' . $image_name;
            
            $stmt = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, features=?, is_active=?, image_path=? WHERE id=?");
            $stmt->execute([$title, $description, $price, $features, $is_active, $image_path, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, features=?, is_active=? WHERE id=?");
            $stmt->execute([$title, $description, $price, $features, $is_active, $id]);
        }
        $success = "Product updated successfully!";
    }
    
    if (isset($_POST['delete_product'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Product deleted successfully!";
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1>Manage Products & Services</h1>
			<a href="update_content.php?section_id=products" class="btn btn-outline-secondary">
				<i class="fas fa-arrow-left"></i> Back to Content Management
			</a>
		</div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Add Product Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Product</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Features (separate with |)</label>
                                <textarea name="features" class="form-control" rows="3" placeholder="Feature 1|Feature 2|Feature 3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Product Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
        
        <!-- Products List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Existing Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['image_path']): ?>
                                        <img src="../<?php echo $product['image_path']; ?>" width="50" height="50" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product['title']; ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $product['id']; ?>">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="delete_product" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- Edit Product Modal -->
                            <div class="modal fade" id="editProductModal<?php echo $product['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Product Title</label>
                                                            <input type="text" name="title" class="form-control" value="<?php echo $product['title']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="description" class="form-control" rows="3" required><?php echo $product['description']; ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Price</label>
                                                            <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" step="0.01">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Features (separate with |)</label>
                                                            <textarea name="features" class="form-control" rows="3"><?php echo $product['features']; ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Product Image</label>
                                                            <input type="file" name="image" class="form-control" accept="image/*">
                                                            <?php if ($product['image_path']): ?>
                                                                <small class="text-muted">Current: <?php echo $product['image_path']; ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="mb-3 form-check">
                                                            <input type="checkbox" name="is_active" class="form-check-input" id="active<?php echo $product['id']; ?>" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="active<?php echo $product['id']; ?>">Active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>