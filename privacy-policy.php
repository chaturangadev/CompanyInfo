<?php
include 'includes/config.php';

// Fetch privacy policy content and SEO settings from database
$privacy_content = $settings['privacy_policy_content'] ?? '';
$privacy_last_updated = $settings['privacy_last_updated'] ?? '';
$privacy_meta_title = $settings['privacy_meta_title'] ?? 'Privacy Policy - ' . ($settings['company_name'] ?? '');
$privacy_meta_description = $settings['privacy_meta_description'] ?? 'Read our Privacy Policy to understand how we collect, use, and protect your personal information.';

// If no custom content, use default
if (empty($privacy_content)) {
    $privacy_content = '<!-- Default privacy policy content will go here -->';
}

// Set page variables for SEO
$page_title = htmlspecialchars($privacy_meta_title);
$page_description = htmlspecialchars($privacy_meta_description);
$current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:url" content="<?php echo $current_url; ?>">
    <meta property="og:type" content="website">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $current_url; ?>">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "<?php echo $page_title; ?>",
        "description": "<?php echo $page_description; ?>",
        "url": "<?php echo $current_url; ?>",
        "lastReviewed": "<?php echo !empty($privacy_last_updated) ? date('Y-m-d', strtotime($privacy_last_updated)) : date('Y-m-d'); ?>",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>",
            "url": "<?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']; ?>"
        }
    }
    </script>
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "<?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']; ?>"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Privacy Policy",
                "item": "<?php echo $current_url; ?>"
            }
        ]
    }
    </script>
    
    <?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/legal.css">
    
    <style>
        :root {
            --primary-color: <?php echo $colors['primary_color']; ?>;
            --secondary-color: <?php echo $colors['secondary_color']; ?>;
            --background-color: <?php echo $colors['background_color']; ?>;
            --text-color: <?php echo $colors['text_color']; ?>;
            --accent-color: <?php echo $colors['accent_color']; ?>;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
            </ol>
        </div>
    </nav>

    <!-- Legal Header -->
    <section class="legal-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="section-title">Privacy Policy</h1>
                    <p class="lead">Your privacy is important to us. Learn how we protect and manage your personal information.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Privacy Policy Content -->
    <section class="legal-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <!-- Dynamic Content -->
                    <div class="legal-content-body">
                        <?php echo $privacy_content; ?>
                    </div>

                    <!-- Last Updated -->
                    <div class="last-updated">
                        <p class="mb-0">
                            <i class="fas fa-clock"></i> 
                            <strong>Last Updated:</strong> 
                            <?php if (!empty($privacy_last_updated)): ?>
                                <?php echo date('F j, Y', strtotime($privacy_last_updated)); ?>
                            <?php else: ?>
                                <?php echo date('F j, Y'); ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Related Legal Links -->
                    <div class="related-links mt-4">
                        <h3>Related Documents</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="terms-of-service.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-file-contract"></i> Terms of Service
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="index.php#contact" class="btn btn-outline-secondary w-100 mb-2">
                                    <i class="fas fa-envelope"></i> Contact Us
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Back to Home -->
                    <div class="text-center mt-5">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/analytics.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add smooth scrolling for anchor links within the privacy policy
        document.addEventListener('DOMContentLoaded', function() {
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Update last updated text for better UX
            const lastUpdated = document.querySelector('.last-updated');
            if (lastUpdated) {
                const updateDate = new Date('<?php echo !empty($privacy_last_updated) ? $privacy_last_updated : date('Y-m-d H:i:s'); ?>');
                const now = new Date();
                const diffTime = Math.abs(now - updateDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays < 30) {
                    lastUpdated.innerHTML += '<br><small class="text-success"><i class="fas fa-check-circle"></i> Recently updated</small>';
                }
            }
        });
    </script>
</body>
</html>