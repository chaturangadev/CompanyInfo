<?php
// test_email.php
header('Content-Type: application/json');

// Include config and check admin session
include '../includes/config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_email = filter_var($_POST['test_email'] ?? '', FILTER_SANITIZE_EMAIL);
    
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address']);
        exit;
    }
    
    try {
        // Get email settings from database
        $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
        
        // Check if required settings are present
        $required_settings = ['smtp_host', 'smtp_username', 'smtp_password'];
        foreach ($required_settings as $setting) {
            if (empty($settings[$setting])) {
                throw new Exception("Required setting '$setting' is not configured");
            }
        }
        
        // Configure PHPMailer
        $phpmailer_path = '../includes/PHPMailer/src/';
        if (!file_exists($phpmailer_path . 'PHPMailer.php')) {
            // Try alternative path
            $phpmailer_path = 'includes/PHPMailer/src/';
            if (!file_exists($phpmailer_path . 'PHPMailer.php')) {
                throw new Exception('PHPMailer not found. Please ensure PHPMailer is installed in the includes directory.');
            }
        }
        
        require $phpmailer_path . 'PHPMailer.php';
        require $phpmailer_path . 'SMTP.php';
        require $phpmailer_path . 'Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $settings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['smtp_username'];
        $mail->Password = $settings['smtp_password'];
        $mail->SMTPSecure = $settings['smtp_encryption'] ?? 'tls';
        $mail->Port = $settings['smtp_port'] ?? 587;
        
        // Debugging (optional - remove in production)
        $mail->SMTPDebug = 0; // 0 = off, 1 = client messages, 2 = client and server messages
        $mail->Debugoutput = 'error_log';
        
        // Recipients
        $from_email = $settings['from_email'] ?? $settings['smtp_username'];
        $from_name = $settings['from_name'] ?? $settings['company_name'] ?? 'Solar Company';
        
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($test_email);
        $mail->addReplyTo($from_email, $from_name);
        
        // Content
        $mail->Subject = 'Test Email - Solar Company Configuration';
        $mail->Body = "Hello,\n\nThis is a test email from your Solar Company website email configuration.\n\nIf you received this email, your SMTP settings are working correctly!\n\nConfiguration Details:\n- SMTP Host: " . $settings['smtp_host'] . "\n- SMTP Port: " . ($settings['smtp_port'] ?? '587') . "\n- Encryption: " . ($settings['smtp_encryption'] ?? 'tls') . "\n\nSent at: " . date('Y-m-d H:i:s');
        
        $mail->AltBody = "This is a test email from your Solar Company website email configuration. If you received this email, your SMTP settings are working correctly!";
        
        if ($mail->send()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Test email sent successfully to ' . $test_email
            ]);
        } else {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
        }
        
    } catch (Exception $e) {
        error_log("Email Test Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send test email: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>