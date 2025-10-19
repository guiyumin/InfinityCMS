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
        <form method="POST" action="<?= url('/admin/posts') ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="content">Content * <small>(Markdown supported)</small></label>
                <div class="content-tabs">
                    <button type="button" class="tab-button active" onclick="showTab('write')">Write</button>
                    <button type="button" class="tab-button" onclick="showTab('preview')">Preview</button>
                </div>
                <div id="write-tab" class="tab-content active">
                    <textarea id="content" name="content" class="form-control" rows="15" required placeholder="Write your content in Markdown format..."></textarea>
                    <small class="form-help">You can use Markdown formatting: **bold**, *italic*, # headings, [links](url), ![images](url), etc.</small>
                </div>
                <div id="preview-tab" class="tab-content" style="display: none;">
                    <div id="preview-content" class="markdown-preview"></div>
                </div>
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
}
</style>

<script>
// Use a fixed key for new posts
const draftKey = 'post_draft_new';
const draftStatusKey = 'post_draft_status_new';

// Auto-save functionality
let saveTimeout;
let lastSavedContent = '';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const contentField = document.getElementById('content');
    const titleField = document.getElementById('title');
    const excerptField = document.getElementById('excerpt');

    // Restore draft if it exists
    restoreDraft();

    // Set up auto-save listeners
    [contentField, titleField, excerptField].forEach(field => {
        if (field) {
            field.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => saveDraft(), 1000); // Save after 1 second of inactivity
                updateDraftIndicator('typing');
            });
        }
    });

    // Clear draft on successful form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        clearDraft();
    });
});

function saveDraft() {
    const draft = {
        title: document.getElementById('title').value,
        content: document.getElementById('content').value,
        excerpt: document.getElementById('excerpt').value,
        author: document.getElementById('author').value,
        status: document.getElementById('status').value,
        timestamp: Date.now()
    };

    try {
        localStorage.setItem(draftKey, JSON.stringify(draft));
        lastSavedContent = draft.content;
        updateDraftIndicator('saved');
    } catch (e) {
        console.error('Failed to save draft:', e);
        updateDraftIndicator('error');
    }
}

function restoreDraft() {
    try {
        const savedDraft = localStorage.getItem(draftKey);
        if (savedDraft) {
            const draft = JSON.parse(savedDraft);

            // Check if draft is recent (less than 24 hours old)
            const draftTime = new Date(draft.timestamp);
            const timeDiff = Date.now() - draft.timestamp;
            const hoursSince = timeDiff / (1000 * 60 * 60);

            if (hoursSince < 24 && (draft.title || draft.content)) {
                const restore = confirm(`Found an unsaved draft from ${draftTime.toLocaleString()}. Would you like to restore it?`);

                if (restore) {
                    document.getElementById('title').value = draft.title || '';
                    document.getElementById('content').value = draft.content || '';
                    document.getElementById('excerpt').value = draft.excerpt || '';
                    document.getElementById('author').value = draft.author || 'Admin';
                    document.getElementById('status').value = draft.status || 'draft';
                    updateDraftIndicator('restored');
                } else {
                    clearDraft();
                }
            } else if (hoursSince >= 24) {
                // Clear old drafts
                clearDraft();
            }
        }
    } catch (e) {
        console.error('Failed to restore draft:', e);
    }
}

function clearDraft() {
    try {
        localStorage.removeItem(draftKey);
        localStorage.removeItem(draftStatusKey);
        updateDraftIndicator('cleared');
    } catch (e) {
        console.error('Failed to clear draft:', e);
    }
}

function updateDraftIndicator(status) {
    let indicator = document.getElementById('draft-indicator');

    // Create indicator if it doesn't exist
    if (!indicator) {
        indicator = document.createElement('span');
        indicator.id = 'draft-indicator';
        indicator.style.cssText = 'margin-left: 10px; font-size: 0.875rem; padding: 2px 8px; border-radius: 3px;';

        const header = document.querySelector('.page-header h1');
        if (header) {
            header.appendChild(indicator);
        }
    }

    switch(status) {
        case 'typing':
            indicator.textContent = 'Saving...';
            indicator.style.background = '#fef3c7';
            indicator.style.color = '#92400e';
            break;
        case 'saved':
            indicator.textContent = 'Draft saved';
            indicator.style.background = '#d1fae5';
            indicator.style.color = '#065f46';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
            break;
        case 'restored':
            indicator.textContent = 'Draft restored';
            indicator.style.background = '#dbeafe';
            indicator.style.color = '#1e40af';
            break;
        case 'error':
            indicator.textContent = 'Failed to save';
            indicator.style.background = '#fee2e2';
            indicator.style.color = '#991b1b';
            break;
        case 'cleared':
            indicator.style.display = 'none';
            break;
    }

    if (status !== 'cleared') {
        indicator.style.display = 'inline-block';
    }
}

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

        // Save draft before preview
        saveDraft();

        // Render preview using the current content
        const content = document.getElementById('content').value;
        renderPreview(content);
    }
}

function renderPreview(markdown) {
    const previewElement = document.getElementById('preview-content');

    // Show loading state
    previewElement.innerHTML = '<p style="color: #6b7280;">Loading preview...</p>';

    // If no content, show empty message
    if (!markdown || !markdown.trim()) {
        previewElement.innerHTML = '<p style="color: #6b7280;">Nothing to preview. Start typing in the Write tab.</p>';
        return;
    }

    // Send markdown to server for parsing
    fetch('<?= url('/admin/posts/preview') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest' // Identify as AJAX request
        },
        body: 'content=' + encodeURIComponent(markdown),
        credentials: 'same-origin' // Include cookies for session
    })
    .then(response => {
        if (response.status === 401) {
            // User is not authenticated
            window.location.href = '<?= url('/login') ?>';
            throw new Error('Authentication required');
        }
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        previewElement.innerHTML = html;
    })
    .catch(error => {
        console.error('Preview error:', error);
        if (error.message !== 'Authentication required') {
            previewElement.innerHTML = '<p style="color: #ef4444;">Error loading preview. Please try again.</p>';
        }
    });
}

// Add keyboard shortcut for preview (Ctrl/Cmd + Shift + P)
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'P') {
        e.preventDefault();
        const isInPreview = document.getElementById('preview-tab').style.display !== 'none';
        showTab(isInPreview ? 'write' : 'preview');
    }
});
</script>