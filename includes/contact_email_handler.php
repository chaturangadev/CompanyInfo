<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($message)) {
        $contact_error = "Please fill in all required fields";
    } else {
        try {
            // Get email settings
            $stmt = $pdo->query("SELECT setting_name, setting_value FROM website_settings");
            $settings = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_name']] = $row['setting_value'];
            }
            
            // Configure PHPMailer
            require 'includes/PHPMailer/src/PHPMailer.php';
            require 'includes/PHPMailer/src/SMTP.php';
            require 'includes/PHPMailer/src/Exception.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = $settings['smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $settings['smtp_username'] ?? '';
            $mail->Password = $settings['smtp_password'] ?? '';
            $mail->SMTPSecure = $settings['smtp_encryption'] ?? 'tls';
            $mail->Port = $settings['smtp_port'] ?? 587;
            
            // Email content
            $admin_email = $settings['admin_notification_email'] ?? $settings['company_email'] ?? 'admin@yourcompany.com';
            
            $mail->setFrom($settings['from_email'] ?? 'noreply@yourcompany.com', $settings['from_name'] ?? 'Solar Company');
            $mail->addAddress($admin_email);
            $mail->addReplyTo($email, $name);
            
            $mail->Subject = "Contact Form: " . ($subject ?: 'New Message from Website');
            $mail->Body = "
            New contact form submission from your website:
            
            Name: $name
            Email: $email
            Subject: " . ($subject ?: 'Not specified') . "
            
            Message:
            $message
            
            Sent at: " . date('Y-m-d H:i:s');
            
            if ($mail->send()) {
                $contact_success = "Thank you for your message! We'll get back to you soon.";
            } else {
                $contact_error = "Sorry, there was an error sending your message. Please try again later.";
            }
            
        } catch (Exception $e) {
            $contact_error = "Sorry, there was an error sending your message. Please try again later.";
        }
    }
}
?>