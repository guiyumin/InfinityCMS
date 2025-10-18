<div class="home-page">
    <section class="hero">
        <div class="container">
            <h1>Welcome to <?= e(config('app.name')) ?></h1>
            <p>A powerful, modern CMS built with PHP</p>

            <?php if (is_logged_in()): ?>
                <a href="<?= url('/admin') ?>" class="btn btn-primary">Go to Dashboard</a>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2>Features</h2>

            <div class="feature-grid">
                <div class="feature-card">
                    <h3>Plugin System</h3>
                    <p>Extend functionality with powerful plugins</p>
                </div>

                <div class="feature-card">
                    <h3>Theme Support</h3>
                    <p>Customize your site with beautiful themes</p>
                </div>

                <div class="feature-card">
                    <h3>HTMX Powered</h3>
                    <p>Dynamic content without writing JavaScript</p>
                </div>

                <div class="feature-card">
                    <h3>Alpine.js Integration</h3>
                    <p>Reactive components made simple</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Posts (HTMX example) -->
    <section class="latest-posts">
        <div class="container">
            <h2>Latest Posts</h2>

            <div
                hx-get="<?= url('/api/posts/latest') ?>"
                hx-trigger="load"
                hx-swap="innerHTML">
                <p>Loading posts...</p>
            </div>
        </div>
    </section>
</div>
