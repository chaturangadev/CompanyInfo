<?php
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle statistics updates
    if (isset($_POST['update_statistics'])) {
        $statistics = [
            'projects_count' => $_POST['projects_count'] ?? '1000+',
            'clients_count' => $_POST['clients_count'] ?? '500+',
            'satisfaction_rate' => $_POST['satisfaction_rate'] ?? '98%',
            'years_of_experience' => $_POST['years_of_experience'] ?? '10+'
        ];
        
        foreach ($statistics as $name => $value) {
            // Check if setting exists, if not insert, else update
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM website_settings WHERE setting_name = ?");
            $check_stmt->execute([$name]);
            $exists = $check_stmt->fetchColumn();
            
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->execute([$value, $name]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO website_settings (setting_name, setting_value) VALUES (?, ?)");
                $stmt->execute([$name, $value]);
            }
        }
        
        $success = "Statistics updated successfully!";
    }
}

// Fetch current statistics
$stats_stmt = $pdo->prepare("SELECT setting_name, setting_value FROM website_settings WHERE setting_name IN ('projects_count', 'clients_count', 'satisfaction_rate', 'years_of_experience')");
$stats_stmt->execute();
$statistics = $stats_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Set default values if not exists
$projects_count = $statistics['projects_count'] ?? '1000+';
$clients_count = $statistics['clients_count'] ?? '500+';
$satisfaction_rate = $statistics['satisfaction_rate'] ?? '98%';
$years_of_experience = $statistics['years_of_experience'] ?? '10+';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Configuration - <?php echo htmlspecialchars($company_name); ?></title>
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .preview-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 30px;
            margin-top: 20px;
        }
        .preview-stat {
            text-align: center;
            padding: 15px;
        }
        .preview-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .preview-label {
            font-size: 1rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="fa-solid fa-chart-simple"></i> Statistics Configuration
            </h1>
            <a href="update_content.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Content Management
            </a>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Configuration Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs"></i> Configure Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-project-diagram"></i> Projects Count
                                    </label>
                                    <input type="text" name="projects_count" class="form-control" 
                                           value="<?php echo htmlspecialchars($projects_count); ?>" 
                                           placeholder="e.g., 1000+">
                                    <small class="text-muted">This will be displayed as "X Projects"</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-users"></i> Happy Clients Count
                                    </label>
                                    <input type="text" name="clients_count" class="form-control" 
                                           value="<?php echo htmlspecialchars($clients_count); ?>" 
                                           placeholder="e.g., 500+">
                                    <small class="text-muted">This will be displayed as "X Happy Clients"</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-smile"></i> Satisfaction Rate
                                    </label>
                                    <input type="text" name="satisfaction_rate" class="form-control" 
                                           value="<?php echo htmlspecialchars($satisfaction_rate); ?>" 
                                           placeholder="e.g., 98%">
                                    <small class="text-muted">This will be displayed as "X Satisfaction"</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Years Of Experience
                                    </label>
                                    <input type="text" name="years_of_experience" class="form-control" 
                                           value="<?php echo htmlspecialchars($years_of_experience); ?>" 
                                           placeholder="e.g., 10+">
                                    <small class="text-muted">This will be displayed as "X Years Of Experience"</small>
                                </div>
                            </div>
                            <button type="submit" name="update_statistics" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Statistics
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Preview Section -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye"></i> Live Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="preview-box">
                            <div class="row text-center">
                                <div class="col-6 mb-4">
                                    <div class="preview-stat">
                                        <div class="preview-number"><?php echo htmlspecialchars($projects_count); ?></div>
                                        <div class="preview-label">Projects</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-4">
                                    <div class="preview-stat">
                                        <div class="preview-number"><?php echo htmlspecialchars($clients_count); ?></div>
                                        <div class="preview-label">Happy Clients</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="preview-stat">
                                        <div class="preview-number"><?php echo htmlspecialchars($satisfaction_rate); ?></div>
                                        <div class="preview-label">Satisfaction</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="preview-stat">
                                        <div class="preview-number"><?php echo htmlspecialchars($years_of_experience); ?></div>
                                        <div class="preview-label">Years Of Experience</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            This is how your statistics will appear on the website.
                        </small>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Tips
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning"></i>
                                <small>Use format like "1000+", "500+", "98%"</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning"></i>
                                <small>Keep statistics concise and impactful</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning"></i>
                                <small>Update regularly to reflect growth</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Live preview update
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[name]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const statName = this.name;
                    const value = this.value;
                    const previewElement = document.querySelector(`.preview-number:nth-child(${getStatIndex(statName)})`);
                    if (previewElement) {
                        previewElement.textContent = value;
                    }
                });
            });
            
            function getStatIndex(statName) {
                const stats = {
                    'projects_count': 1,
                    'clients_count': 2,
                    'satisfaction_rate': 3,
                    'years_of_experience': 4
                };
                return stats[statName] || 1;
            }
        });
    </script>
</body>
</html>