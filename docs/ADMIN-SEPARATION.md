# Admin/Theme Separation - Implementation Guide

## Overview

This document describes the architectural separation between **admin/dashboard infrastructure** (core) and **public theme** (themes).

**Key Principle:** Themes are for public-facing pages only. Admin dashboard is core infrastructure.

---

## Architecture Changes

### Before (Mixed)
```
themes/infinity/
├── pages/
│   ├── home.php ..................... PUBLIC (correct)
│   ├── post.php ..................... PUBLIC (correct)
│   ├── dashboard/index.php .......... ADMIN (wrong location!)
│   └── migrations/index.php ......... ADMIN (wrong location!)
```

### After (Separated)
```
app/Views/admin/              ← NEW: Admin views (core infrastructure)
├── layouts/
│   └── admin.php ................... Admin layout
├── partials/
│   ├── header.php .................. Admin header
│   └── footer.php .................. Admin footer
├── dashboard/
│   └── index.php ................... Dashboard view
├── migrations/
│   └── index.php ................... Migrations view
├── posts/
│   ├── index.php ................... Post list (future)
│   ├── create.php .................. Create post (future)
│   └── edit.php .................... Edit post (future)
└── assets/
    ├── css/
    │   ├── admin.css ............... Admin global styles
    │   ├── dashboard.css ........... Dashboard-specific styles
    │   └── migrations.css .......... Migrations-specific styles
    └── js/
        └── admin.js ................ Admin global scripts

themes/infinity/              ← Themes only handle public pages
├── pages/
│   ├── home.php .................... PUBLIC
│   ├── post.php .................... PUBLIC
│   ├── about.php ................... PUBLIC
│   └── contact.php ................. PUBLIC
└── (no admin pages)
```

---

## Implementation Status

### ✅ Completed

1. **AdminView Class** - `app/Core/AdminView.php`
   - Dedicated view renderer for admin pages
   - Similar to `View` but for admin-only templates
   - Includes `AdminContext` helper object

2. **Service Container Registration** - `bootstrap/app.php:66`
   - `AdminView` registered as `admin_view` service

3. **Helper Function** - `functions.php:44`
   - `admin_view($template, $data)` - Renders admin templates

4. **Admin Layout** - `app/Views/admin/layouts/admin.php`
   - Dedicated admin layout (not using theme layout)
   - Includes migration alert banner
   - Loads admin-specific assets

5. **Directory Structure** - `app/Views/admin/`
   - Created directory structure for admin views

---

## Migration Steps (To Complete)

### Step 1: Create Admin Header/Footer Partials

**Create:** `app/Views/admin/partials/header.php`
```php
<header class="admin-header">
    <div class="admin-container">
        <div class="admin-logo">
            <a href="<?= $admin->url('/admin/dashboard') ?>">
                <h1><?= config('app.name') ?> Admin</h1>
            </a>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="<?= $admin->url('/admin/dashboard') ?>">Dashboard</a></li>
                <li><a href="<?= $admin->url('/admin/posts') ?>">Posts</a></li>
                <li><a href="<?= $admin->url('/admin/migrations') ?>">Migrations</a></li>
                <li><a href="<?= $admin->url('/') ?>" target="_blank">View Site</a></li>
                <li><a href="<?= $admin->url('/logout') ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>
```

**Create:** `app/Views/admin/partials/footer.php`
```php
<footer class="admin-footer">
    <div class="admin-container">
        <p>&copy; <?= date('Y') ?> <?= config('app.name') ?> - Admin Dashboard</p>
    </div>
</footer>
```

---

### Step 2: Move Dashboard Template

**From:** `themes/infinity/pages/dashboard/index.php`
**To:** `app/Views/admin/dashboard/index.php`

**Changes needed:**
- Replace `$theme->` with `$admin->`
- Remove embedded `<style>` block (lines 59-191)
- Extract styles to `app/Views/admin/assets/css/dashboard.css`

---

### Step 3: Move Migrations Template

**From:** `themes/infinity/pages/migrations/index.php`
**To:** `app/Views/admin/migrations/index.php`

**Changes needed:**
- Replace `app('view')->partial()` with `$admin->partial()`
- Replace `url()` with `$admin->url()`
- Replace `e()` with `$admin->e()`
- Remove embedded `<style>` block (lines 103-228)
- Extract styles to `app/Views/admin/assets/css/migrations.css`

---

### Step 4: Extract Admin CSS

**Create:** `app/Views/admin/assets/css/admin.css` (Global admin styles)
```css
/* Base admin layout */
.admin-body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #f5f5f5;
}

.admin-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Migration alert banner */
.migration-alert {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-left: 4px solid #ffc107;
    padding: 1rem 1.5rem;
    margin: 1rem 0;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert-content svg {
    flex-shrink: 0;
    color: #856404;
}

.alert-message strong {
    color: #856404;
    display: block;
    margin-bottom: 0.25rem;
}

.alert-message span {
    color: #856404;
}

.alert-button {
    background: #ffc107;
    color: #000;
    padding: 0.5rem 1.25rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
    transition: background 0.2s;
}

.alert-button:hover {
    background: #e0a800;
}

/* Admin header */
.admin-header {
    background: #2c3e50;
    color: white;
    padding: 1rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-logo h1 {
    margin: 0;
    font-size: 1.5rem;
}

.admin-logo a {
    color: white;
    text-decoration: none;
}

.admin-nav ul {
    list-style: none;
    display: flex;
    gap: 1.5rem;
    margin: 0;
    padding: 0;
}

.admin-nav a {
    color: white;
    text-decoration: none;
    transition: opacity 0.2s;
}

.admin-nav a:hover {
    opacity: 0.8;
}

/* Admin footer */
.admin-footer {
    background: #2c3e50;
    color: white;
    text-align: center;
    padding: 1rem 0;
    margin-top: 3rem;
}
```

**Create:** `app/Views/admin/assets/css/dashboard.css`
- Copy styles from `themes/infinity/pages/dashboard/index.php` (lines 59-191)

**Create:** `app/Views/admin/assets/css/migrations.css`
- Copy styles from `themes/infinity/pages/migrations/index.php` (lines 103-228)

**Create:** `app/Views/admin/assets/js/admin.js`
```javascript
// Admin-specific JavaScript
console.log('Admin dashboard loaded');

// Add any admin-specific JS functionality here
```

---

### Step 5: Update Admin Controllers

**File:** `app/Http/Controllers/Admin/DashboardController.php`

**Before:**
```php
public function index() {
    // ...
    return view('dashboard.index', [
        'title' => 'Dashboard',
        'totalPosts' => $totalPosts,
        // ...
    ]);
}
```

**After:**
```php
public function index() {
    // ...
    return admin_view('dashboard.index', [
        'title' => 'Dashboard',
        'totalPosts' => $totalPosts,
        // ...
    ]);
}
```

**File:** `app/Http/Controllers/Admin/MigrationController.php`

**Before:**
```php
public function index() {
    $migration = new Migration();
    $status = $migration->getStatus();

    return view('migrations.index', [
        'title' => 'Database Migrations',
        'migrations' => $status,
    ]);
}
```

**After:**
```php
public function index() {
    $migration = new Migration();
    $status = $migration->getStatus();

    return admin_view('migrations.index', [
        'title' => 'Database Migrations',
        'migrations' => $status,
    ]);
}
```

---

### Step 6: Update AdminMiddleware

**File:** `app/Http/Middlewares/AdminMiddleware.php`

**Change line 69:**
```php
// Before:
app('view')->share([
    'hasPendingMigrations' => $hasPending,
    'pendingMigrationsCount' => $count,
]);

// After:
app('admin_view')->share([
    'hasPendingMigrations' => $hasPending,
    'pendingMigrationsCount' => $count,
]);
```

---

### Step 7: Clean Up Theme Files

**Delete these files** (after moving content to `app/Views/admin/`):
- `themes/infinity/pages/dashboard/index.php`
- `themes/infinity/pages/migrations/index.php`
- `themes/infinity/pages/dashboard/` (empty directory)
- `themes/infinity/pages/migrations/` (empty directory)

---

## Benefits of This Separation

### 1. **Security**
- Admin code is not exposed in themes
- Theme developers cannot accidentally break admin functionality
- Admin templates can have different security requirements

### 2. **Maintainability**
- Clear separation of concerns
- Admin updates don't affect themes
- Theme updates don't affect admin

### 3. **Flexibility**
- Can change admin UI independently of theme
- Can have different styling systems (admin vs. public)
- Easier to add new admin features

### 4. **Professional Architecture**
- Matches industry standards (WordPress, Laravel, etc.)
- Core infrastructure vs. customizable presentation
- Better for multi-tenant or white-label scenarios

---

## API Differences

### Theme Templates (`$theme`)
```php
// For PUBLIC pages only
<?= $theme->get('title') ?>          // Auto-escaped
<?= $theme->url('/blog') ?>
<?= $theme->asset('css/style.css') ?> // Theme asset
<?= $theme->config('app.name') ?>     // Restricted config access
```

### Admin Templates (`$admin`)
```php
// For ADMIN pages only
<?= $admin->get('title') ?>           // Manual escaping
<?= $admin->e($userInput) ?>          // Escape helper
<?= $admin->url('/admin/posts') ?>
<?= $admin->asset('css/admin.css') ?> // Admin asset
<?php $admin->partial('header') ?>    // Admin partial
```

---

## Testing After Migration

### 1. Test Dashboard
```
Visit: http://localhost/admin/dashboard
Expected: Dashboard renders with new admin layout
```

### 2. Test Migrations
```
Visit: http://localhost/admin/migrations
Expected: Migrations page renders with admin layout
```

### 3. Test Migration Alert
```
1. Create dummy migration: touch database/migrations/999_test.php
2. Visit any admin page
Expected: Yellow banner appears at top
```

### 4. Test Public Theme
```
Visit: http://localhost/
Expected: Public theme still works, no admin pages visible
```

---

## File Checklist

### Created Files
- [ ] `app/Core/AdminView.php`
- [ ] `app/Views/admin/layouts/admin.php`
- [ ] `app/Views/admin/partials/header.php`
- [ ] `app/Views/admin/partials/footer.php`
- [ ] `app/Views/admin/dashboard/index.php`
- [ ] `app/Views/admin/migrations/index.php`
- [ ] `app/Views/admin/assets/css/admin.css`
- [ ] `app/Views/admin/assets/css/dashboard.css`
- [ ] `app/Views/admin/assets/css/migrations.css`
- [ ] `app/Views/admin/assets/js/admin.js`

### Modified Files
- [x] `bootstrap/app.php` - Register AdminView
- [x] `functions.php` - Add `admin_view()` helper
- [ ] `app/Http/Controllers/Admin/DashboardController.php` - Use `admin_view()`
- [ ] `app/Http/Controllers/Admin/MigrationController.php` - Use `admin_view()`
- [ ] `app/Http/Middlewares/AdminMiddleware.php` - Share to `admin_view`

### Deleted Files (After Migration)
- [ ] `themes/infinity/pages/dashboard/index.php`
- [ ] `themes/infinity/pages/migrations/index.php`

---

## Summary

This separation ensures:
- ✅ Themes are **ONLY** for public pages
- ✅ Admin dashboard is **core infrastructure**
- ✅ Clear architectural boundaries
- ✅ Better security and maintainability
- ✅ Professional CMS architecture

Next step: Complete the file migrations listed above!
