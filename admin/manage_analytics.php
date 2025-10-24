<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

	$stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings WHERE setting_name LIKE 'google_analytics_%'");
	$analytics_settings = [];
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$analytics_settings[$row['setting_name']] = $row['setting_value'];
	}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_analytics'])) {
        $analytics_id = trim($_POST['google_analytics_id']);
        $status = isset($_POST['google_analytics_status']) ? 'enabled' : 'disabled';
        $anonymize_ip = isset($_POST['google_analytics_anonymize_ip']) ? '1' : '0';
        $enhanced_link_attribution = isset($_POST['google_analytics_enhanced_link_attribution']) ? '1' : '0';
        $cross_domain_tracking = isset($_POST['google_analytics_cross_domain_tracking']) ? '1' : '0';
        $remarketing = isset($_POST['google_analytics_remarketing']) ? '1' : '0';
        
        // Update all analytics settings
        $settings_to_update = [
            'google_analytics_id' => $analytics_id,
            'google_analytics_status' => $status,
            'google_analytics_anonymize_ip' => $anonymize_ip,
            'google_analytics_enhanced_link_attribution' => $enhanced_link_attribution,
            'google_analytics_cross_domain_tracking' => $cross_domain_tracking,
            'google_analytics_remarketing' => $remarketing
        ];
        
        foreach ($settings_to_update as $name => $value) {
            $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
            $stmt->execute([$value, $name]);
        }
        
        $success = "Google Analytics settings updated successfully!";
        
        // Refresh settings
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }
}

// Fetch current analytics settings
$stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings WHERE setting_name LIKE 'google_analytics_%'");
$analytics_settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $analytics_settings[$row['setting_name']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Analytics - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
        .feature-card {
            border-left: 4px solid #007bff;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        .instructions {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .preview-box {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-chart-line"></i> Google Analytics Configuration</h1>
                <p class="text-muted">Configure and manage Google Analytics tracking for your website</p>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- Configuration Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Analytics Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- Basic Settings -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Basic Configuration</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Google Analytics Tracking ID</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="google_analytics_id" 
                                           value="<?php echo htmlspecialchars($analytics_settings['google_analytics_id'] ?? ''); ?>"
                                           placeholder="G-XXXXXXXXXX or UA-XXXXXXXXX-X">
                                    <div class="form-text">
                                        Enter your Google Analytics 4 (G-) or Universal Analytics (UA-) Tracking ID
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="google_analytics_status" 
                                           id="analyticsStatus"
                                           <?php echo ($analytics_settings['google_analytics_status'] ?? 'disabled') === 'enabled' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="analyticsStatus">
                                        Enable Google Analytics Tracking
                                    </label>
                                </div>
                            </div>

                            <!-- Privacy & Compliance -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Privacy & Compliance</h6>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="google_analytics_anonymize_ip" 
                                           id="anonymizeIp"
                                           <?php echo ($analytics_settings['google_analytics_anonymize_ip'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="anonymizeIp">
                                        Anonymize IP Addresses
                                    </label>
                                    <div class="form-text">
                                        Recommended for GDPR compliance. Masks the last octet of IP addresses.
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="google_analytics_enhanced_link_attribution" 
                                           id="enhancedLinks"
                                           <?php echo ($analytics_settings['google_analytics_enhanced_link_attribution'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enhancedLinks">
                                        Enhanced Link Attribution
                                    </label>
                                    <div class="form-text">
                                        Improves the accuracy of your In-Page Analytics report.
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Features -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Advanced Features</h6>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="google_analytics_cross_domain_tracking" 
                                           id="crossDomain"
                                           <?php echo ($analytics_settings['google_analytics_cross_domain_tracking'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="crossDomain">
                                        Cross-Domain Tracking
                                    </label>
                                    <div class="form-text">
                                        Enable if you track users across multiple domains.
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="google_analytics_remarketing" 
                                           id="remarketing"
                                           <?php echo ($analytics_settings['google_analytics_remarketing'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="remarketing">
                                        Remarketing & Advertising Features
                                    </label>
                                    <div class="form-text">
                                        Enable Google Analytics advertising features for remarketing.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" name="update_analytics" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Analytics Settings
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Code Preview -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-code"></i> Tracking Code Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">This is the tracking code that will be added to your website:</p>
                        <div class="preview-box">
                            &lt;!-- Google Analytics --&gt;<br>
                            &lt;script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($analytics_settings['google_analytics_id'] ?? 'G-XXXXXXXXXX'); ?>"&gt;&lt;/script&gt;<br>
                            &lt;script&gt;<br>
                            &nbsp;&nbsp;window.dataLayer = window.dataLayer || [];<br>
                            &nbsp;&nbsp;function gtag(){dataLayer.push(arguments);}<br>
                            &nbsp;&nbsp;gtag('js', new Date());<br>
                            &nbsp;&nbsp;gtag('config', '<?php echo htmlspecialchars($analytics_settings['google_analytics_id'] ?? 'G-XXXXXXXXXX'); ?>', {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;anonymize_ip: <?php echo ($analytics_settings['google_analytics_anonymize_ip'] ?? '1') === '1' ? 'true' : 'false'; ?>,<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;allow_google_signals: <?php echo ($analytics_settings['google_analytics_remarketing'] ?? '0') === '1' ? 'true' : 'false'; ?>,<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;allow_ad_personalization_signals: <?php echo ($analytics_settings['google_analytics_remarketing'] ?? '0') === '1' ? 'true' : 'false'; ?><br>
                            &nbsp;&nbsp;});<br>
                            &lt;/script&gt;
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Analytics Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Tracking Status:</span>
                            <span class="badge bg-<?php echo ($analytics_settings['google_analytics_status'] ?? 'disabled') === 'enabled' ? 'success' : 'danger'; ?> status-badge">
                                <?php echo ($analytics_settings['google_analytics_status'] ?? 'disabled') === 'enabled' ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Tracking ID:</span>
                            <span class="text-muted">
                                <?php echo !empty($analytics_settings['google_analytics_id']) ? htmlspecialchars($analytics_settings['google_analytics_id']) : 'Not Set'; ?>
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span>IP Anonymization:</span>
                            <span class="badge bg-<?php echo ($analytics_settings['google_analytics_anonymize_ip'] ?? '1') === '1' ? 'success' : 'warning'; ?> status-badge">
                                <?php echo ($analytics_settings['google_analytics_anonymize_ip'] ?? '1') === '1' ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle"></i> Setup Instructions
                        </h5>
                    </div>
                    <div class="card-body">
                        <ol class="small">
                            <li>Create a Google Analytics 4 property</li>
                            <li>Get your Measurement ID (starts with G-)</li>
                            <li>Enter the ID in the form</li>
                            <li>Enable tracking</li>
                            <li>Save settings</li>
                            <li>Verify tracking in Google Analytics real-time report</li>
                        </ol>
                        
                        <div class="mt-3">
                            <a href="https://analytics.google.com/" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-external-link-alt"></i> Go to Google Analytics
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GDPR Compliance -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shield-alt"></i> Privacy Compliance
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">
                            <i class="fas fa-info-circle text-primary"></i> 
                            Ensure your website has a proper privacy policy and cookie consent banner that informs users about analytics tracking.
                        </p>
                        
                        <div class="mt-2">
                            <a href="../privacy-policy.php" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-file-alt"></i> View Privacy Policy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dynamic preview update
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', updatePreview);
        });
        
        function updatePreview() {
            // This would update the preview box in a real implementation
            console.log('Settings updated - preview would refresh');
        }
        
        // Toggle advanced options based on status
        const analyticsStatus = document.getElementById('analyticsStatus');
        const advancedOptions = document.querySelectorAll('input[name^="google_analytics_"]:not([name="google_analytics_status"])');
        
        function toggleAdvancedOptions() {
            advancedOptions.forEach(option => {
                option.disabled = !analyticsStatus.checked;
            });
        }
        
        analyticsStatus.addEventListener('change', toggleAdvancedOptions);
        toggleAdvancedOptions(); // Initial state
    </script>
</body>
</html>