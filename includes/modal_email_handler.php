<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consultation_submit'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $needs = $_POST['needs'] ?? '';
    
    if (empty($name) || empty($email) || empty($phone)) {
        $consultation_error = "Please fill in all required fields";
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
            
            $mail->Subject = "Free Consultation Request from $name";
            $mail->Body = "
            New consultation request from your website:
            
            Name: $name
            Email: $email
            Phone: $phone
            
            Energy Needs:
            " . ($needs ?: 'Not specified') . "
            
            Sent at: " . date('Y-m-d H:i:s');
            
            if ($mail->send()) {
                $consultation_success = "Thank you for your consultation request! We'll contact you soon.";
            } else {
                $consultation_error = "Sorry, there was an error submitting your request. Please try again later.";
            }
            
        } catch (Exception $e) {
            $consultation_error = "Sorry, there was an error submitting your request. Please try again later.";
        }
    }
}
?>