<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_slide'])) {
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $button_text = $_POST['button_text'];
        $button_link = $_POST['button_link'];
        $slide_order = $_POST['slide_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'assets/uploads/' . $image_name;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO hero_slides (title, subtitle, button_text, button_link, image_path, slide_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $subtitle, $button_text, $button_link, $image_path, $slide_order, $is_active]);
        $success = "Slide added successfully!";
    }
    
    if (isset($_POST['update_slide'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $button_text = $_POST['button_text'];
        $button_link = $_POST['button_link'];
        $slide_order = $_POST['slide_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/uploads/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'assets/uploads/' . $image_name;
                $stmt = $pdo->prepare("UPDATE hero_slides SET title=?, subtitle=?, button_text=?, button_link=?, image_path=?, slide_order=?, is_active=? WHERE id=?");
                $stmt->execute([$title, $subtitle, $button_text, $button_link, $image_path, $slide_order, $is_active, $id]);
            }
        } else {
            $stmt = $pdo->prepare("UPDATE hero_slides SET title=?, subtitle=?, button_text=?, button_link=?, slide_order=?, is_active=? WHERE id=?");
            $stmt->execute([$title, $subtitle, $button_text, $button_link, $slide_order, $is_active, $id]);
        }
        $success = "Slide updated successfully!";
    }
    
    if (isset($_POST['delete_slide'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Slide deleted successfully!";
    }
}

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY slide_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero Slides - <?php echo htmlspecialchars($company_name); ?></title>
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
        <h1>Manage Hero Slides</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Add Slide Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Slide</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Slide Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <textarea name="subtitle" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="button_text" class="form-control" value="Get Free Consultation">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Button Link</label>
                                <input type="text" name="button_link" class="form-control" value="#">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slide Order</label>
                                <input type="number" name="slide_order" class="form-control" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slide Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_slide" class="btn btn-primary">Add Slide</button>
                </form>
            </div>
        </div>
        
        <!-- Slides List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Existing Slides</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($slides as $slide): ?>
                            <tr>
                                <td>
                                    <?php if ($slide['image_path']): ?>
                                        <img src="../<?php echo $slide['image_path']; ?>" width="80" height="50" style="object-fit: cover;" class="rounded">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $slide['title']; ?></td>
                                <td><?php echo $slide['slide_order']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $slide['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $slide['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSlideModal<?php echo $slide['id']; ?>">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                        <button type="submit" name="delete_slide" class="btn btn-sm btn-danger" onclick="return confirm('Delete this slide?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- Edit Slide Modal -->
                            <div class="modal fade" id="editSlideModal<?php echo $slide['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Slide</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Slide Title</label>
                                                            <input type="text" name="title" class="form-control" value="<?php echo $slide['title']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Subtitle</label>
                                                            <textarea name="subtitle" class="form-control" rows="3" required><?php echo $slide['subtitle']; ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Button Text</label>
                                                            <input type="text" name="button_text" class="form-control" value="<?php echo $slide['button_text']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Button Link</label>
                                                            <input type="text" name="button_link" class="form-control" value="<?php echo $slide['button_link']; ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Slide Order</label>
                                                            <input type="number" name="slide_order" class="form-control" value="<?php echo $slide['slide_order']; ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Slide Image</label>
                                                            <input type="file" name="image" class="form-control" accept="image/*">
                                                            <?php if ($slide['image_path']): ?>
                                                                <small class="text-muted">Current: <?php echo $slide['image_path']; ?></small>
                                                                <br>
                                                                <img src="../<?php echo $slide['image_path']; ?>" width="100" class="mt-2 rounded">
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="mb-3 form-check">
                                                            <input type="checkbox" name="is_active" class="form-check-input" id="active<?php echo $slide['id']; ?>" <?php echo $slide['is_active'] ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="active<?php echo $slide['id']; ?>">Active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="update_slide" class="btn btn-primary">Update Slide</button>
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