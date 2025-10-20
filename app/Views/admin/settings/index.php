<div class="admin-container">
    <div class="page-header">
        <h1>Settings</h1>
        <a href="<?= url('/admin/settings/create') ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Setting
        </a>
    </div>

    <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

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

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Setting Key</th>
                    <th>Value</th>
                    <th>Description</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($settings)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No settings found. <a href="<?= url('/admin/settings/create') ?>">Create your first setting</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($settings as $setting): ?>
                        <tr>
                            <td>
                                <code class="setting-key"><?= htmlspecialchars($setting['setting_key']) ?></code>
                            </td>
                            <td>
                                <div class="setting-value">
                                    <?= htmlspecialchars(strlen($setting['setting_value']) > 50 ? substr($setting['setting_value'], 0, 50) . '...' : $setting['setting_value']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <?= htmlspecialchars($setting['description'] ?? '-') ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($setting['updated_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= url('/admin/settings/edit?id=' . $setting['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <form method="POST" action="<?= url('/admin/settings/delete') ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this setting?');">
                                        <input type="hidden" name="id" value="<?= $setting['id'] ?>">
                                        <?= csrf_field() ?>
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

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
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

.setting-key {
    background: #f1f5f9;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    color: #dc2626;
}

.setting-value {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    color: #334155;
}

.text-muted {
    color: #64748b;
    font-size: 0.875rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.text-center {
    text-align: center;
}
</style>
