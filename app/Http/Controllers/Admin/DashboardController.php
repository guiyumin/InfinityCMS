<?php

namespace App\Http\Controllers\Admin;

/**
 * Admin Dashboard Controller
 */
class DashboardController {
    /**
     * Show admin dashboard
     *
     * @return string
     */
    public function index() {
        $stats = [
            'total_posts' => db()->table('posts')->count(),
            'published_posts' => db()->table('posts')->where('status', 'published')->count(),
            'draft_posts' => db()->table('posts')->where('status', 'draft')->count(),
        ];

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ]);
    }

    /**
     * Get dashboard stats (HTMX endpoint)
     *
     * @return string
     */
    public function stats() {
        $stats = [
            'total_posts' => db()->table('posts')->count(),
            'published_posts' => db()->table('posts')->where('status', 'published')->count(),
            'draft_posts' => db()->table('posts')->where('status', 'draft')->count(),
        ];

        $html = '<div class="stats-grid">';
        $html .= '<div class="stat-card">';
        $html .= '<h3>' . $stats['total_posts'] . '</h3>';
        $html .= '<p>Total Posts</p>';
        $html .= '</div>';
        $html .= '<div class="stat-card">';
        $html .= '<h3>' . $stats['published_posts'] . '</h3>';
        $html .= '<p>Published</p>';
        $html .= '</div>';
        $html .= '<div class="stat-card">';
        $html .= '<h3>' . $stats['draft_posts'] . '</h3>';
        $html .= '<p>Drafts</p>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
