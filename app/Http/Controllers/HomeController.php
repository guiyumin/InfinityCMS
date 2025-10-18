<?php

namespace App\Http\Controllers;

/**
 * Home Controller
 */
class HomeController {
    /**
     * Show home page
     *
     * @return string
     */
    public function index() {
        return view('home', [
            'title' => 'Home',
        ]);
    }
}
