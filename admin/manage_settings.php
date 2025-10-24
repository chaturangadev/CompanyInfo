<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Fetch all palettes
$palettes = $pdo->query("SELECT * FROM color_palettes ORDER BY is_active DESC, name ASC")->fetchAll();

// Fetch all settings first
$stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Company Info update
    if (isset($_POST['update_company_info'])) {
        try {
            $pdo->beginTransaction();
            
            // Update company settings from the form
            $companySettings = [
                'company_name' => $_POST['company_name'] ?? '',
                'company_email' => $_POST['company_email'] ?? '',
                'company_phone' => $_POST['company_phone'] ?? '',
                'company_address' => $_POST['company_address'] ?? '',
                'company_working_hours' => $_POST['company_working_hours'] ?? '',
                'contact_form_email' => $_POST['contact_form_email'] ?? ''
            ];
            
            foreach ($companySettings as $name => $value) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->execute([$value, $name]);
            }
            
            $pdo->commit();
            $success = "Company information updated successfully!";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating company information: " . $e->getMessage();
        }
        
        // Refresh settings
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }
    
    // Handle SEO Settings update
    if (isset($_POST['update_seo_settings'])) {
        try {
            $pdo->beginTransaction();
            
            // Update SEO settings from the form
            $seoSettings = [
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'meta_keywords' => $_POST['meta_keywords'] ?? '',
                'google_search_console' => $_POST['google_search_console'] ?? '',
                'og_image' => $_POST['og_image'] ?? '',
                'og_title' => $_POST['og_title'] ?? '',
                'og_description' => $_POST['og_description'] ?? '',
                'structured_data_type' => $_POST['structured_data_type'] ?? '',
                'structured_data_business_name' => $_POST['structured_data_business_name'] ?? '',
                'structured_data_address' => $_POST['structured_data_address'] ?? '',
                'structured_data_phone' => $_POST['structured_data_phone'] ?? '',
                'structured_data_email' => $_POST['structured_data_email'] ?? '',
                'structured_data_opening_hours' => $_POST['structured_data_opening_hours'] ?? '',
                'sitemap_auto_update' => isset($_POST['sitemap_auto_update']) ? '1' : '0',
                'robots_txt_content' => $_POST['robots_txt_content'] ?? ''
            ];
            
            foreach ($seoSettings as $name => $value) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->execute([$value, $name]);
            }
            
            // Handle robots.txt file update
            if (!empty($_POST['robots_txt_content'])) {
                file_put_contents('../robots.txt', $_POST['robots_txt_content']);
            }
            
            $pdo->commit();
            $success = "SEO settings updated successfully!";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating SEO settings: " . $e->getMessage();
        }
        
        // Refresh settings
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }
    
    // Handle Social Media update
    if (isset($_POST['update_social_media'])) {
        try {
            $pdo->beginTransaction();
            
            // Update social media settings from the form
            $socialSettings = [
                'social_facebook' => $_POST['social_facebook'] ?? '',
                'social_x' => $_POST['social_x'] ?? '',
                'social_instagram' => $_POST['social_instagram'] ?? '',
                'social_linkedin' => $_POST['social_linkedin'] ?? '',
                'social_youtube' => $_POST['social_youtube'] ?? '',
                'social_whatsapp' => $_POST['social_whatsapp'] ?? ''
            ];
            
            foreach ($socialSettings as $name => $value) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->execute([$value, $name]);
            }
            
            $pdo->commit();
            $success = "Social media links updated successfully!";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating social media links: " . $e->getMessage();
        }
        
        // Refresh settings
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }
    
    // Handle Branding update
    if (isset($_POST['update_branding'])) {
        try {
            $pdo->beginTransaction();
            
            // Update branding settings
            $brandingSettings = [
                'contact_map_embed' => $_POST['contact_map_embed'] ?? ''
            ];
            
            foreach ($brandingSettings as $name => $value) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->execute([$value, $name]);
            }
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $upload_dir = '../assets/images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $logo_name = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $target_file = $upload_dir . $logo_name;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                    $logo_path = 'assets/images/' . $logo_name;
                    $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'website_logo'");
                    $stmt->execute([$logo_path]);
                }
            }
            
            // Handle favicon upload
            if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === 0) {
                $upload_dir = '../assets/images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $favicon_name = 'favicon_' . time() . '.' . pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
                $target_file = $upload_dir . $favicon_name;
                
                if (move_uploaded_file($_FILES['favicon']['tmp_name'], $target_file)) {
                    $favicon_path = 'assets/images/' . $favicon_name;
                    $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'website_favicon'");
                    $stmt->execute([$favicon_path]);
                }
            }
            
            $pdo->commit();
            $success = "Branding assets updated successfully!";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating branding assets: " . $e->getMessage();
        }
        
        // Refresh settings
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }
    
    // Handle Email Configuration update
    if (isset($_POST['update_email_config'])) {
        try {
            $pdo->beginTransaction();
            
            // Update email settings from the form
            $emailSettings = [
                'smtp_host' => $_POST['smtp_host'] ?? '',
                'smtp_port' => $_POST['smtp_port'] ?? '',
                'smtp_username' => $_POST['smtp_username'] ?? '',
                'smtp_password' => $_POST['smtp_password'] ?? '',
                'smtp_encryption' => $_POST['smtp_encryption'] ?? '',
                'from_email' => $_POST['from_email'] ?? '',
                'from_name' => $_POST['from_name'] ?? '',
                'admin_notification_email' => $_POST['admin_notification_email'] ?? ''
            ];
            
            foreach ($emailSettings as $name => $value) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->execute([$value, $name]);
            }
            
            $pdo->commit();
            $success = "Email configuration updated successfully!";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating email configuration: " . $e->getMessage();
        }
        
        // Refresh settings
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }
	
	// Handle WhatsApp Configuration update
	if (isset($_POST['update_whatsapp_config'])) {
		try {
			$pdo->beginTransaction();
			
			// Update WhatsApp settings from the form
			$whatsappSettings = [
				'whatsapp_number' => $_POST['whatsapp_number'] ?? '',
				'whatsapp_welcome_message' => $_POST['whatsapp_welcome_message'] ?? '',
				'whatsapp_button_position' => $_POST['whatsapp_button_position'] ?? 'bottom-right',
				'whatsapp_button_style' => $_POST['whatsapp_button_style'] ?? 'floating',
				'whatsapp_enabled' => isset($_POST['whatsapp_enabled']) ? '1' : '0',
				'whatsapp_show_desktop' => isset($_POST['whatsapp_show_desktop']) ? '1' : '0',
				'whatsapp_show_mobile' => isset($_POST['whatsapp_show_mobile']) ? '1' : '0'
			];
			
			foreach ($whatsappSettings as $name => $value) {
				$stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
				$stmt->execute([$value, $name]);
			}
			
			$pdo->commit();
			$success = "WhatsApp configuration updated successfully!";
			
		} catch (Exception $e) {
			$pdo->rollBack();
			$error = "Error updating WhatsApp configuration: " . $e->getMessage();
		}
		
		// Refresh settings
		$stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
		$settings = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$settings[$row['setting_name']] = $row['setting_value'];
		}
	}
    
    // Handle palette activation
    if (isset($_POST['activate_palette'])) {
        $palette_id = $_POST['palette_id'];
        
        try {
            $pdo->beginTransaction();
            
            // Deactivate all palettes
            $stmt = $pdo->prepare("UPDATE color_palettes SET is_active = FALSE");
            $stmt->execute();
            
            // Activate selected palette
            $stmt = $pdo->prepare("UPDATE color_palettes SET is_active = TRUE WHERE id = ?");
            $stmt->execute([$palette_id]);
            
            // Update current palette setting
            $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = 'current_color_palette'");
            $stmt->execute([$palette_id]);
            
            $pdo->commit();
            $success = "Color palette activated successfully!";
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error activating palette: " . $e->getMessage();
        }
        
        // Refresh palettes
        $palettes = $pdo->query("SELECT * FROM color_palettes ORDER BY is_active DESC, name ASC")->fetchAll();
    }
    
    // Handle adding new palette
    if (isset($_POST['add_palette'])) {
        $name = $_POST['name'] ?? '';
        $primary_color = $_POST['primary_color'] ?? '';
        $secondary_color = $_POST['secondary_color'] ?? '';
        $accent_color = $_POST['accent_color'] ?? '';
        $background_color = $_POST['background_color'] ?? '';
        $text_color = $_POST['text_color'] ?? '';
        $light_color = $_POST['light_color'] ?? '';
        
        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO color_palettes (name, primary_color, secondary_color, accent_color, background_color, text_color, light_color) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $primary_color, $secondary_color, $accent_color, $background_color, $text_color, $light_color]);
                $success = "Custom palette added successfully!";
                
                // Refresh palettes
                $palettes = $pdo->query("SELECT * FROM color_palettes ORDER BY is_active DESC, name ASC")->fetchAll();
            } catch (PDOException $e) {
                $error = "Error adding palette: " . $e->getMessage();
            }
        } else {
            $error = "Please enter a theme name";
        }
    }
}

// Read robots.txt content
$robots_content = file_exists('../robots.txt') ? file_get_contents('../robots.txt') : "User-agent: *\nDisallow: /admin/\nDisallow: /includes/\nSitemap: " . (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/sitemap.xml";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .settings-section {
            margin-bottom: 2rem;
        }
        .palette-card {
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .palette-card.active {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .palette-card:hover {
            transform: translateY(-2px);
        }
        .color-swatch {
            height: 40px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .palette-preview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3px;
            margin-bottom: 10px;
        }
        .active-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #007bff;
            font-weight: 600;
        }
        .tab-save-btn {
            margin-top: 1rem;
        }
        .password-toggle {
            cursor: pointer;
        }
        /* WhatsApp Preview in Admin */
        .whatsapp-float-preview {
            width: 60px;
            height: 60px;
            background: #25D366;
            color: white;
            border: none;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
            animation: whatsapp-pulse 2s infinite;
        }

        .whatsapp-float-preview:hover {
            background: #128C7E;
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
            animation: none;
        }
        
        .seo-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
        }
        .seo-preview-title {
            color: #1a0dab;
            font-size: 18px;
            margin-bottom: 5px;
            cursor: pointer;
        }
        .seo-preview-url {
            color: #006621;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .seo-preview-description {
            color: #545454;
            font-size: 14px;
            line-height: 1.4;
        }
        .char-count {
            font-size: 12px;
            color: #6c757d;
        }
        .char-count.warning {
            color: #ffc107;
        }
        .char-count.danger {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h1><i class="fas fa-cogs"></i> Website Settings</h1>
        
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

        <!-- Settings Tabs -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">
                    <i class="fas fa-building"></i> Company Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                    <i class="fas fa-search"></i> SEO Settings
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                    <i class="fas fa-share-alt"></i> Social Media
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab">
                    <i class="fas fa-paint-brush"></i> Branding
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                    <i class="fas fa-envelope"></i> Email Config
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp" type="button" role="tab">
                    <i class="fab fa-whatsapp"></i> Whatsapp
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab">
                    <i class="fas fa-palette"></i> Color Themes
                </button>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabsContent">
            
            <!-- Company Information Tab -->
            <div class="tab-pane fade show active" id="company" role="tabpanel">
                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-building"></i> Company Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company Name *</label>
                                        <input type="text" name="company_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['company_name'] ?? 'Solar Energy Solutions'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email Address *</label>
                                        <input type="email" name="company_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['company_email'] ?? 'info@solarcompany.com'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="company_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['company_phone'] ?? '+1 (555) 123-4567'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="company_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['company_address'] ?? '123 Solar Street, Energy City, EC 12345'); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Working Hours</label>
                                        <input type="text" name="company_working_hours" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['company_working_hours'] ?? 'Mon-Fri: 9AM-6PM'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact Form Email *</label>
                                        <input type="email" name="contact_form_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['contact_form_email'] ?? 'info@solarcompany.com'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-save-btn">
                        <button type="submit" name="update_company_info" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Company Information
                        </button>
                    </div>
                </form>
            </div>

            <!-- SEO Settings Tab -->
            <div class="tab-pane fade" id="seo" role="tabpanel">
                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-search"></i> SEO Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Configure your website's search engine optimization settings.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Meta Tags</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title *</label>
                                        <input type="text" name="meta_title" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['meta_title'] ?? $settings['company_name'] ?? 'Solar Energy Solutions'); ?>" 
                                               maxlength="60" required
                                               onkeyup="updateSeoPreview()">
                                        <div class="char-count" id="title-count">60 characters remaining</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description *</label>
                                        <textarea name="meta_description" class="form-control" rows="3" 
                                                  maxlength="160" required
                                                  onkeyup="updateSeoPreview()"><?php echo htmlspecialchars($settings['meta_description'] ?? 'Professional solar energy solutions for residential and commercial properties. Save money with renewable energy.'); ?></textarea>
                                        <div class="char-count" id="description-count">160 characters remaining</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['meta_keywords'] ?? 'solar energy, renewable energy, solar panels, solar installation'); ?>">
                                        <small class="text-muted">Comma separated keywords</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Search Engine Preview</h6>
                                    <div class="seo-preview">
                                        <div class="seo-preview-title" id="preview-title">
                                            <?php echo htmlspecialchars($settings['meta_title'] ?? $settings['company_name'] ?? 'Solar Energy Solutions'); ?>
                                        </div>
                                        <div class="seo-preview-url">
                                            <?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']; ?>
                                        </div>
                                        <div class="seo-preview-description" id="preview-description">
                                            <?php echo htmlspecialchars($settings['meta_description'] ?? 'Professional solar energy solutions for residential and commercial properties. Save money with renewable energy.'); ?>
                                        </div>
                                    </div>

                                    <h6 class="mt-4">Open Graph (Social Media)</h6>
                                    <div class="mb-3">
                                        <label class="form-label">OG Image URL</label>
                                        <input type="url" name="og_image" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['og_image'] ?? ''); ?>">
                                        <small class="text-muted">Recommended: 1200x630px</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">OG Title</label>
                                        <input type="text" name="og_title" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['og_title'] ?? $settings['meta_title'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">OG Description</label>
                                        <textarea name="og_description" class="form-control" rows="2"><?php echo htmlspecialchars($settings['og_description'] ?? $settings['meta_description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Google Services</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Google Search Console Verification</label>
                                        <textarea name="google_search_console" class="form-control" rows="2" 
                                                  placeholder='<meta name="google-site-verification" content="..." />>'><?php echo htmlspecialchars($settings['google_search_console'] ?? ''); ?></textarea>
                                        <small class="text-muted">Paste your Google Search Console verification meta tag here</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Structured Data (Schema.org)</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Business Type</label>
                                        <select name="structured_data_type" class="form-control">
                                            <option value="LocalBusiness" <?php echo ($settings['structured_data_type'] ?? 'LocalBusiness') === 'LocalBusiness' ? 'selected' : ''; ?>>Local Business</option>
                                            <option value="HomeAndConstructionBusiness" <?php echo ($settings['structured_data_type'] ?? 'LocalBusiness') === 'HomeAndConstructionBusiness' ? 'selected' : ''; ?>>Home & Construction</option>
                                            <option value="ProfessionalService" <?php echo ($settings['structured_data_type'] ?? 'LocalBusiness') === 'ProfessionalService' ? 'selected' : ''; ?>>Professional Service</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Business Name</label>
                                        <input type="text" name="structured_data_business_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['structured_data_business_name'] ?? $settings['company_name'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Technical SEO</h6>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" name="sitemap_auto_update" class="form-check-input" 
                                               id="sitemap_auto_update" value="1" 
                                               <?php echo ($settings['sitemap_auto_update'] ?? '1') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="sitemap_auto_update">
                                            Auto-update XML Sitemap
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">robots.txt Content</label>
                                        <textarea name="robots_txt_content" class="form-control" rows="6" 
                                                  style="font-family: monospace;"><?php echo htmlspecialchars($robots_content); ?></textarea>
                                        <small class="text-muted">Edit your robots.txt file content</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>SEO Tools</h6>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="generateSitemap()">
                                            <i class="fas fa-sitemap"></i> Generate XML Sitemap
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="testSeoSettings()">
                                            <i class="fas fa-vial"></i> Test SEO Settings
                                        </button>
                                        <a href="../sitemap.xml" target="_blank" class="btn btn-outline-info">
                                            <i class="fas fa-external-link-alt"></i> View Sitemap
                                        </a>
                                        <a href="../robots.txt" target="_blank" class="btn btn-outline-info">
                                            <i class="fas fa-external-link-alt"></i> View Robots.txt
                                        </a>
                                    </div>
                                    
                                    <h6 class="mt-4">Business Contact for Schema</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Business Address</label>
                                        <textarea name="structured_data_address" class="form-control" rows="2"><?php echo htmlspecialchars($settings['structured_data_address'] ?? $settings['company_address'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Business Phone</label>
                                        <input type="text" name="structured_data_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['structured_data_phone'] ?? $settings['company_phone'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Business Email</label>
                                        <input type="email" name="structured_data_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['structured_data_email'] ?? $settings['company_email'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Opening Hours</label>
                                        <input type="text" name="structured_data_opening_hours" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['structured_data_opening_hours'] ?? $settings['company_working_hours'] ?? ''); ?>">
                                        <small class="text-muted">Example: Mo-Fr 09:00-18:00</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-save-btn">
                        <button type="submit" name="update_seo_settings" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save SEO Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Social Media Tab -->
            <div class="tab-pane fade" id="social" role="tabpanel">
                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-share-alt"></i> Social Media Links
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Facebook URL</label>
                                        <input type="url" name="social_facebook" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">X URL</label>
                                        <input type="url" name="social_x" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['social_x'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Instagram URL</label>
                                        <input type="url" name="social_instagram" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['social_instagram'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">LinkedIn URL</label>
                                        <input type="url" name="social_linkedin" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['social_linkedin'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">YouTube URL</label>
                                        <input type="url" name="social_youtube" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['social_youtube'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">WhatsApp Number</label>
                                        <input type="text" name="social_whatsapp" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['social_whatsapp'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-save-btn">
                        <button type="submit" name="update_social_media" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Social Media Links
                        </button>
                    </div>
                </form>
            </div>

            <!-- Branding Tab -->
            <div class="tab-pane fade" id="branding" role="tabpanel">
                <form method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-paint-brush"></i> Branding & Assets
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Website Logo</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                        <?php if (isset($settings['website_logo']) && $settings['website_logo']): ?>
                                            <small class="text-muted">Current: <?php echo $settings['website_logo']; ?></small>
                                            <br>
                                            <img src="../<?php echo $settings['website_logo']; ?>" height="50" class="mt-2">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Favicon</label>
                                        <input type="file" name="favicon" class="form-control" accept="image/*">
                                        <?php if (isset($settings['website_favicon']) && $settings['website_favicon']): ?>
                                            <small class="text-muted">Current: <?php echo $settings['website_favicon']; ?></small>
                                            <br>
                                            <img src="../<?php echo $settings['website_favicon']; ?>" width="32" class="mt-2">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Google Maps Embed -->
                            <div class="mb-3">
                                <label class="form-label">Google Maps Embed Code</label>
                                <textarea name="contact_map_embed" class="form-control" rows="4" placeholder='&lt;iframe src="https://www.google.com/maps/embed?pb=..."&gt;&lt;/iframe&gt;'><?php echo htmlspecialchars($settings['contact_map_embed'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="tab-save-btn">
                        <button type="submit" name="update_branding" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Branding Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Configuration Tab -->
            <div class="tab-pane fade" id="email" role="tabpanel">
                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-envelope"></i> Email Configuration
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Configure email settings for contact forms and notifications.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>SMTP Settings</h6>
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Host *</label>
                                        <input type="text" name="smtp_host" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Port *</label>
                                        <input type="number" name="smtp_port" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Username *</label>
                                        <input type="text" name="smtp_username" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Password *</label>
                                        <div class="input-group">
                                            <input type="password" name="smtp_password" class="form-control" 
                                                   value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>" required>
                                            <span class="input-group-text password-toggle" onclick="togglePassword(this)">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Encryption *</label>
                                        <select name="smtp_encryption" class="form-control" required>
                                            <option value="tls" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                            <option value="" <?php empty($settings['smtp_encryption'] ?? '') ? 'selected' : ''; ?>>None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Email Settings</h6>
                                    <div class="mb-3">
                                        <label class="form-label">From Email *</label>
                                        <input type="email" name="from_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['from_email'] ?? $settings['company_email'] ?? 'noreply@solarcompany.com'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">From Name *</label>
                                        <input type="text" name="from_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['from_name'] ?? $settings['company_name'] ?? 'Solar Company'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Admin Notification Email *</label>
                                        <input type="email" name="admin_notification_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($settings['admin_notification_email'] ?? $settings['company_email'] ?? 'admin@solarcompany.com'); ?>" required>
                                        <small class="text-muted">Where contact form submissions and consultation requests will be sent</small>
                                    </div>
                                    
                                    <h6 class="mt-4">Test Email Configuration</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Test Email Address</label>
                                        <input type="email" id="test_email" class="form-control" placeholder="Enter email to test configuration">
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" onclick="testEmailConfig()">
                                        <i class="fas fa-paper-plane"></i> Send Test Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-save-btn">
                        <button type="submit" name="update_email_config" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Email Configuration
                        </button>
                    </div>
                </form>
            </div>
			
			<!-- WhatsApp Configuration Tab -->
			<div class="tab-pane fade" id="whatsapp" role="tabpanel">
				<form method="POST">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">
								<i class="fab fa-whatsapp"></i> WhatsApp Configuration
							</h5>
						</div>
						<div class="card-body">
							<div class="alert alert-info">
								<i class="fas fa-info-circle"></i> Configure your WhatsApp floating button settings.
							</div>
							
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label">WhatsApp Business Number *</label>
										<input type="text" name="whatsapp_number" class="form-control" 
											   value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" 
											   placeholder="15551234567" required>
										<small class="text-muted">Enter full number with country code (no + or spaces)</small>
									</div>
									
									<div class="mb-3">
										<label class="form-label">Default Welcome Message</label>
										<textarea name="whatsapp_welcome_message" class="form-control" rows="4" 
												  placeholder="Hello! Thank you for contacting <?php echo htmlspecialchars($settings['company_name'] ?? 'us'); ?>. How can we help you today?"><?php echo htmlspecialchars($settings['whatsapp_welcome_message'] ?? ''); ?></textarea>
										<small class="text-muted">This message will be pre-filled when customers click the WhatsApp button</small>
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label">Button Position</label>
										<select name="whatsapp_button_position" class="form-control">
											<option value="bottom-right" <?php echo ($settings['whatsapp_button_position'] ?? 'bottom-right') === 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
											<option value="bottom-left" <?php echo ($settings['whatsapp_button_position'] ?? 'bottom-right') === 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
											<option value="top-right" <?php echo ($settings['whatsapp_button_position'] ?? 'bottom-right') === 'top-right' ? 'selected' : ''; ?>>Top Right</option>
											<option value="top-left" <?php echo ($settings['whatsapp_button_position'] ?? 'bottom-right') === 'top-left' ? 'selected' : ''; ?>>Top Left</option>
										</select>
									</div>
									
									<div class="mb-3 form-check">
										<input type="checkbox" name="whatsapp_enabled" class="form-check-input" 
											   id="whatsapp_enabled" value="1" 
											   <?php echo ($settings['whatsapp_enabled'] ?? '1') ? 'checked' : ''; ?>>
										<label class="form-check-label" for="whatsapp_enabled">
											Enable WhatsApp Button
										</label>
									</div>
									
									<div class="mb-3 form-check">
										<input type="checkbox" name="whatsapp_show_desktop" class="form-check-input" 
											   id="whatsapp_show_desktop" value="1" 
											   <?php echo ($settings['whatsapp_show_desktop'] ?? '1') ? 'checked' : ''; ?>>
										<label class="form-check-label" for="whatsapp_show_desktop">
											Show on Desktop
										</label>
									</div>
									
									<div class="mb-3 form-check">
										<input type="checkbox" name="whatsapp_show_mobile" class="form-check-input" 
											   id="whatsapp_show_mobile" value="1" 
											   <?php echo ($settings['whatsapp_show_mobile'] ?? '1') ? 'checked' : ''; ?>>
										<label class="form-check-label" for="whatsapp_show_mobile">
											Show on Mobile
										</label>
									</div>
								</div>
							</div>
							
							<div class="card mt-4">
								<div class="card-header">
									<h6 class="mb-0">Live Preview</h6>
								</div>
								<div class="card-body text-center">
									<div class="whatsapp-preview">
										<div class="whatsapp-float-preview">
											<i class="fab fa-whatsapp"></i>
										</div>
										<p class="text-muted mt-2">Your WhatsApp button with pulse animation</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-save-btn">
						<button type="submit" name="update_whatsapp_config" class="btn btn-primary">
							<i class="fas fa-save"></i> Save WhatsApp Configuration
						</button>
					</div>
				</form>
			</div>

            <!-- Color Themes Tab -->
            <div class="tab-pane fade" id="colors" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Color Themes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Choose a color theme for your website. Changes take effect immediately.</p>
                        
                        <div class="row">
                            <?php foreach ($palettes as $palette): ?>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="card palette-card <?php echo $palette['is_active'] ? 'active' : ''; ?>">
                                    <div class="card-body">
                                        <?php if ($palette['is_active']): ?>
                                            <span class="active-badge">Active</span>
                                        <?php endif; ?>
                                        
                                        <h6 class="card-title"><?php echo htmlspecialchars($palette['name']); ?></h6>
                                        
                                        <div class="palette-preview">
                                            <div class="color-swatch" style="background-color: <?php echo $palette['primary_color']; ?>"></div>
                                            <div class="color-swatch" style="background-color: <?php echo $palette['secondary_color']; ?>"></div>
                                            <div class="color-swatch" style="background-color: <?php echo $palette['accent_color']; ?>"></div>
                                            <div class="color-swatch" style="background-color: <?php echo $palette['background_color']; ?>"></div>
                                            <div class="color-swatch" style="background-color: <?php echo $palette['text_color']; ?>"></div>
                                            <div class="color-swatch" style="background-color: <?php echo $palette['light_color']; ?>"></div>
                                        </div>
                                        
                                        <!-- Separate form for activating palette -->
                                        <form method="POST" class="d-inline w-100">
                                            <input type="hidden" name="palette_id" value="<?php echo $palette['id']; ?>">
                                            <button type="submit" name="activate_palette" class="btn btn-sm <?php echo $palette['is_active'] ? 'btn-success' : 'btn-outline-primary'; ?> w-100">
                                                <?php echo $palette['is_active'] ? 'Active' : 'Activate'; ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-4">
                        
                        <h6>Create Custom Theme</h6>
                        <!-- Separate form for adding new palette -->
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Theme Name *</label>
                                        <input type="text" name="name" class="form-control" placeholder="My Custom Theme" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Primary *</label>
                                    <input type="color" name="primary_color" class="form-control form-control-color" value="#FF6B35" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Secondary *</label>
                                    <input type="color" name="secondary_color" class="form-control form-control-color" value="#2E86AB" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Accent *</label>
                                    <input type="color" name="accent_color" class="form-control form-control-color" value="#F7C59F" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Background *</label>
                                    <input type="color" name="background_color" class="form-control form-control-color" value="#FFFFFF" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Text *</label>
                                    <input type="color" name="text_color" class="form-control form-control-color" value="#333333" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Light *</label>
                                    <input type="color" name="light_color" class="form-control form-control-color" value="#F8F9FA" required>
                                </div>
                            </div>
                            <button type="submit" name="add_palette" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-plus"></i> Add Custom Theme
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit on palette card click
        document.querySelectorAll('.palette-card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Only trigger if not clicking the button directly
                if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                    const form = this.querySelector('form');
                    if (form && !this.classList.contains('active')) {
                        form.submit();
                    }
                }
            });
        });

        // Save active tab
        const settingsTabs = document.getElementById('settingsTabs');
        if (settingsTabs) {
            settingsTabs.addEventListener('shown.bs.tab', function (event) {
                localStorage.setItem('activeSettingsTab', event.target.getAttribute('id'));
            });
            
            // Restore active tab
            const activeTab = localStorage.getItem('activeSettingsTab');
            if (activeTab) {
                const tab = document.querySelector(`#${activeTab}`);
                if (tab) {
                    new bootstrap.Tab(tab).show();
                }
            }
        }

        // Toggle password visibility
        function togglePassword(element) {
            const passwordInput = element.closest('.input-group').querySelector('input');
            const icon = element.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Test email configuration
        function testEmailConfig() {
            const testEmail = document.getElementById('test_email').value;
            
            if (!testEmail) {
                alert('Please enter a test email address');
                return;
            }
            
            if (!validateEmail(testEmail)) {
                alert('Please enter a valid email address');
                return;
            }
            
            // Show loading state
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;
            
            // Send AJAX request to test email - handle both JSON and plain text responses
            fetch('test_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `test_email=${encodeURIComponent(testEmail)}`
            })
            .then(response => {
                // First try to parse as JSON, if that fails, treat as plain text
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        return { success: false, message: text || 'Unknown error occurred' };
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    alert('Test email sent successfully! Please check your inbox.');
                } else {
                    alert('Failed to send test email: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error sending test email: ' + error.message);
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // SEO Functions
        function updateSeoPreview() {
            const titleInput = document.querySelector('input[name="meta_title"]');
            const descriptionInput = document.querySelector('textarea[name="meta_description"]');
            const titlePreview = document.getElementById('preview-title');
            const descriptionPreview = document.getElementById('preview-description');
            const titleCount = document.getElementById('title-count');
            const descriptionCount = document.getElementById('description-count');
            
            // Update preview
            titlePreview.textContent = titleInput.value || 'Your page title will appear here';
            descriptionPreview.textContent = descriptionInput.value || 'Your meta description will appear here';
            
            // Update character counts
            const titleLength = titleInput.value.length;
            const descriptionLength = descriptionInput.value.length;
            
            titleCount.textContent = `${60 - titleLength} characters remaining`;
            descriptionCount.textContent = `${160 - descriptionLength} characters remaining`;
            
            // Add warning classes
            titleCount.className = 'char-count' + (titleLength > 60 ? ' danger' : (titleLength > 55 ? ' warning' : ''));
            descriptionCount.className = 'char-count' + (descriptionLength > 160 ? ' danger' : (descriptionLength > 155 ? ' warning' : ''));
        }

        function generateSitemap() {
			// Show loading state
			const btn = event.target;
			const originalText = btn.innerHTML;
			btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
			btn.disabled = true;
			
			// Send AJAX request to generate sitemap
			fetch('generate_sitemap.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				}
			})
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(data => {
				if (data.success) {
					alert('✅ Sitemap generated successfully!');
				} else {
					alert('❌ Failed to generate sitemap: ' + data.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('❌ Error generating sitemap: ' + error.message);
			})
			.finally(() => {
				// Restore button state
				btn.innerHTML = originalText;
				btn.disabled = false;
			});
		}

        function testSeoSettings() {
            // Simple SEO test - check for required fields
            const title = document.querySelector('input[name="meta_title"]').value;
            const description = document.querySelector('textarea[name="meta_description"]').value;
            
            let issues = [];
            
            if (!title) {
                issues.push('Meta title is required');
            } else if (title.length > 60) {
                issues.push('Meta title is too long (max 60 characters)');
            }
            
            if (!description) {
                issues.push('Meta description is required');
            } else if (description.length > 160) {
                issues.push('Meta description is too long (max 160 characters)');
            }
            
            if (issues.length === 0) {
                alert('✅ SEO settings look good! All required fields are properly configured.');
            } else {
                alert('⚠️ SEO issues found:\n\n• ' + issues.join('\n• '));
            }
        }

        // Initialize SEO preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSeoPreview();
        });
    </script>
</body>
</html>