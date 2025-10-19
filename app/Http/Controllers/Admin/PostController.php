<?php

namespace App\Http\Controllers\Admin;

use App\Vendors\Parsedown;

/**
 * Admin Post Controller
 */
class PostController {
    /**
     * Display all posts
     *
     * @return string
     */
    public function index() {
        $posts = db()->table('posts')
            ->orderBy('created_at', 'DESC')
            ->get();

        return admin_view('posts.index', [
            'title' => 'Manage Posts',
            'posts' => $posts,
        ]);
    }

    /**
     * Show create post form
     *
     * @return string
     */
    public function create() {
        return admin_view('posts.create', [
            'title' => 'Create New Post',
        ]);
    }

    /**
     * Store a new post
     *
     * @return void
     */
    public function store() {
        $data = request()->all();

        // Validate required fields
        $errors = [];
        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }
        if (empty($data['content'])) {
            $errors[] = 'Content is required';
        }

        if (!empty($errors)) {
            session()->flash('errors', $errors);
            app('response')->redirect('/admin/posts/create');
            return;
        }

        // Generate slug from title
        $slug = $this->generateSlug($data['title']);

        // Handle featured image upload
        $featuredImage = null;
        if (!empty($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $featuredImage = $this->handleImageUpload($_FILES['featured_image']);
        }

        // Insert post
        db()->table('posts')->insert([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'featured_image' => $featuredImage,
            'author' => $data['author'] ?? 'Admin',
            'status' => $data['status'] ?? 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        session()->flash('success', 'Post created successfully');
        app('response')->redirect('/admin/posts');
    }

    /**
     * Show edit post form
     *
     * @param int $id
     * @return string
     */
    public function edit($id) {
        $post = db()->table('posts')->find($id);

        if (!$post) {
            app('response')->notFound('Post not found');
        }

        return admin_view('posts.edit', [
            'title' => 'Edit Post',
            'post' => $post,
        ]);
    }

    /**
     * Update a post
     *
     * @param int $id
     * @return void
     */
    public function update($id) {
        $post = db()->table('posts')->find($id);

        if (!$post) {
            app('response')->notFound('Post not found');
        }

        $data = request()->all();

        // Validate required fields
        $errors = [];
        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }
        if (empty($data['content'])) {
            $errors[] = 'Content is required';
        }

        if (!empty($errors)) {
            session()->flash('errors', $errors);
            app('response')->redirect('/admin/posts/' . $id . '/edit');
            return;
        }

        // Generate new slug if title changed
        $slug = $post['slug'];
        if ($data['title'] !== $post['title']) {
            $slug = $this->generateSlug($data['title'], $id);
        }

        // Handle featured image upload
        $featuredImage = $post['featured_image'];
        if (!empty($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if ($featuredImage && file_exists(public_path($featuredImage))) {
                unlink(public_path($featuredImage));
            }
            $featuredImage = $this->handleImageUpload($_FILES['featured_image']);
        }

        // Update post
        db()->table('posts')->where('id', $id)->update([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'featured_image' => $featuredImage,
            'author' => $data['author'] ?? $post['author'],
            'status' => $data['status'] ?? 'draft',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        session()->flash('success', 'Post updated successfully');
        app('response')->redirect('/admin/posts');
    }

    /**
     * Delete a post
     *
     * @param int $id
     * @return void
     */
    public function destroy($id) {
        $post = db()->table('posts')->find($id);

        if (!$post) {
            app('response')->notFound('Post not found');
        }

        // Delete featured image if exists
        if ($post['featured_image'] && file_exists(public_path($post['featured_image']))) {
            unlink(public_path($post['featured_image']));
        }

        db()->table('posts')->where('id', $id)->delete();

        session()->flash('success', 'Post deleted successfully');
        app('response')->redirect('/admin/posts');
    }

    /**
     * Generate unique slug from title
     *
     * @param string $title
     * @param int|null $excludeId
     * @return string
     */
    private function generateSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

        // Check if slug exists
        $query = db()->table('posts')->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $count = 0;
        $originalSlug = $slug;
        while ($query->first()) {
            $count++;
            $slug = $originalSlug . '-' . $count;
            $query = db()->table('posts')->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Handle image upload
     *
     * @param array $file
     * @return string|null
     */
    private function handleImageUpload($file) {
        $uploadDir = '/uploads/posts/';
        $uploadPath = public_path($uploadDir);

        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('post_') . '.' . $extension;
        $destination = $uploadPath . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $uploadDir . $filename;
        }

        return null;
    }
}