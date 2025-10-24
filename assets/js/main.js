// Universal Navigation and Back to Top Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Back to Top Button
    initBackToTop();
    
    // Initialize Navigation
    initNavigation();
    
    // Initialize WhatsApp Button
    initWhatsAppButton();
    
    // Update floating buttons positioning
    updateFloatingButtonsPosition();
});

// Back to Top Functionality
function initBackToTop() {
    const backToTopBtn = document.querySelector('.back-to-top') || document.getElementById('backToTopBtn');
    
    if (!backToTopBtn) {
        console.log('Back to top button not found');
        return;
    }
    
    function toggleBackToTop() {
        const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollPosition > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    }
    
    function scrollToTop() {
        try {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } catch (error) {
            document.documentElement.scrollTop = 0;
        }
    }
    
    window.addEventListener('scroll', toggleBackToTop);
    backToTopBtn.addEventListener('click', scrollToTop);
    
    toggleBackToTop();
}

// Navigation Functionality
function initNavigation() {
    // Handle all navigation links
    const navLinks = document.querySelectorAll('a[href*="#"], a.external-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip if it's an external link without hash or admin link
            if (!href.includes('#') || href.includes('admin/')) {
                return;
            }
            
            // Check if link points to current page or homepage
            if (href.includes('index.php#') || href.startsWith('#')) {
                e.preventDefault();
                
                let targetId, targetPage;
                
                if (href.includes('index.php#')) {
                    // External link to homepage section
                    targetPage = 'index.php';
                    targetId = href.split('#')[1];
                } else {
                    // Internal anchor link
                    targetId = href.substring(1);
                    targetPage = window.location.pathname;
                }
                
                const currentPage = window.location.pathname;
                const isHomepage = currentPage.endsWith('index.php') || currentPage.endsWith('/');
                
                if (!isHomepage && targetPage === 'index.php') {
                    // Redirect to homepage with anchor
                    window.location.href = `index.php#${targetId}`;
                } else {
                    // Scroll to section on current page
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Close mobile menu if open
                        closeMobileMenu();
                    }
                }
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.navbar-collapse') && !e.target.closest('.navbar-toggler')) {
            closeMobileMenu();
        }
    });
}

// Close mobile menu function
function closeMobileMenu() {
    const navbarCollapse = document.querySelector('.navbar-collapse');
    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
        bsCollapse.hide();
    }
}

// WhatsApp Button Functionality
function initWhatsAppButton() {
    const whatsappBtn = document.querySelector('.whatsapp-float');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', function(e) {
            // Optional: Add analytics tracking
            console.log('WhatsApp button clicked');
            
            // You can add tracking code here:
            // gtag('event', 'whatsapp_click', {
            //     'event_category': 'engagement',
            //     'event_label': 'whatsapp_contact'
            // });
        });
        
        // Add hover effects
        whatsappBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.animation = 'none';
        });
        
        whatsappBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.animation = 'whatsapp-pulse 2s infinite';
        });
    }
}

// Update floating buttons positioning based on WhatsApp configuration
function updateFloatingButtonsPosition() {
    const whatsappButton = document.querySelector('.whatsapp-float');
    const backToTopButton = document.querySelector('.back-to-top') || document.getElementById('backToTopBtn');
    
    if (whatsappButton && backToTopButton) {
        const whatsappPosition = whatsappButton.classList.contains('whatsapp-position-bottom-right') ? 'bottom-right' :
                                whatsappButton.classList.contains('whatsapp-position-bottom-left') ? 'bottom-left' :
                                whatsappButton.classList.contains('whatsapp-position-top-right') ? 'top-right' :
                                whatsappButton.classList.contains('whatsapp-position-top-left') ? 'top-left' : 'bottom-right';
        
        // Reset any previous positioning
        backToTopButton.style.bottom = '';
        backToTopButton.style.right = '';
        backToTopButton.style.left = '';
        
        // Adjust back-to-top position based on WhatsApp button position
        if (whatsappPosition === 'bottom-right') {
            backToTopButton.style.bottom = '90px';
            backToTopButton.style.right = '20px';
        } else if (whatsappPosition === 'bottom-left') {
            backToTopButton.style.bottom = '20px';
            backToTopButton.style.right = '20px';
        }
        // For top positions, back-to-top stays in default bottom-right position
        
        console.log(`WhatsApp button positioned: ${whatsappPosition}`);
    }
}

// Handle responsive adjustments
function handleResponsiveAdjustments() {
    const whatsappButton = document.querySelector('.whatsapp-float');
    const backToTopButton = document.querySelector('.back-to-top');
    
    if (!whatsappButton || !backToTopButton) return;
    
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Mobile adjustments
        if (whatsappButton.classList.contains('whatsapp-position-bottom-right')) {
            backToTopButton.style.bottom = '80px';
            backToTopButton.style.right = '15px';
        } else if (whatsappButton.classList.contains('whatsapp-position-bottom-left')) {
            backToTopButton.style.bottom = '15px';
            backToTopButton.style.right = '15px';
        }
    } else {
        // Desktop adjustments
        if (whatsappButton.classList.contains('whatsapp-position-bottom-right')) {
            backToTopButton.style.bottom = '90px';
            backToTopButton.style.right = '20px';
        } else if (whatsappButton.classList.contains('whatsapp-position-bottom-left')) {
            backToTopButton.style.bottom = '20px';
            backToTopButton.style.right = '20px';
        }
    }
}

// Handle page load with hash in URL
window.addEventListener('load', function() {
    const hash = window.location.hash;
    if (hash) {
        setTimeout(() => {
            const target = document.querySelector(hash);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 100);
    }
    
    // Handle responsive adjustments on load and resize
    handleResponsiveAdjustments();
    window.addEventListener('resize', handleResponsiveAdjustments);
});

// Export functions for global access (if needed)
window.FloatingButtons = {
    initBackToTop,
    initWhatsAppButton,
    updateFloatingButtonsPosition,
    handleResponsiveAdjustments
};