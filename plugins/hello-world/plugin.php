<?php
/**
 * Hello World Plugin
 *
 * This is a simple example plugin that demonstrates how to:
 * - Register hooks
 * - Add filters
 * - Extend functionality
 */

use App\Core\Hook;

// Get hook instance
$hook = app('hook');

// Add an action hook - executed when theme renders
$hook->addAction('theme_loaded', function() {
    // Plugin initialization code here
    error_log('Hello World plugin loaded!');
});

// Add a filter hook - modify content before rendering
$hook->addFilter('page_title', function($title) {
    // Add plugin name to page title
    return $title . ' | Hello World Plugin Active';
});

// Add to admin menu (example)
$hook->addAction('admin_menu', function($menu) {
    $menu[] = [
        'title' => 'Hello World',
        'url' => '/admin/hello-world',
        'icon' => 'star',
    ];
    return $menu;
});

// Modify content (example)
$hook->addFilter('post_content', function($content) {
    // Add a message at the beginning of each post
    return '<div class="plugin-message">This content is enhanced by Hello World Plugin!</div>' . $content;
}, 10);

// Custom route registration (example)
app('router')->get('/hello', function() {
    return view('home', [
        'title' => 'Hello from Plugin!',
        'message' => 'This page was created by the Hello World plugin!',
    ]);
});
