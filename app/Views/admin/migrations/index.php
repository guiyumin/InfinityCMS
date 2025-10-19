<div class="admin-container">
    <div class="page-header">
        <h1>Database Migrations</h1>
        <div class="page-actions">
            <form method="POST" action="<?= url('/admin/migrations/run') ?>" style="display: inline;">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to run migrations?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    Run Migrations
                </button>
            </form>

            <form method="POST" action="<?= url('/admin/migrations/rollback') ?>" style="display: inline;">
                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to rollback the last batch?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                    </svg>
                    Rollback
                </button>
            </form>

            <form method="POST" action="<?= url('/admin/migrations/reset') ?>" style="display: inline;">
                <button type="submit" class="btn btn-danger" onclick="return confirm('WARNING: This will rollback ALL migrations and reset the database. Are you sure?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    Reset All
                </button>
            </form>
        </div>
    </div>

    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($message = flash('warning')): ?>
        <div class="alert alert-warning">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($message = flash('danger')): ?>
        <div class="alert alert-danger">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Migration</th>
                    <th>Status</th>
                    <th>Batch</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($migrations)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No migrations found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($migrations as $migration): ?>
                        <tr>
                            <td><?= htmlspecialchars($migration['migration']) ?></td>
                            <td>
                                <?php if ($migration['status'] === 'Migrated'): ?>
                                    <span class="badge badge-success">Migrated</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $migration['batch'] ?? '-' ?></td>
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

.page-actions {
    display: flex;
    gap: 0.5rem;
}

.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
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
    background: #f8f9fa;
}

.admin-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-table td {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    color: #212529;
}

.admin-table tbody tr:hover {
    background: #f8f9fa;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: #6c757d;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1;
    border-radius: 0.25rem;
    text-transform: uppercase;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
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

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}
</style>