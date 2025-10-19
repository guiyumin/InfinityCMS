<?php

namespace App\Http\Controllers;

use App\Vendors\Parsedown;

/**
 * Post Controller
 */
class PostController {
    /**
     * Show all posts
     *
     * @return string
     */
    public function index() {
        $posts = db()->table('posts')
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->get();

        // Parse Markdown for excerpts
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);

        foreach ($posts as &$post) {
            if (!empty($post['excerpt'])) {
                $post['excerpt_html'] = $parsedown->text($post['excerpt']);
            } else {
                // Create excerpt from content if not provided
                $excerpt = substr($post['content'], 0, 200);
                $post['excerpt_html'] = $parsedown->text($excerpt . '...');
            }
        }

        return view('posts', [
            'title' => 'Posts',
            'posts' => $posts,
        ]);
    }

    /**
     * Show single post
     *
     * @param string $slug
     * @return string
     */
    public function show($slug) {
        $post = db()->table('posts')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$post) {
            app('response')->notFound('Post not found');
        }

        // Parse Markdown content to HTML
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true); // Enable safe mode to prevent XSS
        $post['content_html'] = $parsedown->text($post['content']);

        // Parse excerpt if it exists
        if (!empty($post['excerpt'])) {
            $post['excerpt_html'] = $parsedown->text($post['excerpt']);
        }

        return view('post', [
            'title' => $post['title'],
            'post' => $post,
        ]);
    }
}
