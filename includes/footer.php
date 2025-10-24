<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h4><?php echo htmlspecialchars($company_name); ?></h4>
                <p>Providing sustainable, efficient, and affordable solar solutions for your home and business.</p>
                <div class="social-links">
                    <?php if (!empty($social_facebook)): ?>
                        <a href="<?php echo htmlspecialchars($social_facebook); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social_x)): ?>
                        <a href="<?php echo htmlspecialchars($social_x); ?>" target="_blank"><i class="fab fa-x-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social_instagram)): ?>
                        <a href="<?php echo htmlspecialchars($social_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social_linkedin)): ?>
                        <a href="<?php echo htmlspecialchars($social_linkedin); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social_youtube)): ?>
                        <a href="<?php echo htmlspecialchars($social_youtube); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social_whatsapp)): ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($social_whatsapp); ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li>
					    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
							href="<?php echo $is_homepage ? '#hero' : 'index.php'; ?>" 
							style="color: <?php echo $header_text_color; ?>;">
							Home
						</a>
					</li>
                    <li>
						<a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
						   href="<?php echo $is_homepage ? '#products' : 'index.php#products'; ?>" 
						   style="color: <?php echo $header_text_color; ?>;">
						   <?php echo htmlspecialchars(getWebsiteTitle($pdo, 'products', 'Our Products & Services')); ?>
						</a>
					</li>
                    <li>
					   <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
							href="<?php echo $is_homepage ? '#about' : 'index.php#about'; ?>" 
							style="color: <?php echo $header_text_color; ?>;">
							<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'about', 'About Us')); ?>
						</a>
					</li>
                    <li>
						<a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
							href="<?php echo $is_homepage ? '#projects' : 'index.php#projects'; ?>" 
							style="color: <?php echo $header_text_color; ?>;">
							<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'projects', 'Our Projects')); ?>
						</a>
					</li>
                    <li>
					    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
							href="<?php echo $is_homepage ? '#reviews' : 'index.php#reviews'; ?>" 
							style="color: <?php echo $header_text_color; ?>;">
							<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'review', 'Reviews')); ?>
						</a>
					</li>
                    <li>                    
						<a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
							href="<?php echo $is_homepage ? '#why-choose' : 'index.php#why-choose'; ?>" 
							style="color: <?php echo $header_text_color; ?>;">
							<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'why_choose', 'Why Choose Us')); ?>
						</a>
					</li>
					    <a class="nav-link <?php echo $is_homepage ? '' : 'external-link'; ?>" 
							href="<?php echo $is_homepage ? '#contact' : 'index.php#contact'; ?>" 
							style="color: <?php echo $header_text_color; ?>;">
							<?php echo htmlspecialchars(getWebsiteTitle($pdo, 'contact', 'Contact Us')); ?>
						</a>
					<li>
					</li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Our Services</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-light text-decoration-none">Residential Solar</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Commercial Solar</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Solar Maintenance</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Energy Consulting</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Contact Info</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($company_address); ?></li>
                    <li><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($company_phone); ?></li>
                    <li><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($company_email); ?></li>
                    <li><i class="fas fa-clock me-2"></i> <?php echo htmlspecialchars($company_working_hours); ?></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Full Width Copyright Section -->
    <div class="copyright-full">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($company_name); ?>. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="privacy-policy.php" class="text-light text-decoration-none me-3">Privacy Policy</a>
                    <a href="terms-of-service.php" class="text-light text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>
</footer>