<div class="admin-container">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?= e(current_user()['username'] ?? 'Admin') ?>!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total_posts'] ?? 0 ?></h3>
                <p>Total Posts</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon published">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $stats['published_posts'] ?? 0 ?></h3>
                <p>Published</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon draft">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $stats['draft_posts'] ?? 0 ?></h3>
                <p>Drafts</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-panel">
            <div class="panel-header">
                <h2>Quick Actions</h2>
            </div>
            <div class="panel-content">
                <div class="quick-actions">
                    <a href="<?= url('/admin/posts/create') ?>" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        New Post
                    </a>
                    <a href="<?= url('/admin/posts') ?>" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        All Posts
                    </a>
                    <a href="<?= url('/admin/migrations') ?>" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                        Migrations
                    </a>
                    <a href="<?= url('/admin/assets') ?>" class="action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        Assets
                    </a>
                </div>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="panel-header">
                <h2>System Status</h2>
            </div>
            <div class="panel-content">
                <div class="status-list">
                    <div class="status-item">
                        <span class="status-label">PHP Version</span>
                        <span class="status-value"><?= PHP_VERSION ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Database</span>
                        <span class="status-value status-success">Connected</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Environment</span>
                        <span class="status-value"><?= config('app.env', 'production') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
