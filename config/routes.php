<?php
/**
 * Route Definitions
 */

$router = app('router');

// ==========================================
// Frontend Routes
// ==========================================

// Home page
$router->get('/', 'HomeController@index');

// Posts
$router->get('/posts', 'PostController@index');
$router->get('/post/{slug}', 'PostController@show');

// Pages
$router->get('/page/{slug}', 'PageController@show');
// Special routes for common pages
$router->get('/about', function() {
    $controller = new \App\Http\Controllers\PageController();
    return $controller->show('about');
});
$router->get('/contact', 'PageController@contact');
$router->post('/contact', 'PageController@submitContact');

// API endpoints for HTMX
$router->get('/api/posts/latest', function() {
    $posts = db()->table('posts')
        ->where('status', 'published')
        ->orderBy('created_at', 'DESC')
        ->limit(5)
        ->get();

    $html = '<div class="posts-grid">';
    foreach ($posts as $post) {
        $html .= '<div class="post-card">';
        $html .= '<h3>' . e($post['title']) . '</h3>';
        $html .= '<p>' . e(substr($post['content'], 0, 150)) . '...</p>';
        $html .= '<a href="' . url('/post/' . $post['slug']) . '">Read more</a>';
        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;
});

// ==========================================
// Admin Routes (with admin middleware)
// ==========================================

$router->group(['prefix' => '/admin', 'middleware' => 'admin'], function($router) {

    // Admin root - redirect to dashboard
    $router->get('', function() {
        redirect(url('/admin/dashboard'));
    });

    // Dashboard
    $router->get('/dashboard', 'Admin/DashboardController@index');

    // Posts management
    $router->get('/posts', 'Admin/PostController@index');
    $router->get('/posts/create', 'Admin/PostController@create');
    $router->post('/posts', 'Admin/PostController@store');
    $router->get('/posts/{id}/edit', 'Admin/PostController@edit');
    $router->post('/posts/{id}', 'Admin/PostController@update');
    $router->delete('/posts/{id}', 'Admin/PostController@destroy');

    // HTMX endpoints for admin
    $router->get('/stats', 'Admin/DashboardController@stats');

    // Database Migrations
    $router->get('/migrations', 'Admin/MigrationController@index');
    $router->post('/migrations/run', 'Admin/MigrationController@run');
    $router->post('/migrations/rollback', 'Admin/MigrationController@rollback');
    $router->post('/migrations/reset', 'Admin/MigrationController@reset');
    $router->get('/migrations/status', 'Admin/MigrationController@status');
});

// ==========================================
// Setup Routes
// ==========================================

$router->get('/setup', 'SetupController@index');
$router->post('/setup/process', 'SetupController@process');

// ==========================================
// Auth Routes
// ==========================================

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
