<?php
/**
 * Seed sample posts
 */

function up($db) {
    $samplePosts = [
        [
            'title' => 'Welcome to Infinity CMS',
            'slug' => 'welcome-to-infinity-cms',
            'content' => '<p>Welcome to Infinity CMS! This is a modern, lightweight content management system built with PHP, HTMX, and Alpine.js.</p><p>Features include a powerful plugin system, beautiful themes, and a great developer experience.</p>',
            'excerpt' => 'Welcome to Infinity CMS - a modern, lightweight CMS',
            'author' => 'Admin',
            'status' => 'published',
        ],
        [
            'title' => 'Getting Started with Plugins',
            'slug' => 'getting-started-with-plugins',
            'content' => '<p>Creating plugins for Infinity CMS is easy! Just create a directory in the plugins folder with a manifest.php and plugin.php file.</p><p>You can use hooks and filters to extend functionality without modifying core files.</p>',
            'excerpt' => 'Learn how to create plugins for Infinity CMS',
            'author' => 'Admin',
            'status' => 'published',
        ],
        [
            'title' => 'Customizing Your Theme',
            'slug' => 'customizing-your-theme',
            'content' => '<p>Themes in Infinity CMS are simple to create and customize. Use HTMX for dynamic content and Alpine.js for interactivity.</p><p>No need for complex build tools - just pure HTML, CSS, and minimal JavaScript.</p>',
            'excerpt' => 'Learn how to customize themes',
            'author' => 'Admin',
            'status' => 'draft',
        ],
    ];

    foreach ($samplePosts as $post) {
        $db->table('posts')->insert($post);
    }
}

function down($db) {
    // Delete sample posts
    $db->table('posts')->where('slug', 'welcome-to-infinity-cms')->delete();
    $db->table('posts')->where('slug', 'getting-started-with-plugins')->delete();
    $db->table('posts')->where('slug', 'customizing-your-theme')->delete();
}
