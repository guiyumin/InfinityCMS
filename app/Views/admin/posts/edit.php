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
                <div class="content-tabs">
                    <button type="button" class="tab-button active" onclick="showTab('write')">Write</button>
                    <button type="button" class="tab-button" onclick="showTab('preview')">Preview</button>
                </div>
                <div id="write-tab" class="tab-content active">
                    <textarea id="content" name="content" class="form-control" rows="15" required placeholder="Write your content in Markdown format..."><?= htmlspecialchars($post['content']) ?></textarea>
                    <small class="form-help">You can use Markdown formatting: **bold**, *italic*, # headings, [links](url), ![images](url), etc.</small>
                </div>
                <div id="preview-tab" class="tab-content" style="display: none;">
                    <div id="preview-content" class="markdown-preview"></div>
                </div>
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

.content-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.tab-button {
    padding: 0.5rem 1rem;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.tab-button:hover {
    color: #3b82f6;
}

.tab-button.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

.tab-content {
    min-height: 400px;
}

.markdown-preview {
    padding: 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background: white;
    min-height: 400px;
    overflow-y: auto;
}

.markdown-preview h1 { font-size: 2em; margin: 0.67em 0; font-weight: bold; }
.markdown-preview h2 { font-size: 1.5em; margin: 0.83em 0; font-weight: bold; }
.markdown-preview h3 { font-size: 1.17em; margin: 1em 0; font-weight: bold; }
.markdown-preview h4 { font-size: 1em; margin: 1.33em 0; font-weight: bold; }
.markdown-preview h5 { font-size: 0.83em; margin: 1.67em 0; font-weight: bold; }
.markdown-preview h6 { font-size: 0.67em; margin: 2.33em 0; font-weight: bold; }
.markdown-preview p { margin: 1em 0; line-height: 1.6; }
.markdown-preview ul, .markdown-preview ol { margin: 1em 0; padding-left: 2em; }
.markdown-preview li { margin: 0.5em 0; }
.markdown-preview blockquote {
    margin: 1em 0;
    padding: 0.5em 1em;
    border-left: 4px solid #e5e7eb;
    background: #f9fafb;
}
.markdown-preview code {
    background: #f3f4f6;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-family: monospace;
    font-size: 0.875em;
}
.markdown-preview pre {
    background: #1e293b;
    color: #e2e8f0;
    padding: 1em;
    border-radius: 0.375rem;
    overflow-x: auto;
    margin: 1em 0;
}
.markdown-preview pre code {
    background: none;
    padding: 0;
    color: inherit;
}
.markdown-preview img {
    max-width: 100%;
    height: auto;
}
.markdown-preview a {
    color: #3b82f6;
    text-decoration: underline;
}
.markdown-preview hr {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 2em 0;
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

<script>
function showTab(tab) {
    const writeTab = document.getElementById('write-tab');
    const previewTab = document.getElementById('preview-tab');
    const buttons = document.querySelectorAll('.tab-button');

    if (tab === 'write') {
        writeTab.style.display = 'block';
        previewTab.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    } else {
        writeTab.style.display = 'none';
        previewTab.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');

        // Render preview
        const content = document.getElementById('content').value;
        renderPreview(content);
    }
}

function renderPreview(markdown) {
    const previewElement = document.getElementById('preview-content');

    // Show loading state
    previewElement.innerHTML = '<p style="color: #6b7280;">Loading preview...</p>';

    // Send markdown to server for parsing
    fetch('<?= url('/admin/posts/preview') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'content=' + encodeURIComponent(markdown)
    })
    .then(response => response.text())
    .then(html => {
        previewElement.innerHTML = html || '<p style="color: #6b7280;">Nothing to preview</p>';
    })
    .catch(error => {
        previewElement.innerHTML = '<p style="color: #ef4444;">Error loading preview</p>';
    });
}
</script>