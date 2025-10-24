<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_review'])) {
        $customer_name = $_POST['customer_name'];
        $review_text = $_POST['review_text'];
        $rating = $_POST['rating'];
        
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/uploads/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
            $image_path = 'assets/uploads/' . $image_name;
        }
        
        $stmt = $pdo->prepare("INSERT INTO reviews (customer_name, review_text, rating, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$customer_name, $review_text, $rating, $image_path]);
        $success = "Review added successfully!";
    }
    
    if (isset($_POST['update_review'])) {
        $id = $_POST['id'];
        $customer_name = $_POST['customer_name'];
        $review_text = $_POST['review_text'];
        $rating = $_POST['rating'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE reviews SET customer_name=?, review_text=?, rating=?, is_active=? WHERE id=?");
        $stmt->execute([$customer_name, $review_text, $rating, $is_active, $id]);
        $success = "Review updated successfully!";
    }
    
    if (isset($_POST['delete_review'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Review deleted successfully!";
    }
}

$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - <?php echo htmlspecialchars($company_name); ?></title>
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
        <h1>Manage Customer Reviews</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Add Review Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Review</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Photo</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Review Text</label>
                        <textarea name="review_text" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" name="add_review" class="btn btn-primary">Add Review</button>
                </form>
            </div>
        </div>
        
        <!-- Reviews List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Existing Reviews</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['customer_name']; ?></td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?php echo substr($review['review_text'], 0, 100) . '...'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $review['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $review['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editReviewModal<?php echo $review['id']; ?>">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                        <button type="submit" name="delete_review" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- Edit Review Modal -->
                            <div class="modal fade" id="editReviewModal<?php echo $review['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Review</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Customer Name</label>
                                                    <input type="text" name="customer_name" class="form-control" value="<?php echo $review['customer_name']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Rating</label>
                                                    <select name="rating" class="form-select" required>
                                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                                            <option value="<?php echo $i; ?>" <?php echo $i == $review['rating'] ? 'selected' : ''; ?>>
                                                                <?php echo $i; ?> Stars
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Review Text</label>
                                                    <textarea name="review_text" class="form-control" rows="4" required><?php echo $review['review_text']; ?></textarea>
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" name="is_active" class="form-check-input" id="active<?php echo $review['id']; ?>" <?php echo $review['is_active'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="active<?php echo $review['id']; ?>">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="update_review" class="btn btn-primary">Update Review</button>
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