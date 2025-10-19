# Theme Development Guide

## Introduction

Infinity CMS themes control the visual presentation and layout of your website. This guide will walk you through creating custom themes that leverage modern web technologies like HTMX and Alpine.js while maintaining clean, maintainable code.

## Table of Contents
- [Theme Structure](#theme-structure)
- [Creating a Theme](#creating-a-theme)
- [Template Hierarchy](#template-hierarchy)
- [Theme Configuration](#theme-configuration)
- [Template Tags](#template-tags)
- [Working with HTMX](#working-with-htmx)
- [Working with Alpine.js](#working-with-alpinejs)
- [Assets Management](#assets-management)
- [Customizer API](#customizer-api)
- [Best Practices](#best-practices)

## Theme Structure

A complete theme follows this structure:

```
themes/
└── your-theme/
    ├── theme.json           # Theme configuration (required)
    ├── screenshot.png       # Theme screenshot (1200x900px)
    ├── functions.php        # Theme functions and setup
    ├── style.css           # Main stylesheet
    ├── layouts/            # Layout templates
    │   ├── base.php       # Base HTML structure
    │   ├── single.php     # Single post layout
    │   └── archive.php    # Archive layout
    ├── partials/           # Reusable components
    │   ├── header.php     # Site header
    │   ├── footer.php     # Site footer
    │   ├── sidebar.php    # Sidebar
    │   └── nav.php        # Navigation menu
    ├── pages/              # Page templates
    │   ├── home.php       # Homepage template
    │   ├── about.php      # About page template
    │   └── contact.php    # Contact page template
    ├── components/         # HTMX/Alpine components
    │   ├── search.php     # Search component
    │   ├── comments.php   # Comments component
    │   └── modal.php      # Modal component
    ├── assets/            # Theme assets
    │   ├── css/          # Stylesheets
    │   ├── js/           # JavaScript files
    │   ├── images/       # Theme images
    │   └── fonts/        # Custom fonts
    └── languages/         # Translation files
```

## Creating a Theme

### Step 1: Create Theme Directory
```bash
mkdir themes/my-awesome-theme
cd themes/my-awesome-theme
```

### Step 2: Create theme.json
```json
{
    "name": "My Awesome Theme",
    "version": "1.0.0",
    "author": "Your Name",
    "author_uri": "https://yourwebsite.com",
    "description": "A modern, responsive theme for Infinity CMS",
    "license": "GPL-2.0",
    "tags": ["responsive", "modern", "htmx", "alpine", "blog"],
    "text_domain": "my-awesome-theme",
    "requires": "1.0.0",
    "tested_up_to": "1.5.0",
    "supports": {
        "custom-header": true,
        "custom-logo": true,
        "custom-menu": true,
        "post-thumbnails": true,
        "widgets": true,
        "editor-styles": true,
        "dark-mode": true
    },
    "menus": {
        "primary": "Primary Menu",
        "footer": "Footer Menu",
        "mobile": "Mobile Menu"
    },
    "sidebars": {
        "main": {
            "name": "Main Sidebar",
            "description": "Appears on posts and pages"
        },
        "footer": {
            "name": "Footer Widgets",
            "description": "Footer widget area"
        }
    },
    "customizer": {
        "colors": {
            "primary": "#007cba",
            "secondary": "#6c757d",
            "accent": "#28a745"
        },
        "fonts": {
            "body": "Inter, sans-serif",
            "heading": "Poppins, sans-serif"
        }
    }
}
```

### Step 3: Create Base Layout
Create `layouts/base.php`:
```php
<!DOCTYPE html>
<html lang="<?= site_language() ?>" class="<?= html_classes() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= page_title() ?></title>
    <meta name="description" content="<?= page_description() ?>">

    <!-- Theme Styles -->
    <link rel="stylesheet" href="<?= theme_url('style.css') ?>">
    <link rel="stylesheet" href="<?= theme_url('assets/css/main.css') ?>">

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <?php do_action('wp_head') ?>
</head>
<body class="<?= body_classes() ?>" hx-boost="true">
    <?php do_action('after_body_open') ?>

    <div id="app" class="site-wrapper">
        <?php $this->include('partials/header') ?>

        <main id="main" class="site-main">
            <?= $this->yield('content') ?>
        </main>

        <?php $this->include('partials/footer') ?>
    </div>

    <?php do_action('wp_footer') ?>
    <script src="<?= theme_url('assets/js/main.js') ?>"></script>
</body>
</html>
```

### Step 4: Create Header Partial
Create `partials/header.php`:
```php
<header class="site-header" x-data="{ mobileMenuOpen: false }">
    <div class="container">
        <div class="header-inner">
            <!-- Logo -->
            <div class="site-logo">
                <?php if (has_custom_logo()): ?>
                    <?= get_custom_logo() ?>
                <?php else: ?>
                    <a href="<?= home_url() ?>" class="site-title">
                        <?= site_name() ?>
                    </a>
                <?php endif ?>
            </div>

            <!-- Desktop Navigation -->
            <nav class="main-navigation desktop-only">
                <?= wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'nav-menu',
                    'fallback_cb' => false
                ]) ?>
            </nav>

            <!-- Search (HTMX powered) -->
            <div class="header-search">
                <form hx-get="/search"
                      hx-target="#search-results"
                      hx-trigger="keyup changed delay:500ms from:input">
                    <input type="search"
                           name="q"
                           placeholder="Search..."
                           autocomplete="off">
                </form>
                <div id="search-results"></div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle mobile-only"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen">
                <span class="hamburger"></span>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="mobile-navigation mobile-only"
             x-show="mobileMenuOpen"
             x-transition>
            <?= wp_nav_menu([
                'theme_location' => 'mobile',
                'container' => false,
                'menu_class' => 'mobile-menu'
            ]) ?>
        </nav>
    </div>
</header>
```

### Step 5: Create Homepage Template
Create `pages/home.php`:
```php
<?php $this->extend('layouts/base') ?>

<?php $this->section('content') ?>
    <!-- Hero Section -->
    <section class="hero" x-data="{ heroText: 'Welcome to <?= site_name() ?>' }">
        <div class="container">
            <h1 x-text="heroText" x-transition></h1>
            <p class="hero-subtitle"><?= site_description() ?></p>
            <a href="/about" class="btn btn-primary">Learn More</a>
        </div>
    </section>

    <!-- Recent Posts (HTMX) -->
    <section class="recent-posts">
        <div class="container">
            <h2>Recent Posts</h2>
            <div id="posts-grid"
                 hx-get="/api/posts/recent"
                 hx-trigger="load"
                 hx-indicator="#loading"
                 class="posts-grid">
                <div id="loading" class="htmx-indicator">
                    Loading posts...
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center">
                <button hx-get="/api/posts/recent?page=2"
                        hx-target="#posts-grid"
                        hx-swap="beforeend"
                        class="btn btn-outline">
                    Load More
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section with Alpine.js -->
    <section class="features" x-data="features()">
        <div class="container">
            <h2>Features</h2>
            <div class="features-grid">
                <template x-for="feature in features" :key="feature.id">
                    <div class="feature-card"
                         @click="selectFeature(feature)"
                         :class="{ 'active': selectedFeature?.id === feature.id }">
                        <div class="feature-icon" x-html="feature.icon"></div>
                        <h3 x-text="feature.title"></h3>
                        <p x-text="feature.description"></p>
                    </div>
                </template>
            </div>

            <!-- Feature Details -->
            <div x-show="selectedFeature" x-transition class="feature-details">
                <h3 x-text="selectedFeature?.title"></h3>
                <div x-html="selectedFeature?.content"></div>
            </div>
        </div>
    </section>
<?php $this->endSection() ?>

<script>
function features() {
    return {
        features: [
            {
                id: 1,
                icon: '<svg>...</svg>',
                title: 'Fast & Lightweight',
                description: 'Optimized for speed',
                content: '<p>Detailed content about speed...</p>'
            },
            // More features...
        ],
        selectedFeature: null,
        selectFeature(feature) {
            this.selectedFeature = this.selectedFeature?.id === feature.id ? null : feature;
        }
    }
}
</script>
```

## Template Hierarchy

Infinity CMS follows a template hierarchy for rendering content:

```
1. Custom Page Template (if selected)
2. page-{slug}.php
3. page-{id}.php
4. page.php
5. singular.php
6. index.php

For Posts:
1. single-{post-type}-{slug}.php
2. single-{post-type}.php
3. single.php
4. singular.php
5. index.php

For Archives:
1. category-{slug}.php
2. category-{id}.php
3. category.php
4. archive.php
5. index.php
```

## Theme Configuration

### functions.php
```php
<?php
/**
 * Theme Functions
 */

// Theme Setup
add_action('after_setup_theme', function() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height' => 100,
        'width' => 400,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery']);
    add_theme_support('title-tag');
    add_theme_support('responsive-embeds');

    // Register menus
    register_nav_menus([
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu',
        'mobile' => 'Mobile Menu'
    ]);

    // Register sidebars
    register_sidebar([
        'name' => 'Main Sidebar',
        'id' => 'main-sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ]);
});

// Enqueue Assets
add_action('wp_enqueue_scripts', function() {
    // Styles
    wp_enqueue_style('theme-style', get_stylesheet_uri(), [], '1.0.0');
    wp_enqueue_style('theme-main', theme_url('assets/css/main.css'), [], '1.0.0');

    // Scripts
    wp_enqueue_script('htmx', 'https://unpkg.com/htmx.org@1.9.10', [], '1.9.10', true);
    wp_enqueue_script('alpine', 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js', [], '3.0.0', true);
    wp_enqueue_script('theme-main', theme_url('assets/js/main.js'), ['htmx', 'alpine'], '1.0.0', true);

    // Localize script
    wp_localize_script('theme-main', 'themeData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'apiUrl' => home_url('/api'),
        'nonce' => wp_create_nonce('theme_nonce')
    ]);
});

// Custom Image Sizes
add_action('after_setup_theme', function() {
    add_image_size('hero', 1920, 1080, true);
    add_image_size('card', 400, 300, true);
    add_image_size('thumbnail-square', 300, 300, true);
});

// Excerpt Length
add_filter('excerpt_length', function() {
    return 20;
});

// Custom Post Types (if needed)
add_action('init', function() {
    // Register custom post types here
});
```

## Template Tags

Infinity CMS provides numerous template tags for theme development:

### Basic Tags
```php
// Site Information
site_name()              // Get site name
site_description()       // Get site description
site_url()              // Get site URL
home_url($path)         // Get home URL with optional path
theme_url($path)        // Get theme URL with optional path

// Page Information
page_title()            // Get page title
page_description()      // Get page meta description
the_title()            // Display post/page title
the_content()          // Display post/page content
the_excerpt()          // Display post excerpt
the_permalink()        // Get post permalink

// Author Information
the_author()           // Display author name
the_author_link()      // Display author link
get_avatar($size)      // Get author avatar

// Date and Time
the_date($format)       // Display post date
the_time($format)       // Display post time
get_the_date($format)   // Get post date
human_time_diff()       // Human-readable time difference
```

### Conditional Tags
```php
is_home()               // Is homepage
is_front_page()         // Is front page
is_single()             // Is single post
is_page()               // Is page
is_archive()            // Is archive page
is_category()           // Is category archive
is_tag()                // Is tag archive
is_search()             // Is search results
is_404()                // Is 404 page
is_user_logged_in()     // Is user logged in
has_post_thumbnail()    // Has featured image
```

### Loop Tags
```php
// The Loop
if (have_posts()) :
    while (have_posts()) : the_post();
        // Post content
        the_title();
        the_content();
    endwhile;

    // Pagination
    the_posts_pagination([
        'prev_text' => 'Previous',
        'next_text' => 'Next',
        'mid_size' => 2
    ]);
else :
    // No posts found
    echo '<p>No posts found.</p>';
endif;
```

### Navigation Tags
```php
// Menu
wp_nav_menu([
    'theme_location' => 'primary',
    'container' => 'nav',
    'container_class' => 'main-nav',
    'menu_class' => 'nav-menu',
    'fallback_cb' => false,
    'depth' => 2,
    'walker' => new Custom_Nav_Walker()
]);

// Breadcrumbs
the_breadcrumbs([
    'separator' => ' / ',
    'home_text' => 'Home'
]);

// Pagination
paginate_links([
    'total' => $wp_query->max_num_pages,
    'current' => $current_page,
    'format' => '?page=%#%',
    'prev_text' => '&laquo;',
    'next_text' => '&raquo;'
]);
```

## Working with HTMX

HTMX enables dynamic content loading without writing JavaScript:

### Dynamic Content Loading
```php
<!-- Load content on page load -->
<div hx-get="/api/posts/latest"
     hx-trigger="load"
     hx-swap="innerHTML">
    Loading...
</div>

<!-- Infinite Scroll -->
<div hx-get="/api/posts?page=2"
     hx-trigger="revealed"
     hx-swap="afterend"
     hx-indicator="#spinner">
    <!-- Content -->
</div>
<div id="spinner" class="htmx-indicator">Loading...</div>

<!-- Click to Load -->
<button hx-get="/api/posts/more"
        hx-target="#posts-container"
        hx-swap="beforeend">
    Load More Posts
</button>
```

### Forms with HTMX
```php
<!-- Comment Form -->
<form hx-post="/api/comments"
      hx-target="#comments-list"
      hx-swap="afterbegin"
      hx-on::after-request="this.reset()">
    <textarea name="comment" required></textarea>
    <button type="submit">Post Comment</button>
</form>

<!-- Live Search -->
<input type="search"
       name="q"
       hx-get="/api/search"
       hx-trigger="keyup changed delay:500ms"
       hx-target="#search-results"
       hx-indicator=".search-spinner"
       placeholder="Search...">
<div class="search-spinner htmx-indicator">Searching...</div>
<div id="search-results"></div>
```

### HTMX Attributes
```html
hx-get="/url"           <!-- GET request -->
hx-post="/url"          <!-- POST request -->
hx-put="/url"           <!-- PUT request -->
hx-delete="/url"        <!-- DELETE request -->
hx-trigger="click"      <!-- Trigger on event -->
hx-target="#element"    <!-- Target element for swap -->
hx-swap="innerHTML"     <!-- How to swap content -->
hx-indicator="#loading" <!-- Loading indicator -->
hx-confirm="Are you sure?" <!-- Confirmation dialog -->
hx-boost="true"         <!-- Boost regular links -->
```

## Working with Alpine.js

Alpine.js provides reactive components:

### Basic Components
```html
<!-- Toggle Component -->
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" x-transition>
        Content to toggle
    </div>
</div>

<!-- Dropdown Menu -->
<div x-data="{ dropdown: false }" @click.away="dropdown = false">
    <button @click="dropdown = !dropdown">Menu</button>
    <ul x-show="dropdown" x-transition>
        <li><a href="#">Item 1</a></li>
        <li><a href="#">Item 2</a></li>
    </ul>
</div>

<!-- Tabs Component -->
<div x-data="{ activeTab: 'tab1' }">
    <nav>
        <button @click="activeTab = 'tab1'"
                :class="{ 'active': activeTab === 'tab1' }">
            Tab 1
        </button>
        <button @click="activeTab = 'tab2'"
                :class="{ 'active': activeTab === 'tab2' }">
            Tab 2
        </button>
    </nav>
    <div x-show="activeTab === 'tab1'" x-transition>
        Tab 1 content
    </div>
    <div x-show="activeTab === 'tab2'" x-transition>
        Tab 2 content
    </div>
</div>
```

### Advanced Components
```html
<!-- Modal Component -->
<div x-data="{ modalOpen: false }">
    <button @click="modalOpen = true">Open Modal</button>

    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-overlay"
         @click="modalOpen = false">
        <div class="modal-content" @click.stop>
            <button @click="modalOpen = false" class="close">&times;</button>
            <h2>Modal Title</h2>
            <p>Modal content here...</p>
        </div>
    </div>
</div>

<!-- Image Gallery -->
<div x-data="imageGallery()">
    <div class="gallery-grid">
        <template x-for="(image, index) in images" :key="index">
            <img :src="image.thumb"
                 :alt="image.alt"
                 @click="openLightbox(index)"
                 class="gallery-thumb">
        </template>
    </div>

    <div x-show="lightboxOpen" class="lightbox" @click="lightboxOpen = false">
        <img :src="currentImage?.full" :alt="currentImage?.alt">
        <button @click.stop="previousImage()" class="prev">‹</button>
        <button @click.stop="nextImage()" class="next">›</button>
    </div>
</div>

<script>
function imageGallery() {
    return {
        images: [
            { thumb: '/thumb1.jpg', full: '/full1.jpg', alt: 'Image 1' },
            { thumb: '/thumb2.jpg', full: '/full2.jpg', alt: 'Image 2' },
        ],
        lightboxOpen: false,
        currentIndex: 0,
        get currentImage() {
            return this.images[this.currentIndex];
        },
        openLightbox(index) {
            this.currentIndex = index;
            this.lightboxOpen = true;
        },
        nextImage() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },
        previousImage() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        }
    }
}
</script>
```

## Assets Management

### CSS Organization
```scss
// assets/css/main.css
/* Variables */
:root {
    --primary-color: #007cba;
    --secondary-color: #6c757d;
    --font-body: 'Inter', sans-serif;
    --font-heading: 'Poppins', sans-serif;
    --spacing-unit: 1rem;
}

/* Base Styles */
@import 'base/reset';
@import 'base/typography';
@import 'base/forms';

/* Layout */
@import 'layout/header';
@import 'layout/footer';
@import 'layout/sidebar';
@import 'layout/grid';

/* Components */
@import 'components/buttons';
@import 'components/cards';
@import 'components/modals';
@import 'components/navigation';

/* Pages */
@import 'pages/home';
@import 'pages/blog';
@import 'pages/contact';

/* Utilities */
@import 'utilities/spacing';
@import 'utilities/text';
@import 'utilities/responsive';
```

### JavaScript Modules
```javascript
// assets/js/main.js
import { Navigation } from './modules/navigation.js';
import { Search } from './modules/search.js';
import { Comments } from './modules/comments.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize modules
    new Navigation();
    new Search();
    new Comments();

    // HTMX configuration
    document.body.addEventListener('htmx:configRequest', (event) => {
        event.detail.headers['X-CSRF-Token'] = themeData.nonce;
    });

    // Alpine.js global data
    Alpine.data('theme', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            document.documentElement.classList.toggle('dark', this.darkMode);
        }
    }));
});
```

### Image Optimization
```php
// Responsive Images
<?php if (has_post_thumbnail()): ?>
    <picture>
        <source media="(min-width: 1200px)"
                srcset="<?= get_the_post_thumbnail_url(null, 'large') ?>">
        <source media="(min-width: 768px)"
                srcset="<?= get_the_post_thumbnail_url(null, 'medium') ?>">
        <img src="<?= get_the_post_thumbnail_url(null, 'thumbnail') ?>"
             alt="<?= get_the_title() ?>"
             loading="lazy">
    </picture>
<?php endif ?>

// Lazy Loading with HTMX
<img src="placeholder.jpg"
     hx-get="/api/image/full/<?= $image_id ?>"
     hx-trigger="intersect once"
     hx-swap="outerHTML">
```

## Customizer API

Allow users to customize theme options:

```php
// functions.php
add_action('customize_register', function($wp_customize) {
    // Add Section
    $wp_customize->add_section('theme_colors', [
        'title' => 'Theme Colors',
        'priority' => 30,
    ]);

    // Primary Color
    $wp_customize->add_setting('primary_color', [
        'default' => '#007cba',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control(
        $wp_customize,
        'primary_color',
        [
            'label' => 'Primary Color',
            'section' => 'theme_colors',
            'settings' => 'primary_color',
        ]
    ));

    // Typography Section
    $wp_customize->add_section('typography', [
        'title' => 'Typography',
        'priority' => 40,
    ]);

    // Font Family
    $wp_customize->add_setting('body_font', [
        'default' => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('body_font', [
        'label' => 'Body Font',
        'section' => 'typography',
        'type' => 'select',
        'choices' => [
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
        ],
    ]);
});

// Output Custom CSS
add_action('wp_head', function() {
    ?>
    <style>
        :root {
            --primary-color: <?= get_theme_mod('primary_color', '#007cba') ?>;
            --body-font: '<?= get_theme_mod('body_font', 'Inter') ?>', sans-serif;
        }
    </style>
    <?php
});
```

## Accessibility

Ensure your theme is accessible:

```php
<!-- Skip Links -->
<a class="skip-link screen-reader-text" href="#main">
    Skip to main content
</a>

<!-- ARIA Labels -->
<nav role="navigation" aria-label="Main navigation">
    <!-- Navigation -->
</nav>

<main id="main" role="main">
    <!-- Main content -->
</main>

<!-- Keyboard Navigation -->
<div x-data="{ index: 0 }"
     @keydown.arrow-down.prevent="index = Math.min(index + 1, items.length - 1)"
     @keydown.arrow-up.prevent="index = Math.max(index - 1, 0)">
    <template x-for="(item, i) in items" :key="i">
        <div :class="{ 'focused': index === i }"
             :tabindex="index === i ? 0 : -1">
            <!-- Item content -->
        </div>
    </template>
</div>

<!-- Screen Reader Text -->
<span class="screen-reader-text">Loading content</span>
```

## Performance Optimization

### Critical CSS
```php
// Inline critical CSS
add_action('wp_head', function() {
    ?>
    <style>
        /* Critical CSS here */
        <?= file_get_contents(get_theme_file_path('assets/css/critical.css')) ?>
    </style>
    <?php
});

// Load non-critical CSS asynchronously
add_action('wp_head', function() {
    ?>
    <link rel="preload"
          href="<?= theme_url('assets/css/main.css') ?>"
          as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <?php
});
```

### Resource Hints
```php
// DNS Prefetch
add_action('wp_head', function() {
    ?>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
});
```

### Image Optimization
```php
// WebP Support
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    // Generate WebP versions
    return $metadata;
}, 10, 2);

// Responsive Images
add_filter('wp_calculate_image_sizes', function($sizes, $size) {
    return '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
}, 10, 2);
```

## Best Practices

### 1. Code Organization
- Use clear, semantic naming
- Follow PSR standards for PHP
- Organize files logically
- Comment complex logic

### 2. Performance
- Minimize HTTP requests
- Optimize images
- Use lazy loading
- Enable browser caching
- Minify CSS/JS in production

### 3. Security
- Escape all output
- Validate and sanitize input
- Use nonces for forms
- Keep dependencies updated

### 4. Responsive Design
- Mobile-first approach
- Use flexible grids
- Responsive images
- Touch-friendly interfaces

### 5. SEO
- Semantic HTML5
- Proper heading hierarchy
- Meta descriptions
- Schema markup
- XML sitemap support

## Debugging

### Debug Mode
```php
// Enable debug mode in config.php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);

// Theme debug helper
function theme_debug($data, $label = 'Debug') {
    if (WP_DEBUG) {
        echo '<pre class="debug">';
        echo '<strong>' . $label . ':</strong><br>';
        print_r($data);
        echo '</pre>';
    }
}
```

### Browser DevTools
```javascript
// Console logging for development
if (themeData.debug) {
    console.log('Theme initialized');
    console.log('HTMX version:', htmx.version);
    console.log('Alpine version:', Alpine.version);
}
```

## Testing

### Cross-browser Testing
Test your theme in:
- Chrome/Edge
- Firefox
- Safari
- Mobile browsers

### Performance Testing
- Google PageSpeed Insights
- GTmetrix
- WebPageTest
- Lighthouse

### Accessibility Testing
- WAVE (WebAIM)
- axe DevTools
- Keyboard navigation
- Screen reader testing

## Distribution

### Preparing for Release
1. Remove development files
2. Minify assets
3. Optimize images
4. Update version numbers
5. Create screenshot.png (1200x900px)
6. Write comprehensive README
7. Test on fresh installation

### Theme Packaging
```bash
# Create distribution package
zip -r my-theme.zip my-theme/ \
    -x "*.DS_Store" \
    -x "*node_modules/*" \
    -x "*.git/*" \
    -x "*.scss" \
    -x "*.map"
```

## Resources

- [Infinity CMS Documentation](/docs)
- [Theme Development Reference](/docs/themes)
- [Template Tag Reference](/docs/template-tags)
- [HTMX Documentation](https://htmx.org)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Theme Examples](https://github.com/infinity-cms/theme-examples)
- [Community Forum](https://community.infinity-cms.com)