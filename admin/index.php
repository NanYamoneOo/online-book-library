<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';


// Require admin access
requireAdmin();

// Set default page to dashboard if not specified
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define valid admin pages
$valid_pages = [
    'dashboard' => 'dashboard.php',
    'manage_users' => 'manage_users.php',
    'manage_books' => 'manage_books.php',
    'edit_user' => 'edit_user.php'
];

// Check if requested page is valid
if(array_key_exists($page, $valid_pages) && file_exists($valid_pages[$page])) {
    require_once $valid_pages[$page];
} else {
    // Page not found, redirect to dashboard
    header('Location: ' . SITE_URL . '/admin/index.php?page=dashboard');
    exit;
}
?>