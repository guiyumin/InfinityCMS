# Infinity CMS

A modern, lightweight CMS built with pure PHP, HTMX, and Alpine.js. No Composer, no jQuery - just clean, modern code.

## Features

- **PSR-4 Autoloading** - Custom autoloader without Composer
- **Plugin System** - Extend functionality with hooks and filters
- **Theme Support** - Beautiful, customizable themes
- **HTMX Integration** - Dynamic content without writing JavaScript
- **Alpine.js** - Reactive components made simple
- **Query Builder** - Fluent database queries
- **Routing** - Clean, expressive route definitions
- **MVC Architecture** - Organized and maintainable code structure

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache (with mod_rewrite) or Nginx

## Installation

1. Clone the repository

```bash
git clone <repo-url> infinity-cms
cd infinity-cms
```

2. Set up your web server to point to the `public` directory

3. Visit your site in a browser!

## Directory Structure

```
cms/
├─ autoloader.php         # PSR-4 autoloader
├─ functions.php          # Global helper functions
├─ .env.php              # Environment configuration
├─ bootstrap/
│  └─ app.php            # Application bootstrap
├─ public/
│  ├─ index.php          # Front controller
│  └─ .htaccess          # Apache rewrite rules
├─ app/
│  ├─ Core/              # Core framework classes
│  ├─ Http/
│  │  ├─ Controllers/    # Application controllers
│  │  └─ Middlewares/    # HTTP middlewares
│  └─ Models/            # Data models
├─ themes/
│  └─ infinity/          # Infinity theme
├─ plugins/
│  ├─ hello-world/       # Example plugin
│  └─ sitemap/           # Sitemap generator plugin
├─ config/
│  └─ routes.php         # Route definitions
└─ storage/
   ├─ cache/             # Cache files
   └─ logs/              # Log files
```

## Database Migrations

Migrations are managed through the admin dashboard at `/admin/migrations`.

### Creating a New Migration

Create a file in `database/migrations/` with format: `XXX_description.php`

```php
<?php
// database/migrations/006_add_tags_table.php

return [
    'up' => function($db) {
        $db->execute("
            CREATE TABLE IF NOT EXISTS tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },

    'down' => function($db) {
        $db->execute("DROP TABLE IF EXISTS tags");
    }
];
```

### Running Migrations

1. Visit `/admin/migrations` in your browser
2. Click "Run Migrations" to execute pending migrations
3. Use "Rollback" to undo the last batch
4. Use "Reset" to rollback all migrations (⚠️ destructive)

## Creating a Plugin

1. Create a new directory in `plugins/`
2. Create `manifest.php`:

```php
<?php
return [
    'name' => 'My Plugin',
    'version' => '1.0.0',
    'author' => 'Your Name',
    'description' => 'Plugin description',
];
```

3. Create `plugin.php`:

```php
<?php
use App\Core\Hook;

$hook = app('hook');

// Add action hook
$hook->addAction('theme_loaded', function() {
    // Your code here
});

// Add filter hook
$hook->addFilter('page_title', function($title) {
    return $title . ' - Modified';
});
```

## Creating a Theme

1. Create a new directory in `themes/`
2. Create the structure:

```
my-theme/
├─ theme.json
├─ layouts/
│  └─ base.php
├─ partials/
│  ├─ header.php
│  └─ footer.php
├─ pages/
│  └─ home.php
└─ assets/
   ├─ css/
   └─ js/
```

3. Update `.env.php` to use your theme:

```php
'app' => [
    'theme' => 'my-theme', // Default is 'infinity'
],
```

## Using HTMX

```html
<!-- Load content dynamically -->
<div hx-get="/api/posts/latest" hx-trigger="load">Loading...</div>

<!-- Submit form without page reload -->
<form hx-post="/api/posts" hx-target="#post-list">
  <input name="title" required />
  <button type="submit">Add Post</button>
</form>
```

## Using Alpine.js

```html
<!-- Interactive components -->
<div x-data="{ open: false }">
  <button @click="open = !open">Toggle</button>
  <div x-show="open">Content</div>
</div>
```

## Helper Functions

```php
// Get config value
config('app.name');

// Render view
view('home', ['title' => 'Home']);

// Database query
db()->table('posts')->where('status', 'published')->get();

// Generate URL
url('/about');

// Escape output
e($userInput);

// Check if logged in
is_logged_in();

// Flash message
flash('success', 'Post created!');
```

## License

MIT License

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
