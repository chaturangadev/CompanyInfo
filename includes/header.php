<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
$is_homepage = ($current_page == 'index.php' || $current_page == 'index.html' || $current_page == '/');

$logo = $settings['website_logo'] ?? 'assets/images/logo.png';
$header_bg = $settings['header_background'] ?? 'rgba(255,255,255,0.95)';
$header_text_color = $settings['header_text_color'] ?? '#333333';
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: <?php echo $header_bg; ?>;">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <?php if (file_exists($logo)): ?>
                <img src="<?php echo $logo; ?>" alt="Solar Energy Solutions" height="50">
            <?php else: ?>
                <span style="color: <?php echo $header_text_color; ?>; font-weight: 700; font-size: 1.2rem;"><?php echo htmlspecialchars($company_name); ?></span>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#hero' : 'index.php'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#products' : 'index.php#products'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'products', 'Our Products & Services')); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#about' : 'index.php#about'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'about', 'About Us')); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#projects' : 'index.php#projects'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'projects', 'Our Projects')); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#reviews' : 'index.php#reviews'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'review', 'Reviews')); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#why-choose' : 'index.php#why-choose'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'why_choose', 'Why Choose Us')); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
                       href="<?php echo $is_homepage ? '#contact' : 'index.php#contact'; ?>" 
                       style="color: <?php echo $header_text_color; ?>;">
                       <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'contact', 'Contact Us')); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>