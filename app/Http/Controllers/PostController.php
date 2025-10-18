<?php

namespace App\Http\Controllers;

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

        return view('blog', [
            'title' => 'Blog',
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

        return view('post', [
            'title' => $post['title'],
            'post' => $post,
        ]);
    }
}
