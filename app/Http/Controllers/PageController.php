<?php

namespace App\Http\Controllers;

/**
 * Page Controller
 * Handles CMS pages by slug
 */
class PageController
{
    /**
     * Show a page by slug
     */
    public function show($slug = 'about')
    {
        // Fetch page from database
        $db = app('db');
        $page = $db->table('pages')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            // Page not found
            http_response_code(404);
            return view('errors/404', [
                'title' => 'Page Not Found'
            ]);
        }

        // Let the theme decide how to render this page
        // Pass the page data and let the theme use its template
        $template = $page['template'] ?? 'page';

        return view($template, [
            'title' => $page['title'],
            'page' => $page,
            'content' => $page['content'],
            'slug' => $page['slug']
        ]);
    }

    /**
     * Show the contact page
     * This is a special case that might need form handling
     */
    public function contact()
    {
        // Fetch the contact page content from database
        $db = app('db');
        $page = $db->table('pages')
            ->where('slug', 'contact')
            ->where('status', 'published')
            ->first();

        // Even if no page exists, show the contact form
        // The theme can decide how to combine content + form
        return view('contact', [
            'title' => $page['title'] ?? 'Contact Us',
            'page' => $page,
            'content' => $page['content'] ?? ''
        ]);
    }

    /**
     * Handle contact form submission
     * This remains as a special handler for contact forms
     */
    public function submitContact()
    {
        $request = app('request');

        // Get form data
        $name = $request->input('name');
        $email = $request->input('email');
        $subject = $request->input('subject', '');
        $message = $request->input('message');

        // Basic validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        if (empty($message)) {
            $errors[] = 'Message is required';
        }

        if (!empty($errors)) {
            // Store errors in session and redirect back
            $_SESSION['contact_errors'] = $errors;
            $_SESSION['_old'] = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ];
            redirect(url('/contact'));
            return;
        }

        // Here you would typically send an email
        // For now, just show a success message

        // You could optionally store submissions if needed
        // But as you said, this is just a page, not infrastructure

        flash('success', 'Thank you for your message! We\'ll get back to you soon.');
        redirect(url('/contact'));
    }
}