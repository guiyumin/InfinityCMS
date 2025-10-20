<div class="admin-container">
    <div class="page-header">
        <h1>Create New Setting</h1>
        <a href="<?= url('/admin/settings') ?>" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Settings
        </a>
    </div>

    <?php if (isset($_SESSION['errors']) && $_SESSION['errors']): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="<?= url('/admin/settings') ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="setting_key">Setting Key <span class="required">*</span></label>
                <input
                    type="text"
                    id="setting_key"
                    name="setting_key"
                    class="form-control"
                    value="<?= htmlspecialchars(old('setting_key')) ?>"
                    placeholder="e.g., site_name, posts_per_page"
                    required
                >
                <small class="form-help">Use lowercase letters, numbers, and underscores only. Must be unique.</small>
            </div>

            <div class="form-group">
                <label for="setting_value">Setting Value <span class="required">*</span></label>
                <textarea
                    id="setting_value"
                    name="setting_value"
                    class="form-control"
                    rows="4"
                    placeholder="Enter the setting value"
                    required
                ><?= htmlspecialchars(old('setting_value')) ?></textarea>
                <small class="form-help">The actual value for this setting.</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    class="form-control"
                    value="<?= htmlspecialchars(old('description')) ?>"
                    placeholder="Brief description of what this setting does"
                >
                <small class="form-help">Optional description to help identify this setting's purpose.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Setting</button>
                <a href="<?= url('/admin/settings') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.admin-container {
    padding: 2rem;
    max-width: 800px;
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

.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
}

.alert-danger {
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

.form-group label {
    display: block;
    font-weight: 500;
    color: #334155;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-family: inherit;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

textarea.form-control {
    resize: vertical;
    font-family: 'Courier New', monospace;
}

.form-help {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #64748b;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
</style>
