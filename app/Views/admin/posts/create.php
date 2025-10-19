<div class="admin-container">
    <div class="page-header">
        <h1>Create New Post</h1>
        <a href="<?= url('/admin/posts') ?>" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Posts
        </a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach (session()->get('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="<?= url('/admin/posts') ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="content">Content * <small>(Markdown supported)</small></label>
                <textarea id="content" name="content" class="form-control" rows="15" required placeholder="Write your content in Markdown format..."></textarea>
                <small class="form-help">You can use Markdown formatting: **bold**, *italic*, # headings, [links](url), ![images](url), etc.</small>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" class="form-control" rows="3" placeholder="Brief description of your post (optional)"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" class="form-control" value="Admin">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                <small class="form-help">Supported formats: JPG, PNG, GIF, WebP</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Post</button>
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
}
</style>