<?php
include __DIR__ . '/../includes/config.php';

// Always return JSON for this file
header('Content-Type: application/json');

try {
    $base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Add homepage
    $sitemap .= '
    <url>
        <loc>' . $base_url . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>';
    
    // Add static pages
    $pages = [
        'privacy-policy.php',
        'terms-of-service.php'
    ];
    
    foreach ($pages as $page) {
        $sitemap .= '
        <url>
            <loc>' . $base_url . '/' . $page . '</loc>
            <lastmod>' . date('Y-m-d') . '</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>';
    }
    
    $sitemap .= '</urlset>';
    
    // Save to file
    $result = file_put_contents('../sitemap.xml', $sitemap);
    
    if ($result !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Sitemap generated successfully with ' . (count($pages) + 1) . ' URLs',
            'file_size' => $result
        ]);
    } else {
        throw new Exception('Could not write sitemap.xml file. Check directory permissions.');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>