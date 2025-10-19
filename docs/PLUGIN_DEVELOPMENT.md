# Plugin Development Guide

## Introduction

Infinity CMS provides a powerful plugin system that allows you to extend and modify the core functionality without changing the core files. Plugins can hook into various points in the application lifecycle, modify data, add new features, and integrate with external services.

## Table of Contents
- [Plugin Structure](#plugin-structure)
- [Creating Your First Plugin](#creating-your-first-plugin)
- [Hook System](#hook-system)
- [Available Hooks](#available-hooks)
- [Plugin API](#plugin-api)
- [Database Operations](#database-operations)
- [Admin Panel Integration](#admin-panel-integration)
- [Best Practices](#best-practices)
- [Examples](#examples)

## Plugin Structure

Every plugin must follow this structure:
```
plugins/
└── your-plugin/
    ├── manifest.php          # Plugin metadata (required)
    ├── plugin.php           # Main plugin file (required)
    ├── assets/              # CSS, JS, images
    │   ├── css/
    │   ├── js/
    │   └── images/
    ├── views/               # Plugin view templates
    ├── languages/           # Translation files
    ├── includes/            # Additional PHP files
    └── readme.md            # Documentation
```

## Creating Your First Plugin

### Step 1: Create Plugin Directory
```bash
mkdir plugins/hello-world
cd plugins/hello-world
```

### Step 2: Create Manifest File
Create `manifest.php`:
```php
<?php
return [
    'name' => 'Hello World',
    'version' => '1.0.0',
    'author' => 'Your Name',
    'author_url' => 'https://yourwebsite.com',
    'description' => 'A simple hello world plugin for Infinity CMS',
    'requires' => '1.0.0', // Minimum CMS version
    'namespace' => 'HelloWorld', // Optional: for autoloading
    'settings' => true, // Has settings page
    'hooks' => [ // Declare which hooks this plugin uses
        'init',
        'theme_loaded',
        'admin_menu'
    ]
];
```

### Step 3: Create Main Plugin File
Create `plugin.php`:
```php
<?php
/**
 * Hello World Plugin
 *
 * @package HelloWorld
 * @author Your Name
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('INFINITY_CMS')) {
    die('Direct access not permitted');
}

use App\Core\Hook;
use App\Core\Plugin;

class HelloWorldPlugin extends Plugin
{
    /**
     * Plugin activation
     */
    public function activate()
    {
        // Code to run when plugin is activated
        $this->createDatabaseTables();
        $this->setDefaultOptions();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate()
    {
        // Code to run when plugin is deactivated
        // Note: Don't delete data here, use uninstall() for that
    }

    /**
     * Plugin uninstall
     */
    public function uninstall()
    {
        // Complete cleanup - remove all plugin data
        $this->dropDatabaseTables();
        $this->deleteOptions();
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        $hook = app('hook');

        // Add hooks
        $hook->addAction('init', [$this, 'onInit']);
        $hook->addAction('wp_head', [$this, 'addStyles']);
        $hook->addFilter('the_content', [$this, 'filterContent']);

        // Register shortcodes
        $this->registerShortcodes();

        // Add admin menu items
        if (is_admin()) {
            $hook->addAction('admin_menu', [$this, 'addAdminMenu']);
        }
    }

    /**
     * Plugin initialization hook
     */
    public function onInit()
    {
        // Register post types, taxonomies, etc.
    }

    /**
     * Filter content
     */
    public function filterContent($content)
    {
        // Modify and return content
        return $content . '<p>Hello from plugin!</p>';
    }

    /**
     * Add admin menu
     */
    public function addAdminMenu()
    {
        add_menu_page(
            'Hello World',
            'Hello World',
            'manage_options',
            'hello-world',
            [$this, 'renderAdminPage'],
            'dashicons-smiley',
            30
        );
    }

    /**
     * Render admin page
     */
    public function renderAdminPage()
    {
        include __DIR__ . '/views/admin.php';
    }

    private function createDatabaseTables()
    {
        db()->execute("
            CREATE TABLE IF NOT EXISTS hello_world_data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                data TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    private function dropDatabaseTables()
    {
        db()->execute("DROP TABLE IF EXISTS hello_world_data");
    }
}

// Initialize plugin
$helloWorldPlugin = new HelloWorldPlugin();
$helloWorldPlugin->init();
```

## Hook System

### Action Hooks
Action hooks allow you to execute code at specific points:

```php
// Add an action
$hook->addAction('hook_name', 'callback_function', $priority = 10, $args = 1);

// With closure
$hook->addAction('init', function() {
    // Your code here
});

// With class method
$hook->addAction('init', [$this, 'methodName']);

// Execute an action
$hook->doAction('hook_name', $arg1, $arg2);
```

### Filter Hooks
Filter hooks allow you to modify data:

```php
// Add a filter
$hook->addFilter('filter_name', 'callback_function', $priority = 10, $args = 1);

// Example: Modify post title
$hook->addFilter('post_title', function($title, $post) {
    return $title . ' - Modified';
}, 10, 2);

// Apply a filter
$filtered_value = $hook->applyFilter('filter_name', $value, $arg1, $arg2);
```

### Creating Custom Hooks
In your plugin, you can create custom hooks for other plugins to use:

```php
// Create an action hook
$hook->doAction('my_plugin_before_process', $data);

// Create a filter hook
$processed = $hook->applyFilter('my_plugin_process_data', $data, $context);
```

## Available Hooks

### System Hooks

#### Actions
- `init` - Fires after CMS core is loaded
- `plugins_loaded` - All plugins have been loaded
- `theme_loaded` - Theme has been loaded
- `shutdown` - Before PHP shutdown

#### Filters
- `plugin_row_meta` - Plugin listing metadata
- `plugin_action_links` - Plugin action links

### Request Lifecycle Hooks

#### Actions
- `parse_request` - After request is parsed
- `send_headers` - Before headers are sent
- `template_redirect` - Before template is loaded

#### Filters
- `request` - Filter the request array
- `query_vars` - Filter query variables

### Content Hooks

#### Actions
- `before_content` - Before main content
- `after_content` - After main content
- `the_content` - When content is displayed
- `save_post` - After post is saved
- `delete_post` - Before post is deleted
- `publish_post` - When post is published

#### Filters
- `the_title` - Filter post title
- `the_content` - Filter post content
- `the_excerpt` - Filter post excerpt
- `post_class` - Filter post CSS classes

### User Hooks

#### Actions
- `user_register` - After user registration
- `user_login` - After successful login
- `user_logout` - After logout
- `profile_update` - After profile update

#### Filters
- `authenticate` - Filter authentication
- `login_redirect` - Filter login redirect URL

### Admin Hooks

#### Actions
- `admin_init` - Admin area initialization
- `admin_menu` - Add admin menu items
- `admin_head` - Add to admin <head>
- `admin_footer` - Add to admin footer
- `admin_notices` - Display admin notices

#### Filters
- `admin_title` - Filter admin page title
- `admin_body_class` - Filter admin body classes

## Plugin API

### Options API
Store and retrieve plugin settings:

```php
// Set option
set_option('my_plugin_settings', $value);

// Get option
$settings = get_option('my_plugin_settings', $default);

// Update option
update_option('my_plugin_settings', $new_value);

// Delete option
delete_option('my_plugin_settings');
```

### Transients API
Store temporary data with expiration:

```php
// Set transient (cache for 1 hour)
set_transient('my_plugin_cache', $data, 3600);

// Get transient
$cached = get_transient('my_plugin_cache');

// Delete transient
delete_transient('my_plugin_cache');
```

### Shortcode API
Register shortcodes for content:

```php
// Register shortcode
add_shortcode('hello', function($atts, $content = null) {
    $atts = shortcode_atts([
        'name' => 'World'
    ], $atts);

    return '<p>Hello ' . esc_html($atts['name']) . '!</p>';
});

// Usage in content: [hello name="John"]
```

### Widget API
Create custom widgets:

```php
class HelloWidget extends Widget
{
    public function __construct()
    {
        parent::__construct(
            'hello_widget',
            'Hello Widget',
            ['description' => 'A simple hello widget']
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo '<h3>' . $instance['title'] . '</h3>';
        echo '<p>Hello from widget!</p>';
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = $instance['title'] ?? 'Hello';
        ?>
        <p>
            <label>Title:</label>
            <input type="text" name="<?= $this->get_field_name('title') ?>"
                   value="<?= esc_attr($title) ?>">
        </p>
        <?php
    }
}

// Register widget
register_widget('HelloWidget');
```

## Database Operations

### Using the Query Builder
```php
// Select
$results = db()->table('my_plugin_table')
               ->where('status', 'active')
               ->orderBy('created_at', 'desc')
               ->get();

// Insert
$id = db()->table('my_plugin_table')->insert([
    'name' => 'Test',
    'value' => 'data'
]);

// Update
db()->table('my_plugin_table')
     ->where('id', $id)
     ->update(['value' => 'updated']);

// Delete
db()->table('my_plugin_table')
     ->where('id', $id)
     ->delete();
```

### Creating Custom Tables
```php
public function createTables()
{
    $prefix = db()->getPrefix();

    db()->execute("
        CREATE TABLE IF NOT EXISTS {$prefix}my_plugin_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES {$prefix}users(id) ON DELETE CASCADE,
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}
```

## Admin Panel Integration

### Adding Menu Pages
```php
// Add top-level menu
add_menu_page(
    'Page Title',        // Page title
    'Menu Title',        // Menu title
    'manage_options',    // Capability
    'menu-slug',         // Menu slug
    'callback_function', // Callback
    'dashicons-admin-generic', // Icon
    30                   // Position
);

// Add submenu
add_submenu_page(
    'parent-slug',       // Parent slug
    'Page Title',        // Page title
    'Menu Title',        // Menu title
    'manage_options',    // Capability
    'submenu-slug',      // Menu slug
    'callback_function'  // Callback
);
```

### Creating Settings Pages
```php
class MyPluginSettings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function addSettingsPage()
    {
        add_options_page(
            'My Plugin Settings',
            'My Plugin',
            'manage_options',
            'my-plugin',
            [$this, 'renderSettingsPage']
        );
    }

    public function registerSettings()
    {
        register_setting('my_plugin_group', 'my_plugin_options', [
            'sanitize_callback' => [$this, 'sanitizeOptions']
        ]);

        add_settings_section(
            'my_plugin_general',
            'General Settings',
            [$this, 'renderSectionInfo'],
            'my-plugin'
        );

        add_settings_field(
            'api_key',
            'API Key',
            [$this, 'renderApiKeyField'],
            'my-plugin',
            'my_plugin_general'
        );
    }

    public function renderSettingsPage()
    {
        ?>
        <div class="wrap">
            <h1>My Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('my_plugin_group'); ?>
                <?php do_settings_sections('my-plugin'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function sanitizeOptions($input)
    {
        $sanitized = [];
        $sanitized['api_key'] = sanitize_text_field($input['api_key']);
        return $sanitized;
    }
}
```

### Adding Admin Notices
```php
// Success notice
add_action('admin_notices', function() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Settings saved successfully!</p>
    </div>
    <?php
});

// Error notice
add_action('admin_notices', function() {
    ?>
    <div class="notice notice-error">
        <p>An error occurred. Please try again.</p>
    </div>
    <?php
});
```

## AJAX in Plugins

### Register AJAX Handler
```php
// For logged-in users
add_action('wp_ajax_my_action', 'my_ajax_handler');

// For non-logged-in users
add_action('wp_ajax_nopriv_my_action', 'my_ajax_handler');

function my_ajax_handler()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
        wp_die('Security check failed');
    }

    // Process request
    $data = $_POST['data'];
    $result = process_data($data);

    // Return JSON response
    wp_send_json_success($result);
}
```

### JavaScript Side
```javascript
jQuery(document).ready(function($) {
    $('#my-button').click(function() {
        $.ajax({
            url: ajaxurl, // Provided by WordPress
            type: 'POST',
            data: {
                action: 'my_action',
                nonce: my_plugin_ajax.nonce,
                data: 'some data'
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.data);
                }
            }
        });
    });
});
```

### Enqueue Scripts with Localization
```php
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script(
        'my-plugin-admin',
        plugin_url('assets/js/admin.js'),
        ['jquery'],
        '1.0.0',
        true
    );

    wp_localize_script('my-plugin-admin', 'my_plugin_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('my_ajax_nonce')
    ]);
});
```

## Best Practices

### 1. Security

#### Nonce Verification
Always verify nonces for forms and AJAX:
```php
// Create nonce
$nonce = wp_create_nonce('my_action');

// Verify nonce
if (!wp_verify_nonce($_POST['_wpnonce'], 'my_action')) {
    die('Security check failed');
}
```

#### Capability Checks
Check user permissions:
```php
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}
```

#### Data Sanitization
Always sanitize input:
```php
$title = sanitize_text_field($_POST['title']);
$content = wp_kses_post($_POST['content']);
$url = esc_url($_POST['url']);
$id = intval($_POST['id']);
```

#### Data Escaping
Always escape output:
```php
echo esc_html($title);
echo esc_attr($attribute);
echo esc_url($url);
echo esc_js($javascript);
```

### 2. Performance

#### Lazy Loading
Only load plugin code when needed:
```php
if (is_admin()) {
    require_once __DIR__ . '/admin/admin-functions.php';
} else {
    require_once __DIR__ . '/public/public-functions.php';
}
```

#### Caching
Use transients for expensive operations:
```php
$data = get_transient('my_plugin_expensive_data');
if ($data === false) {
    $data = expensive_operation();
    set_transient('my_plugin_expensive_data', $data, DAY_IN_SECONDS);
}
```

#### Asset Loading
Only load assets on pages that need them:
```php
add_action('admin_enqueue_scripts', function($hook) {
    // Only load on our plugin pages
    if (strpos($hook, 'my-plugin') === false) {
        return;
    }

    wp_enqueue_style('my-plugin-admin', plugin_url('assets/css/admin.css'));
    wp_enqueue_script('my-plugin-admin', plugin_url('assets/js/admin.js'));
});
```

### 3. Internationalization

Make your plugin translatable:
```php
// Load text domain
add_action('plugins_loaded', function() {
    load_plugin_textdomain(
        'my-plugin',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
});

// Use translation functions
echo __('Hello World', 'my-plugin');
echo _e('Hello World', 'my-plugin'); // Echo directly
echo _n('One item', '%d items', $count, 'my-plugin');
echo esc_html__('Hello World', 'my-plugin'); // Escaped
```

### 4. Error Handling

Implement proper error handling:
```php
try {
    // Risky operation
    $result = risky_operation();
} catch (\Exception $e) {
    // Log error
    error_log('My Plugin Error: ' . $e->getMessage());

    // Show user-friendly message
    add_action('admin_notices', function() use ($e) {
        ?>
        <div class="notice notice-error">
            <p>An error occurred: <?= esc_html($e->getMessage()) ?></p>
        </div>
        <?php
    });
}
```

### 5. Documentation

Document your code thoroughly:
```php
/**
 * Process user data
 *
 * @param array $data User data to process
 * @param bool $validate Whether to validate data
 * @return array|WP_Error Processed data or error
 * @since 1.0.0
 */
public function processUserData(array $data, bool $validate = true)
{
    // Implementation
}
```

## Examples

### Example 1: Contact Form Plugin

```php
<?php
/**
 * Simple Contact Form Plugin
 */

class ContactFormPlugin extends Plugin
{
    public function init()
    {
        add_shortcode('contact_form', [$this, 'renderForm']);
        add_action('wp_ajax_submit_contact', [$this, 'handleSubmission']);
        add_action('wp_ajax_nopriv_submit_contact', [$this, 'handleSubmission']);
    }

    public function renderForm($atts)
    {
        $atts = shortcode_atts([
            'email' => get_option('admin_email')
        ], $atts);

        ob_start();
        ?>
        <form id="contact-form" class="contact-form">
            <?php wp_nonce_field('contact_form', 'contact_nonce'); ?>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Message:</label>
                <textarea name="message" required></textarea>
            </div>
            <button type="submit">Send Message</button>
        </form>
        <script>
        document.getElementById('contact-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'submit_contact');

            const response = await fetch('<?= admin_url('admin-ajax.php') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                alert('Message sent successfully!');
                e.target.reset();
            } else {
                alert('Error: ' + result.data);
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }

    public function handleSubmission()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['contact_nonce'], 'contact_form')) {
            wp_send_json_error('Security check failed');
        }

        // Sanitize input
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        // Validate
        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error('All fields are required');
        }

        // Send email
        $to = get_option('admin_email');
        $subject = 'New Contact Form Submission';
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = ['From: ' . $email];

        if (wp_mail($to, $subject, $body, $headers)) {
            // Save to database
            db()->table('contact_submissions')->insert([
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'created_at' => current_time('mysql')
            ]);

            wp_send_json_success('Message sent successfully');
        } else {
            wp_send_json_error('Failed to send message');
        }
    }
}
```

### Example 2: Custom Post Type Plugin

```php
<?php
/**
 * Portfolio Custom Post Type Plugin
 */

class PortfolioPlugin extends Plugin
{
    public function init()
    {
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomies']);
        add_filter('template_include', [$this, 'loadTemplates']);
    }

    public function registerPostType()
    {
        register_post_type('portfolio', [
            'labels' => [
                'name' => 'Portfolio',
                'singular_name' => 'Portfolio Item',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Portfolio Item',
                'edit_item' => 'Edit Portfolio Item',
                'view_item' => 'View Portfolio Item'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'portfolio'],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon' => 'dashicons-portfolio',
            'show_in_rest' => true
        ]);
    }

    public function registerTaxonomies()
    {
        register_taxonomy('portfolio_category', 'portfolio', [
            'labels' => [
                'name' => 'Categories',
                'singular_name' => 'Category'
            ],
            'hierarchical' => true,
            'rewrite' => ['slug' => 'portfolio-category']
        ]);

        register_taxonomy('portfolio_tag', 'portfolio', [
            'labels' => [
                'name' => 'Tags',
                'singular_name' => 'Tag'
            ],
            'hierarchical' => false,
            'rewrite' => ['slug' => 'portfolio-tag']
        ]);
    }

    public function loadTemplates($template)
    {
        if (is_singular('portfolio')) {
            $plugin_template = __DIR__ . '/templates/single-portfolio.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        if (is_post_type_archive('portfolio')) {
            $plugin_template = __DIR__ . '/templates/archive-portfolio.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        return $template;
    }
}
```

## Troubleshooting

### Common Issues

1. **Plugin not appearing**: Check file permissions and manifest.php syntax
2. **Hooks not firing**: Verify hook names and priorities
3. **Database errors**: Check table prefixes and SQL syntax
4. **JavaScript not loading**: Verify script handles and dependencies
5. **Translations not working**: Check text domain and .mo file location

### Debugging Tips

```php
// Enable debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Log to file
error_log('Debug: ' . print_r($variable, true));

// Use development tools
if (defined('WP_DEBUG') && WP_DEBUG) {
    // Development code only
}
```

## Resources

- [Infinity CMS Documentation](/docs)
- [Plugin API Reference](/docs/api)
- [Hook Reference](/docs/hooks)
- [Example Plugins](https://github.com/infinity-cms/example-plugins)
- [Community Forum](https://community.infinity-cms.com)