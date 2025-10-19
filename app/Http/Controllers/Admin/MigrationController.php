<?php

namespace App\Http\Controllers\Admin;

use App\Core\Migration;
use App\Http\Middlewares\AdminMiddleware;

/**
 * Migration Controller
 * 数据库迁移控制器
 */
class MigrationController {
    /**
     * Show migrations page
     *
     * @return string
     */
    public function index() {
        $migration = new Migration();
        $status = $migration->getStatus();

        return admin_view('migrations.index', [
            'title' => 'Database Migrations',
            'migrations' => $status,
        ]);
    }

    /**
     * Run migrations
     *
     * @return void
     */
    public function run() {
        $migration = new Migration();
        $results = $migration->run();

        // Clear migrations cache to reflect changes
        AdminMiddleware::clearMigrationsCache();

        // Check if there are any failures
        $hasErrors = false;
        $successCount = 0;
        $errorCount = 0;

        foreach ($results as $result) {
            if (strpos($result, '✗') !== false) {
                $hasErrors = true;
                $errorCount++;
            } elseif (strpos($result, '✓') !== false) {
                $successCount++;
            }
        }

        if (is_htmx()) {
            // Return HTMX response with appropriate alert type
            $alertClass = $hasErrors ? 'alert-warning' : 'alert-success';
            $html = '<div class="alert ' . $alertClass . '">';

            if ($successCount > 0 || $errorCount > 0) {
                $html .= '<h4>Migration Results: ' . $successCount . ' succeeded, ' . $errorCount . ' failed</h4>';
            }

            foreach ($results as $result) {
                $resultClass = strpos($result, '✗') !== false ? 'text-danger' : '';
                $html .= '<p class="' . $resultClass . '">' . e($result) . '</p>';
            }
            $html .= '</div>';

            echo $html;
            exit;
        }

        $flashType = $hasErrors ? 'warning' : 'success';
        $message = '';
        if ($successCount > 0 || $errorCount > 0) {
            $message = '<strong>Migration Results: ' . $successCount . ' succeeded, ' . $errorCount . ' failed</strong><br>';
        }
        $message .= implode('<br>', $results);

        flash($flashType, $message);
        redirect(url('/admin/migrations'));
    }

    /**
     * Rollback a single migration
     *
     * @return void
     */
    public function rollbackOne() {
        $migrationName = request()->get('migration');

        if (!$migrationName) {
            flash('danger', 'Migration name is required');
            redirect(url('/admin/migrations'));
            return;
        }

        $migration = new Migration();
        $results = $migration->rollbackOne($migrationName);

        // Clear migrations cache to reflect changes
        AdminMiddleware::clearMigrationsCache();

        // Check if there are any failures
        $hasErrors = false;
        foreach ($results as $result) {
            if (strpos($result, '✗') !== false) {
                $hasErrors = true;
                break;
            }
        }

        if (is_htmx()) {
            $alertClass = $hasErrors ? 'alert-danger' : 'alert-warning';
            $html = '<div class="alert ' . $alertClass . '">';
            foreach ($results as $result) {
                $resultClass = strpos($result, '✗') !== false ? 'text-danger' : '';
                $html .= '<p class="' . $resultClass . '">' . e($result) . '</p>';
            }
            $html .= '</div>';

            echo $html;
            exit;
        }

        $flashType = $hasErrors ? 'danger' : 'warning';
        $message = implode('<br>', $results);

        flash($flashType, $message);
        redirect(url('/admin/migrations'));
    }

    /**
     * Reset all migrations
     *
     * @return void
     */
    public function reset() {
        $migration = new Migration();
        $results = $migration->reset();

        // Clear migrations cache to reflect changes
        AdminMiddleware::clearMigrationsCache();

        // Check if there are any failures
        $hasErrors = false;
        $successCount = 0;
        $errorCount = 0;

        foreach ($results as $result) {
            if (strpos($result, '✗') !== false) {
                $hasErrors = true;
                $errorCount++;
            } elseif (strpos($result, '✓') !== false) {
                $successCount++;
            }
        }

        if (is_htmx()) {
            $alertClass = $hasErrors ? 'alert-danger' : 'alert-warning';
            $html = '<div class="alert ' . $alertClass . '">';

            if ($successCount > 0 || $errorCount > 0) {
                $html .= '<h4>Reset Results: ' . $successCount . ' succeeded, ' . $errorCount . ' failed</h4>';
            }

            foreach ($results as $result) {
                $resultClass = strpos($result, '✗') !== false ? 'text-danger' : '';
                $html .= '<p class="' . $resultClass . '">' . e($result) . '</p>';
            }
            $html .= '</div>';

            echo $html;
            exit;
        }

        $flashType = $hasErrors ? 'danger' : 'info';
        $message = '';
        if ($successCount > 0 || $errorCount > 0) {
            $message = '<strong>Reset Results: ' . $successCount . ' succeeded, ' . $errorCount . ' failed</strong><br>';
        }
        $message .= implode('<br>', $results);

        flash($flashType, $message);
        redirect(url('/admin/migrations'));
    }

    /**
     * Get migration status (HTMX endpoint)
     *
     * @return string
     */
    public function status() {
        $migration = new Migration();
        $status = $migration->getStatus();

        $html = '<table class="table">';
        $html .= '<thead><tr><th>Migration</th><th>Status</th><th>Batch</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($status as $item) {
            $statusClass = $item['status'] === 'Migrated' ? 'success' : 'warning';
            $html .= '<tr>';
            $html .= '<td>' . e($item['migration']) . '</td>';
            $html .= '<td><span class="badge badge-' . $statusClass . '">' . $item['status'] . '</span></td>';
            $html .= '<td>' . ($item['batch'] ?? '-') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}
