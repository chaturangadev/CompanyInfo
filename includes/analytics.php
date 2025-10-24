<?php
// Google Analytics Tracking Code - ONLY FOR FRONTEND PAGES
// Check if we're not in admin area
$is_admin_page = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);

if (!$is_admin_page) {
    $google_analytics_id = $settings['google_analytics_id'] ?? '';
    $google_analytics_status = $settings['google_analytics_status'] ?? 'disabled';
    $google_analytics_anonymize_ip = $settings['google_analytics_anonymize_ip'] ?? '1';
    $google_analytics_remarketing = $settings['google_analytics_remarketing'] ?? '0';
    $google_analytics_enhanced_link_attribution = $settings['google_analytics_enhanced_link_attribution'] ?? '1';
    $google_analytics_cross_domain_tracking = $settings['google_analytics_cross_domain_tracking'] ?? '0';

    if ($google_analytics_status === 'enabled' && !empty($google_analytics_id)): 
    ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($google_analytics_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        
        gtag('config', '<?php echo htmlspecialchars($google_analytics_id); ?>', {
            anonymize_ip: <?php echo $google_analytics_anonymize_ip === '1' ? 'true' : 'false'; ?>,
            allow_google_signals: <?php echo $google_analytics_remarketing === '1' ? 'true' : 'false'; ?>,
            allow_ad_personalization_signals: <?php echo $google_analytics_remarketing === '1' ? 'true' : 'false'; ?>
        });
        
        <?php if ($google_analytics_enhanced_link_attribution === '1'): ?>
        // Enhanced Link Attribution
        gtag('set', 'link_attribution', true);
        <?php endif; ?>
        
        <?php if ($google_analytics_cross_domain_tracking === '1'): ?>
        // Cross-Domain Tracking (add your domains)
        gtag('config', '<?php echo htmlspecialchars($google_analytics_id); ?>', {
            linker: {
                domains: ['your-domain.com', 'another-domain.com']
            }
        });
        <?php endif; ?>
    </script>
    <?php 
    endif;
}
?>