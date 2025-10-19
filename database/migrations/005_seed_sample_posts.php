<?php
/**
 * Seed sample posts
 */

return [
    'up' => function($db) {
        // Insert sample posts
        $db->execute("
            INSERT INTO posts (title, slug, content, excerpt, author, status)
            VALUES
            (
                'Welcome to Infinity CMS',
                'welcome-to-infinity-cms',
                'Welcome to **Infinity CMS**! This is a modern, lightweight content management system built with PHP, HTMX, and Alpine.js.

Features include a powerful plugin system, beautiful themes, and a great developer experience.',
                'Welcome to Infinity CMS - a modern, lightweight CMS',
                'Admin',
                'published'
            ),
            (
                'Getting Started with Plugins',
                'getting-started-with-plugins',
                '# Getting Started with Plugins

Creating plugins for Infinity CMS is easy! Just create a directory in the plugins folder with a `manifest.php` and `plugin.php` file.

## Key Features
- Use hooks and filters to extend functionality
- No need to modify core files
- Simple and intuitive API

You can use hooks and filters to extend functionality without modifying core files.',
                'Learn how to create plugins for Infinity CMS',
                'Admin',
                'published'
            ),
            (
                'Customizing Your Theme',
                'customizing-your-theme',
                '# Customizing Your Theme

Themes in Infinity CMS are simple to create and customize. Use **HTMX** for dynamic content and **Alpine.js** for interactivity.

## Benefits
- No complex build tools required
- Pure HTML, CSS, and minimal JavaScript
- Fast and responsive

No need for complex build tools - just pure HTML, CSS, and minimal JavaScript.',
                'Learn how to customize themes',
                'Admin',
                'draft'
            )
        ");

        // Insert sample comments
        $db->execute("
            INSERT INTO comments (post_id, author_name, author_email, content, status)
            VALUES
            (1, 'John Doe', 'john@example.com', 'Hello World! This CMS looks amazing!', 'approved'),
            (1, 'Jane Smith', 'jane@example.com', 'Great work! Looking forward to using this.', 'approved'),
            (1, 'Bob Wilson', 'bob@example.com', 'Simple and elegant. Love it!', 'approved'),
            (2, 'Alice Brown', 'alice@example.com', 'The plugin system is really easy to understand. Thanks for the tutorial!', 'approved'),
            (2, 'Charlie Davis', 'charlie@example.com', 'Can''t wait to build my first plugin!', 'approved'),
            (2, 'Eve Martinez', 'eve@example.com', 'This is exactly what I needed. Clear and concise.', 'pending')
        ");
    },

    'down' => function($db) {
        // Delete sample comments (will cascade when posts are deleted)
        $db->execute("
            DELETE FROM comments
            WHERE post_id IN (1, 2, 3)
        ");

        // Delete sample posts
        $db->execute("
            DELETE FROM posts
            WHERE slug IN (
                'welcome-to-infinity-cms',
                'getting-started-with-plugins',
                'customizing-your-theme'
            )
        ");
    }
];
