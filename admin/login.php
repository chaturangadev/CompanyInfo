<?php
session_start();
include '../includes/config.php';

// Session timeout configuration (3 hours = 10800 seconds)
$inactive = 10800;

// Check if timeout condition exists
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    // Last request was more than 3 hours ago
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Debug: Check what we're receiving
    error_log("Login attempt - Username: $username, Password: [hidden]");
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            error_log("User found: " . $user['username']);
            error_log("Stored hash: " . $user['password']);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['last_activity'] = time(); // Set initial activity time
                
                error_log("Login successful for user: $username");
                header('Location: index.php');
                exit;
            } else {
                $error = "Invalid password!";
                error_log("Password verification failed for user: $username");
            }
        } else {
            $error = "User not found!";
            error_log("User not found: $username");
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    }
}

// Show success message if password was changed
if (isset($_GET['password_changed'])) {
    $success = "Password changed successfully! Please login with your new password.";
}

// Show timeout message
if (isset($_GET['timeout'])) {
    $error = "Your session has expired due to inactivity. Please login again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card p-4">
                    <div class="text-center mb-4">
                        <h2><?php echo htmlspecialchars($company_name); ?></h2>
                        <p class="text-muted">Admin Panel Login</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="admin" required autocomplete="username">
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required autocomplete="current-password">
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.querySelector('input[name="password"]').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in both username and password.');
            }
        });
    </script>
</body>
</html>