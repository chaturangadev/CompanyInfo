<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'solar_company';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch website settings
$settings = [];
$stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

// Fetch active color palette
$current_palette_id = $settings['current_color_palette'] ?? 1;
$palette_stmt = $pdo->prepare("SELECT * FROM color_palettes WHERE id = ?");
$palette_stmt->execute([$current_palette_id]);
$active_palette = $palette_stmt->fetch();

// If no active palette found, use the first one
if (!$active_palette) {
    $palette_stmt = $pdo->query("SELECT * FROM color_palettes WHERE is_active = TRUE LIMIT 1");
    $active_palette = $palette_stmt->fetch();
    
    // If still no palette, get the first one
    if (!$active_palette) {
        $palette_stmt = $pdo->query("SELECT * FROM color_palettes ORDER BY id ASC LIMIT 1");
        $active_palette = $palette_stmt->fetch();
    }
}

// Set CSS variables from active palette
$colors = [
    'primary_color' => $active_palette['primary_color'] ?? '#FF6B35',
    'secondary_color' => $active_palette['secondary_color'] ?? '#2E86AB',
    'accent_color' => $active_palette['accent_color'] ?? '#F7C59F',
    'background_color' => $active_palette['background_color'] ?? '#FFFFFF',
    'text_color' => $active_palette['text_color'] ?? '#333333',
    'light_color' => $active_palette['light_color'] ?? '#F8F9FA'
];

// Company Information
$company_name = $settings['company_name'] ?? 'Solar Energy Solutions';
$company_email = $settings['company_email'] ?? 'info@solarcompany.com';
$company_phone = $settings['company_phone'] ?? '+1 (555) 123-4567';
$company_address = $settings['company_address'] ?? '123 Solar Street, Energy City, EC 12345';
$company_working_hours = $settings['company_working_hours'] ?? 'Mon-Fri: 9AM-6PM';
$contact_form_email = $settings['contact_form_email'] ?? 'info@solarcompany.com';

$projects_count = $settings['projects_count'] ?? '1000+';
$clients_count = $settings['clients_count'] ?? '500+';
$satisfaction_rate = $settings['satisfaction_rate'] ?? '98%';
$years_of_experience = $settings['years_of_experience'] ?? '5+';

// Branding Assets
$website_logo = $settings['website_logo'] ?? '';
$website_favicon = $settings['website_favicon'] ?? '';

// Social Media
$social_facebook = $settings['social_facebook'] ?? '';
$social_x = $settings['social_x'] ?? '';
$social_instagram = $settings['social_instagram'] ?? '';
$social_linkedin = $settings['social_linkedin'] ?? '';
$social_youtube = $settings['social_youtube'] ?? '';
$social_whatsapp = $settings['social_whatsapp'] ?? '';

// Get WhatsApp settings
$whatsapp_enabled = $settings['whatsapp_enabled'] ?? '1';
$whatsapp_number = $settings['whatsapp_number'] ?? '15551234567';
$whatsapp_welcome_message = $settings['whatsapp_welcome_message'] ?? "Hi {$company_name}! I would like to get more information about your products.";
$whatsapp_show_desktop = $settings['whatsapp_show_desktop'] ?? '1';
$whatsapp_show_mobile = $settings['whatsapp_show_mobile'] ?? '1';
$whatsapp_button_position = $settings['whatsapp_button_position'] ?? 'bottom-right';

// Contact Map
$contact_map_embed = $settings['contact_map_embed'] ?? '';

// Google Analytics
$google_analytics_id = $settings['google_analytics_id'] ?? '';
$google_analytics_status = $settings['google_analytics_status'] ?? 'disabled';

// Fetch active hero slides
$hero_slides = $pdo->query("SELECT * FROM hero_slides WHERE is_active = TRUE ORDER BY slide_order ASC")->fetchAll();

// NEW: Fetch all website content sections at once for better performance
$website_content = [];
try {
    $content_stmt = $pdo->query("SELECT section_name, title, content, image_path FROM website_content WHERE is_active = TRUE");
    $website_content_results = $content_stmt->fetchAll();
    
    // Convert to associative array with section_name as key
    foreach ($website_content_results as $row) {
        $website_content[$row['section_name']] = [
            'title' => $row['title'],
            'content' => $row['content'],
            'image_path' => $row['image_path']
        ];
    }
} catch (PDOException $e) {
    // If table doesn't exist yet, create empty array
    $website_content = [];
}

// Function to get website content by section
function getWebsiteContent($pdo, $page_slug) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM website_content WHERE page_slug = ? AND status = 'active'");
        $stmt->execute([$page_slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getWebsiteContent: " . $e->getMessage());
        return null;
    }
}

// Function to get website title by section
function getWebsiteTitle($pdo, $section_name, $default = '') {
    global $website_content;
    
    if (isset($website_content[$section_name])) {
        return $website_content[$section_name]['title'] ?? $default;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT title FROM website_content WHERE section_name = ? AND is_active = TRUE");
        $stmt->execute([$section_name]);
        $result = $stmt->fetch();
        return $result ? $result['title'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

// Function to get content with image
function getWebsiteContentWithImage($pdo, $section_name) {
    global $website_content;
    
    if (isset($website_content[$section_name])) {
        return $website_content[$section_name];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT content, image_path FROM website_content WHERE section_name = ? AND is_active = TRUE");
        $stmt->execute([$section_name]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Function to get complete section data
function getWebsiteSection($pdo, $section_name) {
    global $website_content;
    
    if (isset($website_content[$section_name])) {
        return $website_content[$section_name];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM website_content WHERE section_name = ? AND is_active = TRUE");
        $stmt->execute([$section_name]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Check if user is admin (for admin pages)
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect to login if not admin (for admin pages)
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}
?>