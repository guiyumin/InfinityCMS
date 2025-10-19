<div class="admin-container">
    <div class="page-header">
        <h1>Edit Post</h1>
        <a href="<?= url('/admin/posts') ?>" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Posts
        </a>
    </div>

    <?php if (isset($_SESSION['errors']) && $_SESSION['errors']): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="<?= url('/admin/posts/' . $post['id']) ?>" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="content">Content * <small>(Markdown supported)</small></label>
                <textarea id="content" name="content" class="form-control" rows="15" required placeholder="Write your content in Markdown format..."><?= htmlspecialchars($post['content']) ?></textarea>
                <small class="form-help">You can use Markdown formatting: **bold**, *italic*, # headings, [links](url), ![images](url), etc.</small>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" class="form-control" rows="3" placeholder="Brief description of your post (optional)"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" class="form-control" value="<?= htmlspecialchars($post['author']) ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <?php if (!empty($post['featured_image'])): ?>
                    <div class="current-image">
                        <img src="<?= $post['featured_image'] ?>" alt="Current featured image">
                        <p>Current image</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                <small class="form-help">Upload a new image to replace the current one. Supported formats: JPG, PNG, GIF, WebP</small>
            </div>

            <div class="form-group">
                <label>Post Information</label>
                <div class="post-meta">
                    <p><strong>Slug:</strong> <?= htmlspecialchars($post['slug']) ?></p>
                    <p><strong>Created:</strong> <?= date('F j, Y g:i A', strtotime($post['created_at'])) ?></p>
                    <p><strong>Last Updated:</strong> <?= date('F j, Y g:i A', strtotime($post['updated_at'])) ?></p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="<?= url('/post/' . $post['slug']) ?>" target="_blank" class="btn btn-secondary">View Post</a>
                <a href="<?= url('/admin/posts') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.admin-container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
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

.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.form-container {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

textarea.form-control {
    resize: vertical;
    font-family: inherit;
}

.form-help {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.current-image {
    margin: 1rem 0;
}

.current-image img {
    max-width: 300px;
    max-height: 200px;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
}

.current-image p {
    margin-top: 0.5rem;
    font-size: 0.813rem;
    color: #6b7280;
}

.post-meta {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 0.375rem;
}

.post-meta p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: #4b5563;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
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

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .current-image img {
        max-width: 100%;
    }
}
</style>