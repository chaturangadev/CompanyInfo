<?php
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle section content updates
    if (isset($_POST['section_name'])) {
        $section_name = $_POST['section_name'];
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        
        // Handle file upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Validate image file
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = mime_content_type($_FILES['image']['tmp_name']);
            
            if (in_array($file_type, $allowed_types)) {
                $image_name = time() . '_' . uniqid() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = 'assets/uploads/' . $image_name;
                } else {
                    $upload_error = "Failed to upload image.";
                }
            } else {
                $upload_error = "Invalid file type. Please upload JPEG, PNG, GIF, or WebP images.";
            }
        }
        
        // Update content in database
        if (isset($image_path)) {
            $stmt = $pdo->prepare("UPDATE website_content SET title = ?, content = ?, image_path = ? WHERE section_name = ?");
            $stmt->execute([$title, $content, $image_path, $section_name]);
        } else {
            $stmt = $pdo->prepare("UPDATE website_content SET title = ?, content = ? WHERE section_name = ?");
            $stmt->execute([$title, $content, $section_name]);
        }
        
        $success = "Content updated successfully!";
    }
    
    // Handle features management (for Why Choose Us section)
    if (isset($_POST['update_features'])) {
        // Update existing features
        if (isset($_POST['features'])) {
            foreach ($_POST['features'] as $feature_id => $feature_data) {
                $stmt = $pdo->prepare("UPDATE why_choose_features SET title = ?, description = ?, icon_class = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    $feature_data['title'],
                    $feature_data['description'],
                    $feature_data['icon_class'],
                    $feature_data['display_order'],
                    $feature_id
                ]);
            }
        }
        
        // Add new feature if provided
		if (!empty($_POST['new_feature_title']) && !empty($_POST['new_feature_description'])) {
			// Check if feature already exists
			$check_stmt = $pdo->prepare("SELECT COUNT(*) FROM why_choose_features WHERE title = ?");
			$check_stmt->execute([$_POST['new_feature_title']]);
			$exists = $check_stmt->fetchColumn();
			
			if (!$exists) {
				$max_order = $pdo->query("SELECT MAX(display_order) FROM why_choose_features")->fetchColumn();
				$new_order = $max_order ? $max_order + 1 : 1;
				
				$stmt = $pdo->prepare("INSERT INTO why_choose_features (title, description, icon_class, display_order) VALUES (?, ?, ?, ?)");
				$stmt->execute([
					$_POST['new_feature_title'],
					$_POST['new_feature_description'],
					$_POST['new_feature_icon'] ?? 'fas fa-star',
					$new_order
				]);
				$success = "Features updated successfully!";
			} else {
				$error = "A feature with this title already exists!";
			}
		}
        
        $success = "Features updated successfully!";
		
		// Redirect to prevent form resubmission
		header("Location: ?section_id=why_choose");
		exit;
    }
    
    // Handle feature deletion
    if (isset($_POST['delete_feature'])) {
        $feature_id = $_POST['delete_feature'];
        $stmt = $pdo->prepare("DELETE FROM why_choose_features WHERE id = ?");
        $stmt->execute([$feature_id]);
        $success = "Feature deleted successfully!";
		
		// Redirect to prevent form resubmission
		header("Location: ?section_id=why_choose");
		exit;
    }
}

// Get section ID from URL parameter
$section_id = $_GET['section_id'] ?? 'about';

// Define available sections
$available_sections = [
    'about' => 'About Us',
    'projects' => 'Our Projects',
    'products' => 'Our Products',
	'review' => 'What Our Customers Say',
    'why_choose' => 'Why Choose Us',
    'contact' => 'Contact Us'
];

// Define sections that have separate management pages
$managed_sections = [
    'products' => [
        'buttons' => [
            [
                'text' => 'Manage Products',
                'url' => 'manage_products.php',
                'class' => 'btn-primary',
                'icon' => 'fas fa-cogs'
            ]
        ]
    ],
    'projects' => [
        'buttons' => [
            [
                'text' => 'Manage Projects',
                'url' => 'manage_projects.php',
                'class' => 'btn-primary',
                'icon' => 'fas fa-project-diagram'
            ]
        ]
    ],
	'review' => [
        'buttons' => [
            [
                'text' => 'Manage Reviews',
                'url' => 'manage_reviews.php',
                'class' => 'btn-primary',
                'icon' => 'fa-solid fa-comment'
            ]
        ]
    ],
	'about' => [
        'buttons' => [
            [
                'text' => 'Manage Statics',
                'url' => 'statistics.php',
                'class' => 'btn-primary',
                'icon' => 'fa-solid fa-chart-simple'
            ]
        ]
    ]
];

// Validate section ID
if (!array_key_exists($section_id, $available_sections)) {
    header('Location: update_content.php');
    exit;
}

// Check if current section has separate management
$is_managed_section = array_key_exists($section_id, $managed_sections);

// Fetch current section data
$stmt = $pdo->prepare("SELECT * FROM website_content WHERE section_name = ?");
$stmt->execute([$section_id]);
$section_data = $stmt->fetch();

// If section doesn't exist in database, create it
if (!$section_data) {
    $insert_stmt = $pdo->prepare("INSERT INTO website_content (section_name, title, content) VALUES (?, ?, ?)");
    $insert_stmt->execute([$section_id, $available_sections[$section_id], '']);
    $stmt->execute([$section_id]);
    $section_data = $stmt->fetch();
}

// Fetch features for Why Choose Us section
$features = [];
if ($section_id === 'why_choose') {
    $features = $pdo->query("SELECT * FROM why_choose_features ORDER BY display_order ASC")->fetchAll();
}

// Popular Font Awesome icons for features
$popular_icons = [
    'fas fa-shield-alt',
    'fas fa-bolt',
    'fas fa-headset',
    'fas fa-solar-panel',
    'fas fa-sun',
    'fas fa-battery-full',
    'fas fa-chart-line',
    'fas fa-award',
    'fas fa-certificate',
    'fas fa-rocket',
    'fas fa-gem',
    'fas fa-heart',
    'fas fa-star',
    'fas fa-thumbs-up',
    'fas fa-check-circle',
    'fas fa-lightbulb',
    'fas fa-cogs',
    'fas fa-tools',
    'fas fa-user-cog',
    'fas fa-hands-helping'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Update <?php echo $available_sections[$section_id]; ?> - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .preview-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .section-preview {
            border-left: 4px solid var(--primary-color, #007bff);
            padding-left: 15px;
        }
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }
        .management-buttons {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid var(--primary-color, #007bff);
        }
        .features-section {
            background: linear-gradient(135deg, #fff3e0 0%, #ffecb3 100%);
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border: 2px solid #ffe082;
        }
        .feature-card-admin {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color, #007bff);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .icon-preview {
            font-size: 2rem;
            color: var(--primary-color, #007bff);
            margin-bottom: 10px;
        }
        .feature-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
        }
        .feature-preview-card {
            text-align: center;
            padding: 20px;
        }
        .feature-preview-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <?php if ($is_managed_section): ?>
                    <i class="fas fa-cog me-2"></i>Manage <?php echo $available_sections[$section_id]; ?>
                <?php else: ?>
                    Update <?php echo $available_sections[$section_id]; ?> Section
                <?php endif; ?>
            </h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($upload_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $upload_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">      
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?php if ($is_managed_section): ?>
                                <i class="fas fa-cog me-2"></i>Manage <?php echo $available_sections[$section_id]; ?>
                            <?php else: ?>
                                <i class="fas fa-edit me-2"></i>Edit <?php echo $available_sections[$section_id]; ?> Content
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($is_managed_section): ?>
                            <!-- Management Buttons Section -->
                            <div class="management-buttons">
                                <h6 class="mb-3"><i class="fas fa-rocket me-2"></i>Quick Actions</h6>
                                <div class="row">
                                    <?php foreach ($managed_sections[$section_id]['buttons'] as $button): ?>
                                        <div class="col-12 mb-3">
                                            <a href="<?php echo $button['url']; ?>" class="btn <?php echo $button['class']; ?> w-100 py-3">
                                                <i class="<?php echo $button['icon']; ?> me-2"></i>
                                                <?php echo $button['text']; ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        This section content is automatically generated from your <?php echo strtolower($available_sections[$section_id]); ?> database.
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Features Management Section (Only for Why Choose Us) -->
                        <?php if ($section_id === 'why_choose'): ?>
                        <div class="features-section">
                            <h5 class="mb-4">
                                <i class="fas fa-list-alt me-2"></i>Manage Features
                                <small class="text-muted">(Minimum 3, Maximum 6 features)</small>
                            </h5>
                            
                            <form method="POST">
                                <?php foreach ($features as $index => $feature): ?>
                                <div class="feature-card-admin">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="features[<?php echo $feature['id']; ?>][display_order]" 
                                                   class="form-control" value="<?php echo $feature['display_order']; ?>" min="1" max="6">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Icon</label>
                                            <select name="features[<?php echo $feature['id']; ?>][icon_class]" class="form-select">
                                                <?php foreach ($popular_icons as $icon): ?>
                                                    <option value="<?php echo $icon; ?>" <?php echo $feature['icon_class'] === $icon ? 'selected' : ''; ?>>
                                                        <?php echo $icon; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="features[<?php echo $feature['id']; ?>][title]" 
                                                   class="form-control" value="<?php echo htmlspecialchars($feature['title']); ?>" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Description</label>
                                            <div class="input-group">
                                                <input type="text" name="features[<?php echo $feature['id']; ?>][description]" 
                                                       class="form-control" value="<?php echo htmlspecialchars($feature['description']); ?>" required>
                                                <?php if (count($features) > 3): ?>
                                                <button type="submit" name="delete_feature" value="<?php echo $feature['id']; ?>" 
                                                        class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this feature?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted">
                                            Preview: <i class="<?php echo $feature['icon_class']; ?> me-1"></i>
                                            <?php echo htmlspecialchars($feature['title']); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                                <!-- Add New Feature -->
                                <?php if (count($features) < 6): ?>
                                <div class="feature-card-admin" style="background: #e8f5e8;">
                                    <h6 class="mb-3"><i class="fas fa-plus-circle me-2"></i>Add New Feature</h6>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="form-label">Icon</label>
                                            <select name="new_feature_icon" class="form-select">
                                                <?php foreach ($popular_icons as $icon): ?>
                                                    <option value="<?php echo $icon; ?>"><?php echo $icon; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="new_feature_title" class="form-control" placeholder="Enter feature title">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Description</label>
                                            <input type="text" name="new_feature_description" class="form-control" placeholder="Enter feature description">
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Maximum of 6 features reached. Remove one to add a new feature.
                                </div>
                                <?php endif; ?>

                                <button type="submit" name="update_features" class="btn btn-success mt-3">
                                    <i class="fas fa-save me-2"></i>Update Features
                                </button>
                            </form>

                            <!-- Features Preview -->
                            <div class="feature-preview mt-4">
                                <h6 class="text-center mb-3">Features Preview</h6>
                                <div class="row">
                                    <?php 
                                    $preview_features = array_slice($features, 0, 3); // Show first 3 in preview
                                    foreach ($preview_features as $feature): 
                                    ?>
                                    <div class="col-md-4">
                                        <div class="feature-preview-card">
                                            <div class="feature-preview-icon">
                                                <i class="<?php echo $feature['icon_class']; ?>"></i>
                                            </div>
                                            <h6><?php echo htmlspecialchars($feature['title']); ?></h6>
                                            <small><?php echo htmlspecialchars($feature['description']); ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Main Content Form -->
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="section_name" value="<?php echo $section_id; ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Section Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($section_data['title'] ?? $available_sections[$section_id]); ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Section Content</label>
                                <textarea class="form-control" id="content" name="content" rows="4" 
                                          placeholder="Brief description that appears above the features..."><?php echo htmlspecialchars($section_data['content'] ?? ''); ?></textarea>
                                <div class="form-text">
                                    This text appears above the features section. Use line breaks for paragraphs.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Section Image (Optional)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <?php if (!empty($section_data['image_path'])): ?>
                                    <div class="mt-2">
                                        <p class="mb-1">Current Image:</p>
                                        <img src="../<?php echo $section_data['image_path']; ?>" 
                                             alt="Current section image" 
                                             class="image-preview">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Section Content
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?php if ($is_managed_section): ?>
                                <i class="fas fa-link me-2"></i>Quick Navigation
                            <?php else: ?>
                                <i class="fas fa-eye me-2"></i>Section Preview
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!$is_managed_section): ?>
                            <!-- Preview for regular sections -->
                            <div class="preview-section">
                                <h4 id="preview-title"><?php echo htmlspecialchars($section_data['title'] ?? $available_sections[$section_id]); ?></h4>
                                <div id="preview-content" class="section-preview">
                                    <?php 
                                    if (!empty($section_data['content'])) {
                                        echo nl2br(htmlspecialchars($section_data['content']));
                                    } else {
                                        echo '<p class="text-muted">Content preview will appear here...</p>';
                                    }
                                    ?>
                                </div>
                                
                                <!-- Features Preview in Sidebar (Only for Why Choose Us) -->
                                <?php if ($section_id === 'why_choose' && !empty($features)): ?>
                                <div class="feature-preview mt-3">
                                    <div class="row">
                                        <?php 
                                        $sidebar_features = array_slice($features, 0, 3);
                                        foreach ($sidebar_features as $feature): 
                                        ?>
                                        <div class="col-12 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-preview">
                                                    <i class="<?php echo $feature['icon_class']; ?>"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($feature['title']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($feature['description']); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($section_data['image_path'])): ?>
                                    <div class="mt-3">
                                        <img src="../<?php echo $section_data['image_path']; ?>" 
                                             alt="Section preview image" 
                                             class="img-fluid rounded">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <h6><i class="fas fa-list me-2"></i>Available Sections</h6>
                            <div class="list-group">
                                <?php foreach ($available_sections as $id => $name): ?>
                                    <a href="?section_id=<?php echo $id; ?>" 
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $id === $section_id ? 'active' : ''; ?>">
                                        <span>
                                            <?php if (array_key_exists($id, $managed_sections)): ?>
                                                <i class="fas fa-cog me-2"></i>
                                            <?php else: ?>
                                                <i class="fas fa-edit me-2"></i>
                                            <?php endif; ?>
                                            <?php echo $name; ?>
                                        </span>
                                        <?php if ($id === $section_id): ?>
                                            <i class="fas fa-chevron-right"></i>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview update for content sections
        document.addEventListener('DOMContentLoaded', function() {
            // Content section preview
            const titleInput = document.getElementById('title');
            const contentInput = document.getElementById('content');
            const previewTitle = document.getElementById('preview-title');
            const previewContent = document.getElementById('preview-content');
            
            if (titleInput && previewTitle) {
                titleInput.addEventListener('input', function() {
                    previewTitle.textContent = this.value;
                });
            }
            
            if (contentInput && previewContent) {
                contentInput.addEventListener('input', function() {
                    if (this.value.trim() === '') {
                        previewContent.innerHTML = '<p class="text-muted">Content preview will appear here...</p>';
                    } else {
                        previewContent.innerHTML = this.value.replace(/\n/g, '<br>');
                    }
                });
            }
            
            // Image preview
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            let imgPreview = document.querySelector('.preview-section img');
                            if (!imgPreview) {
                                imgPreview = document.createElement('img');
                                imgPreview.className = 'img-fluid rounded mt-3';
                                document.querySelector('.preview-section').appendChild(imgPreview);
                            }
                            imgPreview.src = e.target.result;
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        });
    </script>
</body>
</html>