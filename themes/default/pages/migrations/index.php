<div class="admin-migrations" x-data="{ showConfirm: false, action: '' }">
    <div class="page-header">
        <h1>Database Migrations</h1>
        <p>Manage your database schema and seed data</p>
    </div>

    <!-- Migration Actions -->
    <div class="migration-actions">
        <button
            class="btn btn-primary"
            hx-post="<?= url('/admin/migrations/run') ?>"
            hx-target="#migration-results"
            hx-swap="innerHTML">
            Run Migrations
        </button>

        <button
            class="btn btn-warning"
            @click="showConfirm = true; action = 'rollback'">
            Rollback Last Batch
        </button>

        <button
            class="btn btn-danger"
            @click="showConfirm = true; action = 'reset'">
            Reset All Migrations
        </button>

        <button
            class="btn btn-secondary"
            hx-get="<?= url('/admin/migrations/status') ?>"
            hx-target="#migration-status"
            hx-swap="innerHTML">
            Refresh Status
        </button>
    </div>

    <!-- Migration Results -->
    <div id="migration-results" class="migration-results"></div>

    <!-- Migration Status Table -->
    <div class="migration-status-container">
        <h2>Migration Status</h2>
        <div id="migration-status">
            <table class="table">
                <thead>
                    <tr>
                        <th>Migration</th>
                        <th>Status</th>
                        <th>Batch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($migrations as $item): ?>
                        <tr>
                            <td><?= e($item['migration']) ?></td>
                            <td>
                                <span class="badge badge-<?= $item['status'] === 'Migrated' ? 'success' : 'warning' ?>">
                                    <?= e($item['status']) ?>
                                </span>
                            </td>
                            <td><?= $item['batch'] ?? '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showConfirm"
         x-cloak
         class="modal-overlay"
         @click.self="showConfirm = false">
        <div class="modal">
            <h3>Confirm Action</h3>
            <p x-show="action === 'rollback'">
                Are you sure you want to rollback the last batch of migrations?
            </p>
            <p x-show="action === 'reset'">
                Are you sure you want to reset ALL migrations? This will delete all data!
            </p>

            <div class="modal-actions">
                <button
                    class="btn btn-secondary"
                    @click="showConfirm = false">
                    Cancel
                </button>
                <button
                    class="btn btn-danger"
                    x-bind:hx-post="action === 'rollback' ? '<?= url('/admin/migrations/rollback') ?>' : '<?= url('/admin/migrations/reset') ?>'"
                    hx-target="#migration-results"
                    hx-swap="innerHTML"
                    @click="showConfirm = false">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-migrations {
    padding: 2rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.migration-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.migration-results {
    margin-bottom: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.table thead th {
    background: #f8f9fa;
    font-weight: 600;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-warning {
    background: #ffc107;
    color: #000;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
}

.modal h3 {
    margin-bottom: 1rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    justify-content: flex-end;
}

.btn-secondary {
    background: #6c757d;
}

.btn-warning {
    background: #ffc107;
    color: #000;
}

.btn-danger {
    background: #dc3545;
}
</style>
