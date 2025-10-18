<?php

namespace App\Http\Controllers\Admin;

use App\Core\Migration;

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

        return view('migrations.index', [
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

        if (is_htmx()) {
            // Return HTMX response
            $html = '<div class="alert alert-success">';
            foreach ($results as $result) {
                $html .= '<p>' . e($result) . '</p>';
            }
            $html .= '</div>';

            echo $html;
            exit;
        }

        flash('success', implode('<br>', $results));
        redirect(url('/admin/migrations'));
    }

    /**
     * Rollback migrations
     *
     * @return void
     */
    public function rollback() {
        $migration = new Migration();
        $results = $migration->rollback();

        if (is_htmx()) {
            $html = '<div class="alert alert-warning">';
            foreach ($results as $result) {
                $html .= '<p>' . e($result) . '</p>';
            }
            $html .= '</div>';

            echo $html;
            exit;
        }

        flash('warning', implode('<br>', $results));
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

        if (is_htmx()) {
            $html = '<div class="alert alert-danger">';
            foreach ($results as $result) {
                $html .= '<p>' . e($result) . '</p>';
            }
            $html .= '</div>';

            echo $html;
            exit;
        }

        flash('danger', implode('<br>', $results));
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
