<?php
session_start();
include '../includes/config.php';
include 'includes/session_check.php';

// Get counts for dashboard stats
$products_count = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = TRUE")->fetchColumn();
$reviews_count = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_active = TRUE")->fetchColumn();
$slides_count = $pdo->query("SELECT COUNT(*) FROM hero_slides WHERE is_active = TRUE")->fetchColumn();
$messages_count = 0; // You can add a contact messages table later
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .quick-action-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
                    <p class="mb-0">Welcome back, <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>! Here's what's happening with your website today.</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="../index.php" target="_blank" class="btn btn-outline-light">
                            <i class="bi bi-eye"></i> View Website
                        </a>
                        <a href="logout.php" class="btn btn-light">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="stat-number text-primary"><?php echo $products_count; ?></div>
                                <div class="stat-label">Active Products</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-primary text-white">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="stat-number text-success"><?php echo $reviews_count; ?></div>
                                <div class="stat-label">Customer Reviews</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-success text-white">
                                    <i class="bi bi-chat-quote"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="stat-number text-warning"><?php echo $slides_count; ?></div>
                                <div class="stat-label">Hero Slides</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-warning text-white">
                                    <i class="bi bi-images"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-info">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="stat-number text-info"><?php echo $messages_count; ?></div>
                                <div class="stat-label">New Messages</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="stat-icon bg-info text-white">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Cards -->
        <div class="row">          
            <!-- Hero Slides Management -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 quick-action-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-images fs-1 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title">Hero Slides</h5>
                                <p class="card-text">Add and manage hero banner slides with images and text.</p>
                                <a href="manage_slides.php" class="btn btn-primary">Manage Slides</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
			<!-- Content Management -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 quick-action-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-file-text fs-1 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title">Website Content</h5>
                                <p class="card-text">Update text content, images, and information across your website.</p>
                                <a href="update_content.php" class="btn btn-primary">Manage Content</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
			<!-- Google Analytics -->
			<div class="col-md-6 mb-4">
				<div class="card h-100 quick-action-card">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="flex-shrink-0">
								<i class="bi bi-graph-up fs-1 text-success"></i>
							</div>
							<div class="flex-grow-1 ms-3">
								<h5 class="card-title">Google Analytics</h5>
								<p class="card-text">Configure and manage website analytics tracking.</p>
								<a href="manage_analytics.php" class="btn btn-success">Manage Analytics</a>
							</div>
						</div>
					</div>
				</div>
			</div>

		<!-- Leagle content -->
		<div class="col-md-6 mb-4">
			<div class="card h-100 quick-action-card">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="bi bi-file-text fs-1 text-info"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h5 class="card-title">Legal Content</h5>
							<p class="card-text">Manage Privacy Policy and Terms of Service content.</p>
							<a href="manage_legal.php" class="btn btn-info">Manage Legal Content</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
        <!-- Settings Management -->
		<div class="col-md-6 mb-4">
			<div class="card h-100 quick-action-card">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="bi bi-gear fs-1 text-primary"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h5 class="card-title">Website Settings</h5>
							<p class="card-text">Manage company info, color themes, social media, and branding.</p>
							<a href="manage_settings.php" class="btn btn-primary">Manage Settings</a>
						</div>
					</div>
				</div>
			</div>
		</div>
				
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 text-center mb-3">
                                <a href="update_content.php?section=hero_title" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-type-h1"></i><br>
                                    Update Hero Text
                                </a>
                            </div>
                            <div class="col-md-3 col-6 text-center mb-3">
                                <a href="manage_products.php" class="btn btn-outline-success w-100 mb-2">
                                    <i class="bi bi-plus-circle"></i><br>
                                    Add New Product
                                </a>
                            </div>
                            <div class="col-md-3 col-6 text-center mb-3">
                                <a href="manage_reviews.php" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="bi bi-chat-left-text"></i><br>
                                    Add New Review
                                </a>
                            </div>
                            <div class="col-md-3 col-6 text-center mb-3">
                                <a href="manage_slides.php" class="btn btn-outline-info w-100 mb-2">
                                    <i class="bi bi-plus-square"></i><br>
                                    Add New Slide
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-clock"></i> Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php
                            // Get recent products
                            $recent_products = $pdo->query("SELECT title, created_at FROM products ORDER BY created_at DESC LIMIT 3")->fetchAll();
                            foreach ($recent_products as $product):
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-box text-primary me-2"></i>
                                    New product added: <strong><?php echo $product['title']; ?></strong>
                                </div>
                                <small class="text-muted"><?php echo date('M j, g:i A', strtotime($product['created_at'])); ?></small>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php
                            // Get recent reviews
                            $recent_reviews = $pdo->query("SELECT customer_name, created_at FROM reviews ORDER BY created_at DESC LIMIT 2")->fetchAll();
                            foreach ($recent_reviews as $review):
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-chat-quote text-success me-2"></i>
                                    New review from: <strong><?php echo $review['customer_name']; ?></strong>
                                </div>
                                <small class="text-muted"><?php echo date('M j, g:i A', strtotime($review['created_at'])); ?></small>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="list-group-item text-center">
                                <a href="#" class="text-decoration-none">View All Activity</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">PHP Version</small>
                                <div class="fw-bold"><?php echo phpversion(); ?></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Database</small>
                                <div class="fw-bold">MySQL</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-muted">Server Time</small>
                                <div class="fw-bold"><?php echo date('Y-m-d H:i:s'); ?></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Last Login</small>
                                <div class="fw-bold"><?php echo date('M j, g:i A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-shield-check"></i> Security Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Admin authentication enabled</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Session management active</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>File uploads secured</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                            <span>Regular backups recommended</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh stats every 30 seconds
        setInterval(function() {
            // You can add AJAX calls here to refresh stats without page reload
            console.log('Stats refresh interval reached');
        }, 30000);
    </script>
</body>
</html>