<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New password and confirmation do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } else {
        // Get current user info
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_SESSION['admin_username']]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                if ($update_stmt->execute([$hashed_password, $_SESSION['admin_username']])) {
                    $success = 'Password updated successfully!';
                    
                    // Log the user out for security
                    session_destroy();
                    header('Location: login.php?password_changed=1');
                    exit;
                } else {
                    $error = 'Failed to update password. Please try again.';
                }
            } else {
                $error = 'Current password is incorrect.';
            }
        } else {
            $error = 'User not found.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .strength-weak { background-color: #dc3545; width: 25%; }
        .strength-fair { background-color: #ffc107; width: 50%; }
        .strength-good { background-color: #17a2b8; width: 75%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-key"></i> Change Password
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" id="passwordForm">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="form-text text-muted">
                                    Password must be at least 6 characters long.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div class="invalid-feedback" id="confirmError">
                                    Passwords do not match.
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Change Password
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Password Requirements Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Password Requirements</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-check text-success me-2"></i> At least 6 characters long</li>
                            <li><i class="fas fa-check text-success me-2"></i> Use a mix of letters and numbers</li>
                            <li><i class="fas fa-check text-success me-2"></i> Avoid common words and patterns</li>
                            <li><i class="fas fa-check text-success me-2"></i> Don't reuse old passwords</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if (password.length >= 6) strength += 25;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
            if (password.match(/\d/)) strength += 25;
            if (password.match(/[^a-zA-Z\d]/)) strength += 25;
            
            strengthBar.className = 'password-strength';
            if (strength <= 25) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 50) {
                strengthBar.classList.add('strength-fair');
            } else if (strength <= 75) {
                strengthBar.classList.add('strength-good');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const confirmError = document.getElementById('confirmError');
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.classList.add('is-invalid');
                confirmError.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                confirmError.style.display = 'none';
            }
        });
        
        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                document.getElementById('confirm_password').classList.add('is-invalid');
                document.getElementById('confirmError').style.display = 'block';
            }
        });
    </script>
</body>
</html>