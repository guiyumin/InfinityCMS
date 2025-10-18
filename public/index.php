<?php
/**
 * Infinity CMS - Front Controller
 * 前端控制器
 *
 * All requests are routed through this file
 */

// Load and run the application
$app = require __DIR__ . '/../bootstrap/app.php';

// Dispatch the request
$app->router->dispatch();
