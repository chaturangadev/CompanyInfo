<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Fetch current legal content
$stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings WHERE setting_name IN ('privacy_policy_content', 'terms_of_service_content', 'privacy_last_updated', 'terms_last_updated', 'privacy_meta_title', 'privacy_meta_description', 'terms_meta_title', 'terms_meta_description')");
$legal_settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $legal_settings[$row['setting_name']] = $row['setting_value'];
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_privacy'])) {
        $privacy_content = $_POST['privacy_policy_content'] ?? '';
        $privacy_meta_title = $_POST['privacy_meta_title'] ?? '';
        $privacy_meta_description = $_POST['privacy_meta_description'] ?? '';
        $current_date = date('Y-m-d H:i:s');
        
        try {
            // Check if setting exists, if not insert it
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'privacy_policy_content'");
            $check_stmt->execute();
            $exists = $check_stmt->fetchColumn();
            
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'privacy_policy_content'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('privacy_policy_content', ?)");
            }
            $stmt->execute([$privacy_content]);
            
            // Update privacy meta title
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'privacy_meta_title'");
            $check_stmt->execute();
            $meta_title_exists = $check_stmt->fetchColumn();
            
            if ($meta_title_exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'privacy_meta_title'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('privacy_meta_title', ?)");
            }
            $stmt->execute([$privacy_meta_title]);
            
            // Update privacy meta description
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'privacy_meta_description'");
            $check_stmt->execute();
            $meta_desc_exists = $check_stmt->fetchColumn();
            
            if ($meta_desc_exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'privacy_meta_description'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('privacy_meta_description', ?)");
            }
            $stmt->execute([$privacy_meta_description]);
            
            // Update timestamp
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'privacy_last_updated'");
            $check_stmt->execute();
            $timestamp_exists = $check_stmt->fetchColumn();
            
            if ($timestamp_exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'privacy_last_updated'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('privacy_last_updated', ?)");
            }
            $stmt->execute([$current_date]);
            
            $success = "Privacy Policy updated successfully!";
            
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['update_terms'])) {
        $terms_content = $_POST['terms_of_service_content'] ?? '';
        $terms_meta_title = $_POST['terms_meta_title'] ?? '';
        $terms_meta_description = $_POST['terms_meta_description'] ?? '';
        $current_date = date('Y-m-d H:i:s');
        
        try {
            // Check if setting exists, if not insert it
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'terms_of_service_content'");
            $check_stmt->execute();
            $exists = $check_stmt->fetchColumn();
            
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'terms_of_service_content'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('terms_of_service_content', ?)");
            }
            $stmt->execute([$terms_content]);
            
            // Update terms meta title
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'terms_meta_title'");
            $check_stmt->execute();
            $meta_title_exists = $check_stmt->fetchColumn();
            
            if ($meta_title_exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'terms_meta_title'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('terms_meta_title', ?)");
            }
            $stmt->execute([$terms_meta_title]);
            
            // Update terms meta description
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'terms_meta_description'");
            $check_stmt->execute();
            $meta_desc_exists = $check_stmt->fetchColumn();
            
            if ($meta_desc_exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'terms_meta_description'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('terms_meta_description', ?)");
            }
            $stmt->execute([$terms_meta_description]);
            
            // Update timestamp
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = 'terms_last_updated'");
            $check_stmt->execute();
            $timestamp_exists = $check_stmt->fetchColumn();
            
            if ($timestamp_exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'terms_last_updated'");
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES ('terms_last_updated', ?)");
            }
            $stmt->execute([$current_date]);
            
            $success = "Terms of Service updated successfully!";
            
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
    
    // Refresh settings after update
    $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings WHERE setting_name IN ('privacy_policy_content', 'terms_of_service_content', 'privacy_last_updated', 'terms_last_updated', 'privacy_meta_title', 'privacy_meta_description', 'terms_meta_title', 'terms_meta_description')");
    $legal_settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $legal_settings[$row['setting_name']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Legal Content - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .legal-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
        }
        .legal-tabs .nav-link.active {
            background: none;
            border-bottom: 3px solid #007bff;
            color: #007bff;
        }
        .last-updated {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .editor-toolbar {
            background: #f8f9fa;
            padding: 0.5rem;
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        .seo-character-count {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: right;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-gavel"></i> Manage Legal Content</h1>
                <p class="text-muted">Update your Privacy Policy and Terms of Service content</p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs legal-tabs card-header-tabs" id="legalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="privacy-tab" data-bs-toggle="tab" data-bs-target="#privacy" type="button" role="tab">
                            <i class="fas fa-shield-alt"></i> Privacy Policy
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab">
                            <i class="fas fa-file-contract"></i> Terms of Service
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="legalTabsContent">
                    <!-- Privacy Policy Tab -->
                    <div class="tab-pane fade show active" id="privacy" role="tabpanel">
                        <form method="POST">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Privacy Policy Meta Title</label>
                                        <input type="text" class="form-control" name="privacy_meta_title" 
                                               value="<?php echo htmlspecialchars($legal_settings['privacy_meta_title'] ?? 'Privacy Policy - ' . ($settings['company_name'] ?? '')); ?>"
                                               maxlength="60" onkeyup="updateCharacterCount(this, 'privacy_title_count')">
                                        <div class="seo-character-count" id="privacy_title_count">
                                            <?php echo 60 - strlen($legal_settings['privacy_meta_title'] ?? 'Privacy Policy - ' . ($settings['company_name'] ?? '')); ?> characters remaining
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Privacy Policy Meta Description</label>
                                        <textarea class="form-control" name="privacy_meta_description" rows="2" 
                                                  maxlength="160" onkeyup="updateCharacterCount(this, 'privacy_desc_count')"><?php echo htmlspecialchars($legal_settings['privacy_meta_description'] ?? 'Read our Privacy Policy to understand how we collect, use, and protect your personal information.'); ?></textarea>
                                        <div class="seo-character-count" id="privacy_desc_count">
                                            <?php echo 160 - strlen($legal_settings['privacy_meta_description'] ?? 'Read our Privacy Policy to understand how we collect, use, and protect your personal information.'); ?> characters remaining
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Privacy Policy Content</label>
                                <div class="editor-toolbar">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Use HTML tags for formatting. You can use &lt;p&gt;, &lt;h2&gt;, &lt;h3&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;
                                    </small>
                                </div>
                                <textarea class="form-control" name="privacy_policy_content" rows="15" placeholder="Enter your Privacy Policy content here..."><?php echo htmlspecialchars($legal_settings['privacy_policy_content'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="last-updated">
                                    <?php if (!empty($legal_settings['privacy_last_updated'])): ?>
                                        Last updated: <?php echo date('F j, Y \a\t g:i A', strtotime($legal_settings['privacy_last_updated'])); ?>
                                    <?php else: ?>
                                        Never updated
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="../privacy-policy.php" target="_blank" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                    <button type="submit" name="update_privacy" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Privacy Policy
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Terms of Service Tab -->
                    <div class="tab-pane fade" id="terms" role="tabpanel">
                        <form method="POST">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Terms of Service Meta Title</label>
                                        <input type="text" class="form-control" name="terms_meta_title" 
                                               value="<?php echo htmlspecialchars($legal_settings['terms_meta_title'] ?? 'Terms of Service - ' . ($settings['company_name'] ?? '')); ?>"
                                               maxlength="60" onkeyup="updateCharacterCount(this, 'terms_title_count')">
                                        <div class="seo-character-count" id="terms_title_count">
                                            <?php echo 60 - strlen($legal_settings['terms_meta_title'] ?? 'Terms of Service - ' . ($settings['company_name'] ?? '')); ?> characters remaining
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Terms of Service Meta Description</label>
                                        <textarea class="form-control" name="terms_meta_description" rows="2" 
                                                  maxlength="160" onkeyup="updateCharacterCount(this, 'terms_desc_count')"><?php echo htmlspecialchars($legal_settings['terms_meta_description'] ?? 'Read our Terms of Service to understand the rules and guidelines for using our website and services.'); ?></textarea>
                                        <div class="seo-character-count" id="terms_desc_count">
                                            <?php echo 160 - strlen($legal_settings['terms_meta_description'] ?? 'Read our Terms of Service to understand the rules and guidelines for using our website and services.'); ?> characters remaining
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Terms of Service Content</label>
                                <div class="editor-toolbar">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Use HTML tags for formatting. You can use &lt;p&gt;, &lt;h2&gt;, &lt;h3&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;
                                    </small>
                                </div>
                                <textarea class="form-control" name="terms_of_service_content" rows="15" placeholder="Enter your Terms of Service content here..."><?php echo htmlspecialchars($legal_settings['terms_of_service_content'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="last-updated">
                                    <?php if (!empty($legal_settings['terms_last_updated'])): ?>
                                        Last updated: <?php echo date('F j, Y \a\t g:i A', strtotime($legal_settings['terms_last_updated'])); ?>
                                    <?php else: ?>
                                        Never updated
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="../terms-of-service.php" target="_blank" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                    <button type="submit" name="update_terms" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Terms of Service
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCharacterCount(input, countElementId) {
            const maxLength = parseInt(input.getAttribute('maxlength'));
            const currentLength = input.value.length;
            const remaining = maxLength - currentLength;
            const countElement = document.getElementById(countElementId);
            
            countElement.textContent = remaining + ' characters remaining';
            
            // Add warning colors
            if (remaining < 10) {
                countElement.style.color = '#dc3545';
            } else if (remaining < 20) {
                countElement.style.color = '#ffc107';
            } else {
                countElement.style.color = '#6c757d';
            }
        }
    </script>
</body>
</html>