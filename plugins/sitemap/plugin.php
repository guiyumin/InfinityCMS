<?php
/**
 * Sitemap Plugin
 *
 * Generates XML sitemap for SEO
 */

use App\Core\Hook;

$hook = app('hook');
$router = app('router');

// Register sitemap route
$router->get('/sitemap.xml', function() {
    $response = app('response');
    $db = db();

    // Get all published posts
    $posts = $db->table('posts')
        ->where('status', 'published')
        ->orderBy('updated_at', 'DESC')
        ->get();

    // Generate XML
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Add homepage
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . url('/') . '</loc>' . "\n";
    $xml .= '    <changefreq>daily</changefreq>' . "\n";
    $xml .= '    <priority>1.0</priority>' . "\n";
    $xml .= '  </url>' . "\n";

    // Add posts
    foreach ($posts as $post) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . url('/post/' . $post['slug']) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('c', strtotime($post['updated_at'])) . '</lastmod>' . "\n";
        $xml .= '    <changefreq>weekly</changefreq>' . "\n";
        $xml .= '    <priority>0.8</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    $xml .= '</urlset>';

    // Send XML response
    $response->setHeader('Content-Type', 'application/xml');
    $response->setContent($xml);
    $response->send();
});

// Add to admin menu
$hook->addAction('admin_menu', function($menu) {
    $menu[] = [
        'title' => 'Sitemap',
        'url' => '/sitemap.xml',
        'icon' => 'map',
    ];
    return $menu;
});
