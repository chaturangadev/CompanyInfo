<?php
// projects.php - View All Projects Page
include 'includes/config.php';

// Get all active projects with category names, ordered by project order and completion date
$projects = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM projects p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.is_active = TRUE 
    ORDER BY p.project_order ASC, p.completion_date DESC
")->fetchAll();

// Get unique categories for filtering
$categories = $pdo->query("
    SELECT DISTINCT c.id, c.name 
    FROM categories c 
    INNER JOIN projects p ON c.id = p.category_id 
    WHERE p.is_active = TRUE 
    ORDER BY c.name ASC
")->fetchAll();

// Set page title
$page_title = "Our Projects - " . $company_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <?php if (!empty($website_favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $website_favicon; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/projects.css"> <!-- Projects specific CSS -->
    <style>
        :root {
            --primary-color: <?php echo $colors['primary_color']; ?>;
            --secondary-color: <?php echo $colors['secondary_color']; ?>;
            --background-color: <?php echo $colors['background_color']; ?>;
            --text-color: <?php echo $colors['text_color']; ?>;
            --accent-color: <?php echo $colors['accent_color']; ?>;
        }
    </style>
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
                "name": "Projects",
                "item": "<?php echo $current_url; ?>"
            }
        ]
    }
    </script>
    <?php include 'includes/analytics.php'; ?>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbarNav">
    <?php include 'includes/header.php'; ?>
	
	<!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Projects</li>
            </ol>
        </div>
    </nav>

    <!-- Projects Section -->
    <section id="projects" class="section-padding">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="section-title"><?php echo htmlspecialchars(getWebsiteTitle($pdo, 'projects', 'Our Projects')); ?></h2>
                    <p class="text-center mb-4"><?php echo nl2br(htmlspecialchars(getWebsiteContent($pdo, 'projects', ""))); ?></p>
                    
                    <!-- Projects Filter -->
                    <div class="projects-filter text-center">
                        <button class="btn btn-primary filter-btn active" data-filter="all">All Projects</button>
                        <?php foreach ($categories as $category): ?>
                            <button class="btn btn-outline-primary filter-btn" 
                                    data-filter="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category['name']))); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="row" id="projectsGrid">
                <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <div class="no-projects">
                            <i class="fas fa-solar-panel"></i>
                            <h3>No Projects Available</h3>
                            <p>We're currently working on updating our project portfolio. Please check back soon!</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $index => $project): ?>
                    <div class="col-lg-4 col-md-6 mb-4 project-item" 
                         data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $project['category_name'] ?? 'uncategorized'))); ?>"
                         data-loaded="false">
                        <div class="card project-card h-100">
						<div class="project-image">
							<?php if ($project['image_path']): ?>
								<img 
									src="assets/images/placeholder.jpg" 
									data-src="<?php echo htmlspecialchars($project['image_path']); ?>" 
									alt="<?php echo htmlspecialchars($project['title']); ?>" 
									class="lazy-load"
									loading="lazy"
								>
							<?php else: ?>
								<div class="project-image-placeholder">
									<i class="fas fa-solar-panel"></i>
								</div>
							<?php endif; ?>
							
							<!-- Image Actions Buttons -->
							<div class="project-image-actions">
								<?php if (!empty($project['external_images'])): ?>
									<button class="btn view-images-btn" 
											data-bs-toggle="modal" 
											data-bs-target="#galleryModal"
											data-project-title="<?php echo htmlspecialchars($project['title']); ?>"
											data-images='<?php echo htmlspecialchars($project['external_images']); ?>'>
										<i class="fas fa-images me-2"></i>View Images
									</button>
								<?php endif; ?>
								
								<?php if (!empty($project['gallery_url'])): ?>
									<a href="<?php echo htmlspecialchars($project['gallery_url']); ?>" 
									   class="btn gallery-url-btn" 
									   target="_blank"
									   rel="noopener noreferrer">
										<i class="fas fa-external-link-alt me-2"></i>View Gallery
									</a>
								<?php endif; ?>
							</div>
						</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($project['description']); ?></p>
                                
                                <div class="project-meta">
                                    <span class="location">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?php echo htmlspecialchars($project['location']); ?>
                                    </span>
                                    <span class="size">
                                        <i class="fas fa-bolt"></i> 
                                        <?php echo htmlspecialchars($project['system_size']); ?>
                                    </span>
                                </div>
                                
                                <?php if ($project['completion_date']): ?>
                                    <div class="project-date mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Completed: <?php echo date('M Y', strtotime($project['completion_date'])); ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($project['category_name']): ?>
                                    <div class="project-category mt-2">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($project['category_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Loading Spinner for Lazy Load -->
            <div id="loadingSpinner" class="loading-spinner" style="display: none;">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading more projects...</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Gallery Modal -->
    <div class="modal fade gallery-modal" id="galleryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalTitle">Project Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="galleryCarouselInner">
                            <!-- Images will be loaded here dynamically -->
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="carousel-indicators gallery-indicators position-relative" id="galleryIndicators">
                        <!-- Indicators will be loaded here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Project Filtering
            const filterBtns = document.querySelectorAll('.filter-btn');
            const projectItems = document.querySelectorAll('.project-item');
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    projectItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.classList.remove('hidden');
                            // Trigger animation
                            item.style.opacity = '0';
                            setTimeout(() => {
                                item.style.opacity = '1';
                            }, 50);
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                });
            });
            
            // Lazy Loading Images
            const lazyImages = document.querySelectorAll('.lazy-load');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.getAttribute('data-src');
                        img.classList.remove('lazy-load');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(img => imageObserver.observe(img));
            
            // Gallery Modal Functionality
            const galleryModal = document.getElementById('galleryModal');
            if (galleryModal) {
                galleryModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const projectTitle = button.getAttribute('data-project-title');
                    const imagesJson = button.getAttribute('data-images');
                    
                    document.getElementById('galleryModalTitle').textContent = projectTitle + ' - Gallery';
                    
                    try {
                        const images = JSON.parse(imagesJson);
                        const carouselInner = document.getElementById('galleryCarouselInner');
                        const galleryIndicators = document.getElementById('galleryIndicators');
                        
                        // Clear previous content
                        carouselInner.innerHTML = '';
                        galleryIndicators.innerHTML = '';
                        
                        // Add images to carousel
                        images.forEach((image, index) => {
                            const isActive = index === 0;
                            
                            // Carousel item
                            const carouselItem = document.createElement('div');
                            carouselItem.className = `carousel-item ${isActive ? 'active' : ''}`;
                            carouselItem.innerHTML = `
                                <img src="${image}" class="d-block w-100" alt="${projectTitle} - Image ${index + 1}">
                            `;
                            carouselInner.appendChild(carouselItem);
                            
                            // Indicator
                            const indicator = document.createElement('img');
                            indicator.src = image;
                            indicator.alt = `Thumbnail ${index + 1}`;
                            indicator.className = `indicator-thumb ${isActive ? 'active' : ''}`;
                            indicator.setAttribute('data-bs-target', '#galleryCarousel');
                            indicator.setAttribute('data-bs-slide-to', index);
                            indicator.addEventListener('click', function() {
                                // Update active states
                                document.querySelectorAll('.indicator-thumb').forEach(thumb => {
                                    thumb.classList.remove('active');
                                });
                                this.classList.add('active');
                            });
                            galleryIndicators.appendChild(indicator);
                        });
                    } catch (error) {
                        console.error('Error parsing gallery images:', error);
                        document.getElementById('galleryCarouselInner').innerHTML = `
                            <div class="carousel-item active">
                                <div class="text-center p-5">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <p>Unable to load gallery images.</p>
                                </div>
                            </div>
                        `;
                    }
                });
            }
            
            // Back to Top Button
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
        
        // WhatsApp Tracking
        function trackWhatsAppClick() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_click', {
                    'event_category': 'Contact',
                    'event_label': 'Projects Page'
                });
            }
        }
    </script>
</body>
</html>