# Infinity CMS Security Architecture

## Theme Security Overhaul - Implementation Summary

This document describes the comprehensive security improvements implemented in Infinity CMS to protect against common vulnerabilities in theme templates.

---

## Security Vulnerabilities Fixed

### 1. **Directory Traversal Protection**
- **Previous Risk:** Templates could access files outside theme directory using `../` patterns
- **Fix:** Added path validation in `View::findTemplate()` and `View::isPathSafe()`
- **Impact:** Templates are now restricted to theme directory only

### 2. **Variable Overwriting Attack Prevention**
- **Previous Risk:** `extract($data)` without flags allowed malicious data to overwrite variables
- **Fix:** Changed to `extract($data, EXTR_SKIP)` to prevent overwriting
- **Impact:** Protected variables like `$theme` and `$path` cannot be overwritten

### 3. **Secure Theme Context**
- **Previous Risk:** Templates had unrestricted access to `db()`, `config()`, file system, etc.
- **Fix:** Created `ThemeContext` class with controlled, safe methods
- **Impact:** Templates now use `$theme->` API with automatic escaping and restricted access

### 4. **Configuration Access Control**
- **Previous Risk:** Templates could access database credentials via `config('database.password')`
- **Fix:** `ThemeContext::config()` blocks sensitive config keys
- **Impact:** Only safe config (app.name, theme.*) is accessible

### 5. **Automatic HTML Escaping**
- **Previous Risk:** Developers had to manually escape every output
- **Fix:** `$theme->get()` escapes by default; `$theme->raw()` for trusted content
- **Impact:** XSS attacks significantly reduced

---

## New Theme API (`$theme` Object)

All theme templates now receive a `$theme` object instead of raw extracted variables.

### Available Methods:

#### Data Access
```php
// Get escaped data (safe for HTML output)
<?= $theme->get('title') ?>
<?= $theme->title ?> // Magic getter, automatically escaped

// Get raw data (for trusted content only)
<?= $theme->raw('content') ?>

// Check if data exists
<?php if ($theme->has('title')): ?>

// Convert to array (for compatibility)
$data = $theme->toArray();
```

#### Helper Methods
```php
// URLs
<?= $theme->url('/admin/dashboard') ?>
<?= $theme->asset('css/style.css') ?> // Theme asset URL

// Escaping
<?= $theme->e($userInput) ?>
<?= $theme->escape($userInput) ?>

// Configuration (restricted)
<?= $theme->config('app.name') ?> // ✅ Allowed
<?= $theme->config('database.password') ?> // ❌ Blocked, returns null

// Authentication
<?php if ($theme->isLoggedIn()): ?>
$user = $theme->currentUser(); // Returns safe fields only

// Partials
<?php $theme->partial('header') ?>
<?php $theme->partial('sidebar', ['extra' => 'data']) ?>

// Request
<?php if ($theme->isHtmx()): ?>
<?php if ($theme->uriIs('/admin*')): ?>

// Forms
<?= $theme->csrfField() ?>

// Flash messages
<?= $theme->flash('success') ?>
```

---

## Migration Guide for Existing Templates

### Before (Old, Insecure)
```php
<title><?= $title ?? config('app.name') ?></title>
<a href="<?= url('/admin') ?>">Dashboard</a>
<?php if (is_logged_in()): ?>
<?php app('view')->partial('header', get_defined_vars()); ?>
<?= $post['content'] ?> // XSS vulnerable!
```

### After (New, Secure)
```php
<title><?= $theme->get('title', $theme->config('app.name')) ?></title>
<a href="<?= $theme->url('/admin') ?>">Dashboard</a>
<?php if ($theme->isLoggedIn()): ?>
<?php $theme->partial('header') ?>
<?= $theme->get('content') ?> // Auto-escaped!
<?= $theme->raw('content') ?> // If you trust the content
```

---

## What Templates CAN Do

✅ Display data passed by controllers
✅ Access safe configuration (app.name, theme.*)
✅ Generate URLs and asset paths
✅ Check authentication status
✅ Include partials
✅ Use CSRF protection
✅ Check request type (HTMX, etc.)
✅ Access flash messages

---

## What Templates CANNOT Do

❌ Direct database access (`db()` is blocked)
❌ Access sensitive config (database credentials, secrets)
❌ Access files outside theme directory
❌ Overwrite protected variables
❌ Execute arbitrary PHP code via service container
❌ Modify session data directly
❌ Access raw request parameters without validation

---

## Backward Compatibility

For gradual migration, the system still supports the old `extract()` style:

```php
// Old style still works (but discouraged)
<?= $title ?>

// New style (recommended)
<?= $theme->title ?>
```

**Important:** `extract()` now uses `EXTR_SKIP` flag, so you cannot overwrite `$theme` or other protected variables.

---

## Security Best Practices for Theme Developers

### 1. Always Use `$theme->get()` for User-Generated Content
```php
// ❌ Bad: Potential XSS
<?= $post['title'] ?>

// ✅ Good: Auto-escaped
<?= $theme->get('post')['title'] ?>
<?= $theme->post['title'] ?> // If post is in root data
```

### 2. Use `raw()` Only for Trusted Content
```php
// ❌ Bad: User-generated content, not escaped
<?= $theme->raw('comment') ?>

// ✅ Good: Admin-controlled content, trusted HTML
<?= $theme->raw('pageContent') ?> // From CMS, has HTML formatting
```

### 3. Don't Try to Bypass Security
```php
// ❌ Bad: Trying to access database
// This won't work anymore - db() is blocked in theme context
$posts = db()->table('posts')->get();

// ✅ Good: Request data from controller
// In controller: view('template', ['posts' => $posts])
// In template: <?php foreach ($theme->get('posts', []) as $post): ?>
```

### 4. Validate URLs in href Attributes
```php
// ❌ Bad: User input in href without validation
<a href="<?= $theme->raw('url') ?>">

// ✅ Good: Use url() helper for internal links
<a href="<?= $theme->url('/post/' . $theme->e($slug)) ?>">
```

---

## Implementation Files

| File | Purpose |
|------|---------|
| `app/Core/ThemeContext.php` | Secure theme API with restricted methods |
| `app/Core/View.php` | Updated rendering with path validation |
| `themes/infinity/layouts/base.php` | Example of new `$theme->` API usage |
| `themes/infinity/partials/header.php` | Example of secure partial |

---

## Testing Security

### Test Directory Traversal Protection
```php
// This should throw an exception:
view('../../config/env'); // ❌ Blocked

// This should work:
view('post'); // ✅ Allowed
```

### Test Configuration Access
```php
// In template:
<?= $theme->config('app.name') ?> // ✅ Works
<?= $theme->config('database.password') ?> // ❌ Returns null
```

### Test XSS Protection
```php
// Controller passes malicious data:
view('test', ['title' => '<script>alert("XSS")</script>']);

// In template:
<?= $theme->title ?> // ✅ Escaped: &lt;script&gt;alert("XSS")&lt;/script&gt;
<?= $theme->raw('title') ?> // ❌ NOT escaped: <script>alert("XSS")</script>
```

---

## Reporting Security Issues

If you discover a security vulnerability in Infinity CMS, please report it to the maintainers immediately. Do not disclose security issues publicly until a fix is available.

---

## Changelog

**Version 1.1.0 - Security Overhaul**
- Added `ThemeContext` class for secure template rendering
- Implemented directory traversal protection
- Added configuration access control
- Implemented automatic HTML escaping
- Fixed `extract()` variable overwriting vulnerability
- Added path validation for all template includes

---

## For More Information

- [Theme Development Guide](docs/theme-development.md)
- [Security Best Practices](docs/security-best-practices.md)
- [API Reference](docs/api-reference.md)
