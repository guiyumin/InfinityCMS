# Development Guide

## Table of Contents
- [Getting Started](#getting-started)
- [Development Environment](#development-environment)
- [Architecture Overview](#architecture-overview)
- [Coding Standards](#coding-standards)
- [Database](#database)
- [Testing](#testing)
- [Debugging](#debugging)
- [Contributing](#contributing)

## Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite or Nginx
- Git
- A text editor or IDE (VSCode, PhpStorm recommended)

### Local Development Setup

1. **Clone the repository**
```bash
git clone <repository-url> infinity-cms
cd infinity-cms
```

2. **Configure your environment**
```bash
cp config.php.example config.php
```

Edit `config.php` with your local database credentials:
```php
return [
    'database' => [
        'host' => 'localhost',
        'name' => 'infinity_cms_dev',
        'user' => 'root',
        'pass' => 'your_password',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'url' => 'http://localhost:8000',
        'env' => 'development',
        'debug' => true,
    ],
];
```

3. **Set up the database**
```bash
mysql -u root -p
CREATE DATABASE infinity_cms_dev;
exit;
```

4. **Run the built-in PHP server**
```bash
php -S localhost:8000 -t public
```

5. **Access the setup wizard**
Navigate to `http://localhost:8000` and follow the setup instructions.

## Development Environment

### Using Docker (Optional)

Create a `docker-compose.yml`:
```yaml
version: '3.8'

services:
  web:
    image: php:7.4-apache
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html/public
    depends_on:
      - db

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: infinity_cms
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

Run with:
```bash
docker-compose up -d
```

### IDE Configuration

#### Visual Studio Code
Recommended extensions:
- PHP Intelephense
- PHP Debug
- MySQL (by Weijan Chen)
- GitLens
- EditorConfig

Create `.vscode/settings.json`:
```json
{
  "php.validate.executablePath": "/usr/bin/php",
  "files.associations": {
    "*.php": "php"
  },
  "editor.formatOnSave": true,
  "editor.tabSize": 4
}
```

#### PhpStorm
1. Configure PHP interpreter
2. Set up database connection
3. Enable PSR-4 namespace resolution
4. Configure code style to PSR-12

## Architecture Overview

### Directory Structure
```
infinity-cms/
├── app/                    # Application code
│   ├── Core/              # Core framework classes
│   │   ├── Application.php    # Main application class
│   │   ├── Container.php      # Dependency injection container
│   │   ├── Database.php       # Database connection and query builder
│   │   ├── Hook.php           # Hook system for plugins
│   │   ├── Router.php         # Routing engine
│   │   ├── Session.php        # Session management
│   │   └── View.php           # View rendering
│   ├── Http/              # HTTP layer
│   │   ├── Controllers/   # Request handlers
│   │   ├── Middlewares/   # Request/response middleware
│   │   └── Kernel.php     # HTTP kernel
│   └── Models/            # Data models
├── bootstrap/             # Bootstrap files
├── config/               # Configuration files
├── database/             # Database migrations and seeds
├── plugins/              # Plugin directory
├── public/               # Public web root
├── storage/              # File storage
├── themes/               # Theme files
└── vendor/               # Third-party packages (if any)
```

### MVC Pattern

#### Models
Models represent data and business logic:
```php
namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'content', 'status', 'author_id'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_categories');
    }
}
```

#### Controllers
Controllers handle HTTP requests:
```php
namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return $this->view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return $this->view('posts.show', compact('post'));
    }
}
```

#### Views
Views present data to users. Located in `themes/{theme_name}/`:
```php
<!-- themes/infinity/posts/index.php -->
<?php $this->extend('layouts/base') ?>

<?php $this->section('content') ?>
    <div class="posts">
        <?php foreach ($posts as $post): ?>
            <article class="post">
                <h2><?= e($post->title) ?></h2>
                <div class="content">
                    <?= $post->excerpt ?>
                </div>
                <a href="/posts/<?= $post->slug ?>">Read more</a>
            </article>
        <?php endforeach ?>
    </div>
<?php $this->endSection() ?>
```

### Routing

Define routes in `config/routes.php`:
```php
use App\Core\Route;

// Basic routes
Route::get('/', 'HomeController@index');
Route::get('/posts', 'PostController@index');
Route::get('/posts/{id}', 'PostController@show');

// Resource routes
Route::resource('admin/posts', 'Admin\PostController');

// API routes
Route::group(['prefix' => 'api'], function() {
    Route::get('/posts', 'Api\PostController@index');
    Route::post('/posts', 'Api\PostController@store');
});

// Route with middleware
Route::get('/admin', 'AdminController@dashboard')
      ->middleware(['auth', 'admin']);
```

### Middleware

Create middleware in `app/Http/Middlewares/`:
```php
namespace App\Http\Middlewares;

use App\Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        if (!is_logged_in()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
```

Register in `app/Http/Kernel.php`:
```php
protected $middlewares = [
    'auth' => AuthMiddleware::class,
    'admin' => AdminMiddleware::class,
    'throttle' => ThrottleMiddleware::class,
];
```

## Coding Standards

### PSR Standards
We follow PSR standards:
- PSR-1: Basic Coding Standard
- PSR-4: Autoloading Standard
- PSR-12: Extended Coding Style

### Naming Conventions
- **Classes**: PascalCase (e.g., `PostController`)
- **Methods**: camelCase (e.g., `getUserById()`)
- **Variables**: camelCase (e.g., `$userName`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_UPLOAD_SIZE`)
- **Database Tables**: snake_case plural (e.g., `posts`, `user_roles`)
- **Database Columns**: snake_case (e.g., `created_at`)

### Code Style Examples

#### PHP Files
```php
<?php

namespace App\Services;

use App\Models\Post;
use App\Contracts\ContentServiceInterface;

class ContentService implements ContentServiceInterface
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get published posts with caching
     *
     * @param int $limit
     * @return array
     */
    public function getPublishedPosts(int $limit = 10): array
    {
        $cacheKey = "posts.published.{$limit}";

        return $this->cacheService->remember($cacheKey, 3600, function() use ($limit) {
            return Post::where('status', 'published')
                       ->orderBy('created_at', 'desc')
                       ->limit($limit)
                       ->get();
        });
    }
}
```

#### JavaScript
```javascript
// Use modern ES6+ syntax
class PostManager {
    constructor(apiUrl) {
        this.apiUrl = apiUrl;
        this.posts = [];
    }

    async fetchPosts() {
        try {
            const response = await fetch(`${this.apiUrl}/posts`);
            this.posts = await response.json();
            return this.posts;
        } catch (error) {
            console.error('Failed to fetch posts:', error);
            throw error;
        }
    }

    renderPost(post) {
        return `
            <article class="post" data-id="${post.id}">
                <h2>${this.escapeHtml(post.title)}</h2>
                <div class="content">${post.content}</div>
            </article>
        `;
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}
```

## Database

### Query Builder

The query builder provides a fluent interface:

```php
// Select queries
$posts = db()->table('posts')
             ->select('id', 'title', 'created_at')
             ->where('status', 'published')
             ->where('created_at', '>', '2024-01-01')
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();

// Insert
$id = db()->table('posts')->insert([
    'title' => 'New Post',
    'content' => 'Content here',
    'status' => 'draft'
]);

// Update
db()->table('posts')
     ->where('id', $id)
     ->update(['status' => 'published']);

// Delete
db()->table('posts')
     ->where('id', $id)
     ->delete();

// Joins
$results = db()->table('posts')
               ->join('users', 'posts.author_id', '=', 'users.id')
               ->join('categories', 'posts.category_id', '=', 'categories.id')
               ->select('posts.*', 'users.name as author', 'categories.name as category')
               ->get();

// Raw queries (use sparingly)
$results = db()->query("SELECT * FROM posts WHERE MATCH(title, content) AGAINST(? IN BOOLEAN MODE)", [$searchTerm]);
```

### Migrations

Create migrations in `database/migrations/`:

```php
// database/migrations/007_create_comments_table.php
return [
    'up' => function($db) {
        $db->execute("
            CREATE TABLE comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT,
                content TEXT NOT NULL,
                status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_post_status (post_id, status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },

    'down' => function($db) {
        $db->execute("DROP TABLE IF EXISTS comments");
    }
];
```

Run migrations:
```bash
# Via web interface
http://localhost:8000/admin/migrations

# Or create a CLI command
php artisan migrate
```

### Database Optimization

1. **Indexing Strategy**
```sql
-- Add indexes for frequently queried columns
ALTER TABLE posts ADD INDEX idx_status_created (status, created_at);
ALTER TABLE posts ADD FULLTEXT(title, content);
```

2. **Query Optimization**
```php
// Bad - N+1 problem
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name; // Executes query each iteration
}

// Good - Eager loading
$posts = Post::with('author')->get();
foreach ($posts as $post) {
    echo $post->author->name; // No additional queries
}
```

## Testing

### Unit Testing

Create tests in `tests/Unit/`:
```php
// tests/Unit/PostTest.php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Post;

class PostTest extends TestCase
{
    public function testPostCreation()
    {
        $post = new Post([
            'title' => 'Test Post',
            'content' => 'Test content',
            'status' => 'draft'
        ]);

        $this->assertEquals('Test Post', $post->title);
        $this->assertEquals('test-post', $post->slug);
    }

    public function testPostValidation()
    {
        $post = new Post();
        $this->assertFalse($post->validate());

        $post->title = 'Valid Title';
        $post->content = 'Valid content';
        $this->assertTrue($post->validate());
    }
}
```

### Integration Testing

Create tests in `tests/Feature/`:
```php
// tests/Feature/ApiTest.php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testGetPosts()
    {
        $response = $this->get('/api/posts');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testCreatePost()
    {
        $response = $this->post('/api/posts', [
            'title' => 'New Post',
            'content' => 'Content'
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}
```

### Running Tests
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite Unit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

## Debugging

### Debug Mode

Enable in `config.php`:
```php
'app' => [
    'debug' => true,
    'log_level' => 'debug'
]
```

### Logging

Use the logger throughout your application:
```php
// Log messages
logger()->info('User logged in', ['user_id' => $userId]);
logger()->warning('Failed login attempt', ['ip' => $_SERVER['REMOTE_ADDR']]);
logger()->error('Database connection failed', ['error' => $e->getMessage()]);

// Debug helper
dd($variable); // Dump and die
dump($variable); // Dump without dying
```

### Debug Toolbar

When debug mode is enabled, a toolbar appears with:
- Execution time
- Memory usage
- Database queries
- Logged messages
- Session data
- Route information

### Xdebug Setup

1. Install Xdebug:
```bash
pecl install xdebug
```

2. Configure in `php.ini`:
```ini
[xdebug]
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_port=9003
xdebug.client_host=localhost
```

3. Configure your IDE to listen on port 9003

## Performance Optimization

### Caching

```php
// Cache HTML output
$cache = app('cache');
$key = 'posts.index.' . md5(serialize($_GET));

if ($cache->has($key)) {
    return $cache->get($key);
}

$html = $this->render('posts.index', $data);
$cache->put($key, $html, 3600); // Cache for 1 hour
return $html;
```

### Database Query Optimization

```php
// Use select to limit columns
Post::select('id', 'title', 'slug')->get();

// Use chunk for large datasets
Post::chunk(100, function($posts) {
    foreach ($posts as $post) {
        // Process post
    }
});

// Use database transactions
db()->beginTransaction();
try {
    // Multiple database operations
    db()->commit();
} catch (\Exception $e) {
    db()->rollback();
    throw $e;
}
```

### Asset Optimization

```html
<!-- Minify and combine CSS/JS -->
<link rel="stylesheet" href="/assets/dist/app.min.css">
<script src="/assets/dist/app.min.js" defer></script>

<!-- Lazy load images -->
<img src="placeholder.jpg" data-src="actual-image.jpg" loading="lazy">

<!-- Use CDN for common libraries -->
<script src="https://cdn.jsdelivr.net/npm/htmx.org@1.9.10"></script>
```

## Security Best Practices

### Input Validation
```php
// Always validate user input
$validator = new Validator($_POST, [
    'email' => 'required|email',
    'password' => 'required|min:8',
    'age' => 'integer|min:18|max:120'
]);

if (!$validator->passes()) {
    return back()->withErrors($validator->errors());
}
```

### SQL Injection Prevention
```php
// Always use parameterized queries
$posts = db()->query(
    "SELECT * FROM posts WHERE status = ? AND author_id = ?",
    [$status, $authorId]
);

// Never do this:
$posts = db()->query("SELECT * FROM posts WHERE id = " . $_GET['id']); // DANGEROUS!
```

### XSS Prevention
```php
// Always escape output
echo e($userInput); // Escapes HTML entities

// For raw HTML (be careful!)
echo clean($htmlContent); // Uses HTML Purifier
```

### CSRF Protection
```php
// In forms
<form method="POST">
    <?= csrf_field() ?>
    <!-- form fields -->
</form>

// Verify in controller
if (!csrf_verify()) {
    abort(403, 'CSRF token mismatch');
}
```

## Contributing

### Git Workflow

1. **Fork the repository**

2. **Create a feature branch**
```bash
git checkout -b feature/amazing-feature
```

3. **Make your changes**
- Write clean, documented code
- Follow coding standards
- Add tests for new features

4. **Commit with meaningful messages**
```bash
git add .
git commit -m "feat: Add amazing feature

- Implement X functionality
- Fix Y issue
- Update Z documentation"
```

5. **Push to your fork**
```bash
git push origin feature/amazing-feature
```

6. **Create a Pull Request**
- Describe your changes
- Reference any related issues
- Ensure all tests pass

### Commit Message Convention

Follow the conventional commits specification:
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting, etc)
- `refactor:` Code refactoring
- `test:` Test additions or changes
- `chore:` Build process or auxiliary tool changes

### Code Review Process

1. All code must be reviewed before merging
2. Address all feedback constructively
3. Ensure CI/CD passes
4. Update documentation if needed

## Resources

### Documentation
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [HTMX Documentation](https://htmx.org/docs/)
- [Alpine.js Documentation](https://alpinejs.dev/start-here)

### Tools
- [PHPStan](https://phpstan.org/) - Static analysis
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) - Code style fixer
- [Psalm](https://psalm.dev/) - Static analysis tool
- [PHPUnit](https://phpunit.de/) - Testing framework

### Learning Resources
- [PHP The Right Way](https://phptherightway.com/)
- [Modern PHP](https://www.modernphp.com/)
- [Clean Code PHP](https://github.com/jupeter/clean-code-php)

## Support

- GitHub Issues: Report bugs or request features
- Discord: Join our community for discussions
- Stack Overflow: Tag questions with `infinity-cms`