<?php include 'includes/config.php'; 

// Fetch active products
$products = $pdo->query("SELECT * FROM products WHERE is_active = TRUE ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Fetch active reviews
$reviews = $pdo->query("SELECT * FROM reviews WHERE is_active = TRUE ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Fetch active projects
$projects = $pdo->query("SELECT * FROM projects WHERE is_active = TRUE ORDER BY project_order ASC, completion_date DESC LIMIT 6")->fetchAll();

//Why choose us
$why_us = $pdo->query("SELECT * FROM why_choose_features WHERE is_active = TRUE ORDER BY display_order ASC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company_name); ?></title>
	
	<!-- SEO Meta Tags -->
    <title><?php echo htmlspecialchars($settings['meta_title'] ?? $settings['company_name'] ?? 'Solar Energy Solutions'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['meta_description'] ?? 'Professional solar energy solutions'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($settings['meta_keywords'] ?? 'solar energy, renewable energy'); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($settings['og_title'] ?? $settings['meta_title'] ?? $settings['company_name'] ?? 'Solar Energy Solutions'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($settings['og_description'] ?? $settings['meta_description'] ?? 'Professional solar energy solutions'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($settings['og_image'] ?? ''); ?>">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="website">
    
    <!-- Google Search Console Verification -->
    <?php if (!empty($settings['google_search_console'])): ?>
        <?php echo $settings['google_search_console']; ?>
    <?php endif; ?>
    
    <!-- Structured Data -->
    <?php
    $structured_data = [
        "@context" => "https://schema.org",
        "@type" => $settings['structured_data_type'] ?? 'LocalBusiness',
        "name" => $settings['structured_data_business_name'] ?? $settings['company_name'] ?? '',
        "description" => $settings['meta_description'] ?? '',
        "url" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'],
        "telephone" => $settings['structured_data_phone'] ?? $settings['company_phone'] ?? '',
        "email" => $settings['structured_data_email'] ?? $settings['company_email'] ?? '',
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $settings['structured_data_address'] ?? $settings['company_address'] ?? ''
        ],
        "openingHours" => $settings['structured_data_opening_hours'] ?? $settings['company_working_hours'] ?? ''
    ];
    
    // Remove empty values
    $structured_data = array_filter($structured_data);
    if (isset($structured_data['address']) && empty(array_filter($structured_data['address']))) {
        unset($structured_data['address']);
    }
    ?>
    
    <?php if (!empty($structured_data['name'])): ?>
    <script type="application/ld+json">
        <?php echo json_encode($structured_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>
    <?php endif; ?>
	
	<?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-color: <?php echo $colors['primary_color']; ?>;
            --secondary-color: <?php echo $colors['secondary_color']; ?>;
            --background-color: <?php echo $colors['background_color']; ?>;
            --text-color: <?php echo $colors['text_color']; ?>;
            --accent-color: <?php echo $colors['accent_color']; ?>;
        }
    </style>
	 <?php include 'includes/analytics.php'; ?>
	 <?php include 'includes/contact_email_handler.php'; ?>
	 <?php include 'includes/modal_email_handler.php'; ?>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbarNav">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Banner Carousel -->
    <section id="hero" class="hero-banner">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($hero_slides as $index => $slide): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $index; ?>" 
                    class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                    aria-label="Slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
                <?php foreach ($hero_slides as $index => $slide): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <!-- Mobile Background Image -->
                    <div class="hero-mobile-bg d-md-none" style="background-image: url('<?php echo $slide['image_path']; ?>');"></div>
                    
                    <!-- Desktop Background Image -->
                    <div class="hero-desktop-bg d-none d-md-block" style="background-image: url('<?php echo $slide['image_path']; ?>');"></div>
                    
                    <div class="carousel-overlay"></div>
                    <div class="container">
                        <div class="row align-items-center min-vh-100">
                            <div class="col-lg-8 col-md-10 mx-auto text-center text-md-start">
                                <div class="hero-content">
                                    <h1 class="hero-title"><?php echo $slide['title']; ?></h1>
                                    <p class="hero-subtitle"><?php echo $slide['subtitle']; ?></p>
                                    <div class="hero-buttons">
                                        <button class="btn btn-primary btn-lg me-3 mb-3" data-bs-toggle="modal" data-bs-target="#consultationModal">
                                            <?php echo $slide['button_text'] ?: 'Get Free Consultation'; ?>
                                        </button>
                                        <a href="#products" class="btn btn-outline-light btn-lg mb-3">Our Products</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev d-none d-md-flex" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next d-none d-md-flex" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Products & Services -->
    <section id="products" class="section-padding">
        <div class="container">
            <h2 class="section-title"><?php echo htmlspecialchars(getWebsiteTitle($pdo, 'products', 'Our Products & Services')); ?></h2>
			<p class="text-center mb-5"><?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'products', ""))); ?></p>
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['image_path']): ?>
                            <img src="<?php echo $product['image_path']; ?>" class="card-img-top" alt="<?php echo $product['title']; ?>">
                        <?php else: ?>
                            <div class="card-img-top product-placeholder">
                                <i class="fas fa-solar-panel"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['title']; ?></h5>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <?php if ($product['price']): ?>
                                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                            <?php endif; ?>
                            <?php if ($product['features']): ?>
                                <ul class="features-list">
                                    <?php 
                                    $features = explode('|', $product['features']);
                                    foreach ($features as $feature): 
                                    ?>
                                        <li><i class="fas fa-check"></i> <?php echo trim($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="<?php echo $product['id']; ?>">Learn More</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Us -->
	<section id="about" class="section-padding bg-light">
		<div class="container">
			<h2 class="section-title">
				<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'about', 'About Us')); ?>
			</h2>
			<div class="row align-items-center">
				<div class="col-lg-6 mb-4 mb-lg-0">
					<div class="about-image-container">
						<?php 
						$about_section = getWebsiteSection($pdo, 'about');
						$about_image = $about_section ? $about_section['image_path'] : '';
						?>
						<?php if ($about_image && file_exists($about_image)): ?>
							<img src="<?php echo $about_image; ?>" alt="About <?php echo htmlspecialchars($company_name); ?>" class="img-fluid about-image">
						<?php else: ?>
							<div class="about-image-placeholder">
								<i class="fas fa-building"></i>
								<p>About <?php echo htmlspecialchars($company_name); ?> Image</p>
							</div>
						<?php endif; ?>
						<div class="experience-badge">
							<div class="experience-years"><?php echo htmlspecialchars($years_of_experience); ?></div>
							<div class="experience-text">Years Experience</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-content">
						<?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'about', "We are " . $company_name . ", a leading solar energy company dedicated to providing sustainable energy solutions. With over 10 years of experience, we help homeowners and businesses transition to clean, renewable energy."))); ?>
					</div>
					<div class="stats mt-4">
							<div class="row">
								<div class="col-4 text-center">
									<div class="stat-number"><?php echo htmlspecialchars($projects_count); ?></div>
									<div class="stat-label">Projects</div>
								</div>
								<div class="col-4 text-center">
									<div class="stat-number"><?php echo htmlspecialchars($clients_count); ?></div>
									<div class="stat-label">Happy Clients</div>
								</div>
								<div class="col-4 text-center">
									<div class="stat-number"><?php echo htmlspecialchars($satisfaction_rate); ?></div>
									<div class="stat-label">Satisfaction</div>
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</section>

    <!-- Our Projects -->
    <section id="projects" class="section-padding">
        <div class="container">
            <h2 class="section-title"><?php echo htmlspecialchars(getWebsiteTitle($pdo, 'projects', 'Our Projects')); ?></h2>
            <p class="text-center mb-5"><?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'projects', ""))); ?></p>
            <div class="row">
                <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="project-card">
                        <div class="project-image">
                            <?php if ($project['image_path']): ?>
                                <img src="<?php echo $project['image_path']; ?>" alt="<?php echo $project['title']; ?>">
                            <?php else: ?>
                                <div class="project-image-placeholder">
                                    <i class="fas fa-solar-panel"></i>
                                </div>
                            <?php endif; ?>
                            <div class="project-overlay">
                                <div class="project-info">
                                    <h5><?php echo $project['title']; ?></h5>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></p>
                                    <p><i class="fas fa-bolt"></i> <?php echo $project['system_size']; ?></p>
                                    <?php if ($project['completion_date']): ?>
                                        <p><i class="fas fa-calendar"></i> <?php echo date('M Y', strtotime($project['completion_date'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="project-content">
                            <h4><?php echo $project['title']; ?></h4>
                            <p><?php echo $project['description']; ?></p>
                            <div class="project-meta">
                                <span class="location"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></span>
                                <span class="size"><i class="fas fa-bolt"></i> <?php echo $project['system_size']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="projects.php" class="btn btn-primary">View All Projects</a>
            </div>
        </div>
    </section>

    <!-- Customer Reviews Carousel -->
    <section id="reviews" class="section-padding reviews-section">
        <div class="container">
            <h2 class="section-title"><?php echo htmlspecialchars(getWebsiteTitle($pdo, 'review', 'What Our Customers Say')); ?></h2>
			<p class="text-center mb-5"><?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'review', ""))); ?></p>
            <div id="reviewsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($reviews as $index => $review): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="review-card">
                            <div class="review-content">
                                <div class="rating mb-3">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="review-text">"<?php echo $review['review_text']; ?>"</p>
                                <div class="customer-info mt-3">
                                    <?php if ($review['image_path']): ?>
                                        <img src="<?php echo $review['image_path']; ?>" alt="<?php echo $review['customer_name']; ?>" class="customer-avatar">
                                    <?php else: ?>
                                        <div class="customer-avatar default">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="customer-details">
                                        <strong class="customer-name"><?php echo $review['customer_name']; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
	<section id="why-choose" class="section-padding bg-light">
		<div class="container">
			<h2 class="section-title">
				<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'why_choose', 'Why Choose Us')); ?>
			</h2>
			<p class="text-center mb-5"><?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'why_choose', ""))); ?></p>
					
			<div class="row">
				<?php foreach ($why_us as $whyUs): ?>
				<div class="col-md-4 mb-4">
					<div class="feature-card text-center">
						<div class="feature-icon">
							<i class="<?php echo htmlspecialchars($whyUs['icon_class']); ?>"></i>
						</div>
						<h4><?php echo htmlspecialchars($whyUs['title']); ?></h4>
						<p><?php echo htmlspecialchars($whyUs['description']); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

    <!-- Contact Us -->
	<section id="contact" class="section-padding">
		<div class="container">
			<h2 class="section-title">
				<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'contact', 'Contact Us')); ?>
			</h2>
			<p class="text-center mb-5"><?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'contact', ""))); ?></p>
            <div class="row">
                <div class="col-lg-8">
                    <form class="contact-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" class="form-control" placeholder="Your Email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Subject">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
                <div class="col-lg-4">
					<div class="contact-info">
						<div class="contact-item">
							<i class="fas fa-envelope"></i>
							<div>
								<strong>Email</strong>
								<p><?php echo htmlspecialchars($company_email); ?></p>
							</div>
						</div>
						<div class="contact-item">
							<i class="fas fa-phone"></i>
							<div>
								<strong>Phone</strong>
								<p><?php echo htmlspecialchars($company_phone); ?></p>
							</div>
						</div>
						<div class="contact-item">
							<i class="fas fa-map-marker-alt"></i>
							<div>
								<strong>Address</strong>
								<p><?php echo htmlspecialchars($company_address); ?></p>
							</div>
						</div>
						<?php if (!empty($company_working_hours)): ?>
						<div class="contact-item">
							<i class="fas fa-clock"></i>
							<div>
								<strong>Working Hours</strong>
								<p><?php echo htmlspecialchars($company_working_hours); ?></p>
							</div>
						</div>
						<?php endif; ?>
					</div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Consultation Modal -->
    <div class="modal fade" id="consultationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Free Consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control" placeholder="Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" placeholder="Your Energy Needs" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Request Consultation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

	<!-- Floating Buttons -->
	<div class="floating-buttons">
		<!-- WhatsApp Floating Button -->
		<?php
		// Only show if enabled
		if ($whatsapp_enabled) {
			// Build display classes based on settings
			$display_classes = ['whatsapp-float'];
			
			// Show/hide based on device settings
			if (!$whatsapp_show_desktop) {
				$display_classes[] = 'd-none d-md-block';
			}
			if (!$whatsapp_show_mobile) {
				$display_classes[] = 'd-md-none';
			}
			
			// Add position class
			$position_class = 'whatsapp-position-' . $whatsapp_button_position;
			$display_classes[] = $position_class;
			
			// Build WhatsApp URL
			$whatsapp_url = "https://wa.me/{$whatsapp_number}?text=" . urlencode($whatsapp_welcome_message);
			
			// Output the button
			?>
			<a href="<?php echo $whatsapp_url; ?>" 
			   class="<?php echo implode(' ', $display_classes); ?>" 
			   target="_blank"
			   aria-label="Contact <?php echo htmlspecialchars($company_name); ?> on WhatsApp"
			   data-whatsapp-position="<?php echo $whatsapp_button_position; ?>"
			   onclick="trackWhatsAppClick()">
				<i class="fab fa-whatsapp"></i>
			</a>
			<?php
		}
		?>
		
		<!-- Back to Top Button -->
		<button class="back-to-top" aria-label="Back to top" id="backToTopBtn">
			<i class="fas fa-chevron-up"></i>
		</button>
	</div>

	<script>
	// WhatsApp tracking function (optional)
	function trackWhatsAppClick() {
		// Add your analytics tracking here
		console.log('WhatsApp button clicked - tracking event');
		
		// Example: Google Analytics 4
		// gtag('event', 'whatsapp_click', {
		//     'event_category': 'engagement',
		//     'event_label': 'whatsapp_contact'
		// });
		
		// Example: Facebook Pixel
		// fbq('track', 'Contact');
	}
	</script>
	
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('backToTopBtn');
            
            if (!backToTopBtn) {
                console.error('Back to top button not found!');
                return;
            }
            
            console.log('Back to top button initialized');
            
            // Show/hide button based on scroll position
            function toggleBackToTop() {
                const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollPosition > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            }
            
            // Smooth scroll to top
            function scrollToTop() {
                try {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } catch (error) {
                    // Fallback for older browsers
                    document.documentElement.scrollTop = 0;
                }
            }
            
            // Event listeners
            window.addEventListener('scroll', toggleBackToTop, { passive: true });
            backToTopBtn.addEventListener('click', scrollToTop);
            
            // Initial check
            toggleBackToTop();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>