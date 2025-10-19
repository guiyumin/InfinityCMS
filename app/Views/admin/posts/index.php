<div class="admin-container">
    <div class="page-header">
        <h1>Manage Posts</h1>
        <a href="<?= url('/admin/posts/create') ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Post
        </a>
    </div>

    <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No posts found. <a href="<?= url('/admin/posts/create') ?>">Create your first post</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td>
                                <a href="<?= url('/post/' . $post['slug']) ?>" target="_blank" class="post-title">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($post['author']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $post['status'] ?>">
                                    <?= ucfirst($post['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= url('/admin/posts/' . $post['id'] . '/edit') ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <form method="POST" action="<?= url('/admin/posts/' . $post['id']) ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-container {
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.813rem;
}

.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.table-container {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead {
    background: #f8fafc;
}

.admin-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.813rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    border-bottom: 1px solid #e2e8f0;
}

.admin-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.admin-table tbody tr:hover {
    background: #f8fafc;
}

.post-title {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.post-title:hover {
    text-decoration: underline;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-published {
    background: #dcfce7;
    color: #166534;
}

.status-draft {
    background: #fef3c7;
    color: #92400e;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.text-center {
    text-align: center;
}
</style>