<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$projects = [];
$success = '';
$categories = [];

try {
    // Fetch existing categories
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_project'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $location = $_POST['location'];
			$gallery_url = $_POST['gallery_url'] ?? null;
            $system_size = $_POST['system_size'];
            $completion_date = $_POST['completion_date'];
            $project_order = $_POST['project_order'];
            $category_input = $_POST['category'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Handle category - check if it's a new category or existing
            if (is_numeric($category_input)) {
                // It's an existing category ID
                $category_id = $category_input;
            } else {
                // It's a new category name
                $new_category = trim($category_input);
                
                // Check if category already exists (case-insensitive)
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)");
                $stmt->execute([$new_category]);
                $existing_category = $stmt->fetch();
                
                if ($existing_category) {
                    $category_id = $existing_category['id'];
                } else {
                    // Insert new category
                    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                    $stmt->execute([$new_category]);
                    $category_id = $pdo->lastInsertId();
                }
            }
            
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $upload_dir = '../assets/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                    $image_path = 'assets/uploads/' . $image_name;
                }
            }
            
			$stmt = $pdo->prepare("INSERT INTO projects (title, description, location, system_size, completion_date, image_path, gallery_url, project_order, category_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->execute([$title, $description, $location, $system_size, $completion_date, $image_path, $gallery_url, $project_order, $category_id, $is_active]);
            $success = "Project added successfully!";
        }
        
        if (isset($_POST['update_project'])) {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $location = $_POST['location'];
			$gallery_url = $_POST['gallery_url'] ?? null;
            $system_size = $_POST['system_size'];
            $completion_date = $_POST['completion_date'];
            $project_order = $_POST['project_order'];
            $category_input = $_POST['category'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Handle category - same logic as above
            if (is_numeric($category_input)) {
                $category_id = $category_input;
            } else {
                $new_category = trim($category_input);
                
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)");
                $stmt->execute([$new_category]);
                $existing_category = $stmt->fetch();
                
                if ($existing_category) {
                    $category_id = $existing_category['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                    $stmt->execute([$new_category]);
                    $category_id = $pdo->lastInsertId();
                }
            }
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $upload_dir = '../assets/uploads/';
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                    $image_path = 'assets/uploads/' . $image_name;
                    
					$stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, location=?, system_size=?, completion_date=?, image_path=?, gallery_url=?, project_order=?, category_id=?, is_active=? WHERE id=?");
					$stmt->execute([$title, $description, $location, $system_size, $completion_date, $image_path, $gallery_url, $project_order, $category_id, $is_active, $id]);
                } else {
                    // If file upload fails, update without image
					$stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, location=?, system_size=?, completion_date=?, gallery_url=?, project_order=?, category_id=?, is_active=? WHERE id=?");
					$stmt->execute([$title, $description, $location, $system_size, $completion_date, $gallery_url, $project_order, $category_id, $is_active, $id]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, location=?, system_size=?, completion_date=?, project_order=?, category_id=?, is_active=? WHERE id=?");
                $stmt->execute([$title, $description, $location, $system_size, $completion_date, $project_order, $category_id, $is_active, $id]);
            }
            $success = "Project updated successfully!";
        }
        
        if (isset($_POST['delete_project'])) {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Project deleted successfully!";
        }
        
        // Refresh categories after any changes
        $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    // Fetch projects with category names
    $projects_result = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM projects p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.project_order ASC, p.completion_date DESC
    ");
    
    if ($projects_result) {
        $projects = $projects_result->fetchAll();
    } else {
        $projects = [];
        error_log("Projects query failed");
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Database error occurred. Please try again.";
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .category-select-wrapper {
            position: relative;
        }
        .category-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .category-option {
            padding: 8px 12px;
            cursor: pointer;
        }
        .category-option:hover {
            background-color: #f8f9fa;
        }
        .new-category-notice {
            display: none;
            margin-top: 5px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Projects</h1>
            <a href="update_content.php?section_id=projects" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Content Management
            </a>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Add Project Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Project</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="projectForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" required>
                            </div>
							<div class="mb-3">
								<label class="form-label">Gallery URL</label>
								<input type="url" name="gallery_url" class="form-control" placeholder="https://example.com/gallery">
								<small class="text-muted">Optional: Link to external gallery or album</small>
							</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">System Size</label>
                                <input type="text" name="system_size" class="form-control" placeholder="e.g., 8.5 kW" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <div class="category-select-wrapper">
                                    <input type="text" 
                                           name="category" 
                                           class="form-control category-input" 
                                           id="categoryInput"
                                           placeholder="Select or type a new category"
                                           autocomplete="off"
                                           required>
                                    <div class="category-options" id="categoryOptions">
                                        <?php foreach ($categories as $category): ?>
                                            <div class="category-option" data-value="<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="new-category-notice alert alert-info" id="newCategoryNotice">
                                    This will create a new category. Click to confirm or select an existing category.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Completion Date</label>
                                <input type="date" name="completion_date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project Order</label>
                                <input type="number" name="project_order" class="form-control" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_project" class="btn btn-primary" id="submitBtn">Add Project</button>
                </form>
            </div>
        </div>
        
        <!-- Projects List -->
        <!-- Projects List -->
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">Existing Projects</h5>
			</div>
			<div class="card-body">
				<?php if (empty($projects)): ?>
					<div class="alert alert-info">
						No projects found. Add your first project using the form above.
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Image</th>
									<th>Title</th>
									<th>Category</th>
									<th>Location</th>
									<th>Size</th>
									<th>Order</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($projects as $project): ?>
								<tr>
									<td>
										<?php if ($project['image_path']): ?>
											<img src="../<?php echo $project['image_path']; ?>" width="60" height="40" style="object-fit: cover;" class="rounded">
										<?php else: ?>
											<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 40px;">
												<i class="fas fa-image text-muted"></i>
											</div>
										<?php endif; ?>
									</td>
									<td><?php echo htmlspecialchars($project['title']); ?></td>
									<td><?php echo htmlspecialchars($project['category_name'] ?? 'Uncategorized'); ?></td>
									<td><?php echo htmlspecialchars($project['location']); ?></td>
									<td><?php echo htmlspecialchars($project['system_size']); ?></td>
									<td><?php echo $project['project_order']; ?></td>
									<td>
										<span class="badge bg-<?php echo $project['is_active'] ? 'success' : 'danger'; ?>">
											<?php echo $project['is_active'] ? 'Active' : 'Inactive'; ?>
										</span>
									</td>
									<td>
										<button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProjectModal<?php echo $project['id']; ?>">Edit</button>
										<form method="POST" style="display: inline;">
											<input type="hidden" name="id" value="<?php echo $project['id']; ?>">
											<button type="submit" name="delete_project" class="btn btn-sm btn-danger" onclick="return confirm('Delete this project?')">Delete</button>
										</form>
									</td>
								</tr>
								
								<!-- Edit Project Modal -->
								<div class="modal fade" id="editProjectModal<?php echo $project['id']; ?>" tabindex="-1">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Edit Project</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
											</div>
											<form method="POST" enctype="multipart/form-data" class="edit-project-form">
												<div class="modal-body">
													<input type="hidden" name="id" value="<?php echo $project['id']; ?>">
													<div class="row">
														<div class="col-md-6">
															<div class="mb-3">
																<label class="form-label">Project Title</label>
																<input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($project['title']); ?>" required>
															</div>
															<div class="mb-3">
																<label class="form-label">Description</label>
																<textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($project['description']); ?></textarea>
															</div>
															<div class="mb-3">
																<label class="form-label">Location</label>
																<input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($project['location']); ?>" required>
															</div>
															<div class="mb-3">
																<label class="form-label">Gallery URL</label>
																<input type="url" name="gallery_url" class="form-control" 
																	   value="<?php echo htmlspecialchars($project['gallery_url'] ?? ''); ?>" 
																	   placeholder="https://example.com/gallery">
																<small class="text-muted">Optional: Link to external gallery or album</small>
															</div>
														</div>
														<div class="col-md-6">
															<div class="mb-3">
																<label class="form-label">System Size</label>
																<input type="text" name="system_size" class="form-control" value="<?php echo htmlspecialchars($project['system_size']); ?>" required>
															</div>
															<div class="mb-3">
																<label class="form-label">Category</label>
																<div class="category-select-wrapper">
																	<input type="text" 
																		   name="category" 
																		   class="form-control category-input" 
																		   value="<?php echo htmlspecialchars($project['category_name'] ?? ''); ?>"
																		   placeholder="Select or type a new category"
																		   autocomplete="off"
																		   required>
																	<div class="category-options">
																		<?php foreach ($categories as $category): ?>
																			<div class="category-option" data-value="<?php echo $category['id']; ?>">
																				<?php echo htmlspecialchars($category['name']); ?>
																			</div>
																		<?php endforeach; ?>
																	</div>
																</div>
																<div class="new-category-notice alert alert-info">
																	This will create a new category. Click to confirm or select an existing category.
																</div>
															</div>
															<div class="mb-3">
																<label class="form-label">Completion Date</label>
																<input type="date" name="completion_date" class="form-control" value="<?php echo $project['completion_date']; ?>">
															</div>
															<div class="mb-3">
																<label class="form-label">Project Order</label>
																<input type="number" name="project_order" class="form-control" value="<?php echo $project['project_order']; ?>">
															</div>
															<div class="mb-3">
																<label class="form-label">Project Image</label>
																<input type="file" name="image" class="form-control" accept="image/*">
																<?php if ($project['image_path']): ?>
																	<small class="text-muted">Current: <?php echo $project['image_path']; ?></small>
																	<br>
																	<img src="../<?php echo $project['image_path']; ?>" width="100" class="mt-2 rounded">
																<?php endif; ?>
															</div>
															<div class="mb-3 form-check">
																<input type="checkbox" name="is_active" class="form-check-input" id="active<?php echo $project['id']; ?>" <?php echo $project['is_active'] ? 'checked' : ''; ?>>
																<label class="form-check-label" for="active<?php echo $project['id']; ?>">Active</label>
															</div>
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
													<button type="submit" name="update_project" class="btn btn-primary">Update Project</button>
												</div>
											</form>
										</div>
									</div>
								</div>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize category select functionality for main form
        initCategorySelect();
        
        // Re-initialize when modals are shown
        document.addEventListener('show.bs.modal', function(event) {
            setTimeout(() => {
                initCategorySelect();
            }, 100);
        });
        
        function initCategorySelect() {
            const categoryInputs = document.querySelectorAll('.category-input');
            
            categoryInputs.forEach(input => {
                const wrapper = input.closest('.category-select-wrapper');
                // Skip if wrapper doesn't exist (element might be in hidden modal)
                if (!wrapper) return;
                
                const options = wrapper.querySelector('.category-options');
                const notice = wrapper.nextElementSibling;
                
                // Show options when input is focused
                input.addEventListener('focus', function() {
                    options.style.display = 'block';
                    filterOptions();
                });
                
                // Hide options when clicking outside
                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target)) {
                        options.style.display = 'none';
                        checkForNewCategory(input, notice);
                    }
                });
                
                // Filter options based on input
                input.addEventListener('input', function() {
                    filterOptions();
                    checkForNewCategory(input, notice);
                });
                
                // Select option when clicked
                options.addEventListener('click', function(e) {
                    if (e.target.classList.contains('category-option')) {
                        input.value = e.target.textContent;
                        options.style.display = 'none';
                        notice.style.display = 'none';
                    }
                });
                
                function filterOptions() {
                    const filter = input.value.toLowerCase();
                    const allOptions = options.querySelectorAll('.category-option');
                    let hasMatch = false;
                    
                    allOptions.forEach(option => {
                        const text = option.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            option.style.display = 'block';
                            hasMatch = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });
                    
                    // If no matches and user has typed something, show "new category" notice
                    if (!hasMatch && filter.length > 0) {
                        notice.style.display = 'block';
                    } else {
                        notice.style.display = 'none';
                    }
                }
                
                function checkForNewCategory(input, notice) {
                    const value = input.value.trim();
                    if (value.length === 0) {
                        notice.style.display = 'none';
                        return;
                    }
                    
                    // Check if the value matches any existing option
                    const allOptions = options.querySelectorAll('.category-option');
                    let isExisting = false;
                    
                    allOptions.forEach(option => {
                        if (option.textContent.toLowerCase() === value.toLowerCase()) {
                            isExisting = true;
                        }
                    });
                    
                    if (!isExisting) {
                        notice.style.display = 'block';
                    } else {
                        notice.style.display = 'none';
                    }
                }
            });
        }
        
        // Handle form submission to confirm new category creation
        const projectForm = document.getElementById('projectForm');
        if (projectForm) {
            projectForm.addEventListener('submit', function(e) {
                const categoryInput = document.getElementById('categoryInput');
                const notice = document.getElementById('newCategoryNotice');
                
                if (notice && notice.style.display === 'block') {
                    const confirmed = confirm('You are about to create a new category. Continue?');
                    if (!confirmed) {
                        e.preventDefault();
                        categoryInput.focus();
                    }
                }
            });
        }
        
        // Also handle edit forms
        const editForms = document.querySelectorAll('.edit-project-form');
        editForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const wrapper = form.querySelector('.category-select-wrapper');
                // Skip if wrapper doesn't exist
                if (!wrapper) return;
                
                const input = wrapper.querySelector('.category-input');
                const notice = wrapper.nextElementSibling;
                
                if (notice && notice.style.display === 'block') {
                    const confirmed = confirm('You are about to create a new category. Continue?');
                    if (!confirmed) {
                        e.preventDefault();
                        input.focus();
                    }
                }
            });
        });
    });
</script>
</body>
</html>