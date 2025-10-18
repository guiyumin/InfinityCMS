<div class="admin-dashboard">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?= e(current_user()['username'] ?? 'Admin') ?>!</p>
    </div>

    <!-- Stats Cards (auto-refresh with HTMX) -->
    <div
        id="dashboard-stats"
        hx-get="<?= url('/admin/stats') ?>"
        hx-trigger="load, every 30s"
        hx-swap="innerHTML">
        <!-- Stats will load here -->
        <div class="loading">Loading stats...</div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-grid">
            <a href="<?= url('/admin/posts/create') ?>" class="action-card">
                <span class="icon">📝</span>
                <h3>New Post</h3>
                <p>Create a new blog post</p>
            </a>

            <a href="<?= url('/admin/posts') ?>" class="action-card">
                <span class="icon">📄</span>
                <h3>All Posts</h3>
                <p>Manage your content</p>
            </a>

            <a href="<?= url('/admin/migrations') ?>" class="action-card">
                <span class="icon">🗄️</span>
                <h3>Migrations</h3>
                <p>Database management</p>
            </a>

            <a href="<?= url('/') ?>" class="action-card" target="_blank">
                <span class="icon">👁️</span>
                <h3>View Site</h3>
                <p>Preview your website</p>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <h2>Recent Posts</h2>
        <div
            hx-get="<?= url('/api/posts/latest') ?>"
            hx-trigger="load"
            hx-swap="innerHTML">
            <div class="loading">Loading recent posts...</div>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6c757d;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h3 {
    font-size: 2rem;
    color: #3b82f6;
    margin-bottom: 0.5rem;
}

.stat-card p {
    color: #6c757d;
    font-size: 0.875rem;
}

.quick-actions {
    margin-bottom: 3rem;
}

.quick-actions h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.action-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.action-card .icon {
    font-size: 2.5rem;
    display: block;
    margin-bottom: 0.5rem;
}

.action-card h3 {
    font-size: 1.125rem;
    margin-bottom: 0.5rem;
}

.action-card p {
    color: #6c757d;
    font-size: 0.875rem;
}

.recent-activity h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.loading {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.posts-grid {
    display: grid;
    gap: 1rem;
}

.post-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.post-card h3 {
    margin-bottom: 0.5rem;
}

.post-card p {
    color: #6c757d;
    margin-bottom: 1rem;
}

.post-card a {
    color: #3b82f6;
    text-decoration: none;
}

.post-card a:hover {
    text-decoration: underline;
}
</style>
