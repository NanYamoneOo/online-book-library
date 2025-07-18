<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth_functions.php';

// Set default page to home if not specified
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define valid pages
$valid_pages = [
    'home' => 'pages/home.php',
    'login' => 'pages/login.php',
    'register' => 'pages/register.php',
    'profile' => 'pages/profile.php',
    'my_books' => 'pages/my_books.php',
    'book_detail' => 'pages/book_detail.php',
    'search' => 'pages/search.php',
    'logout' => 'pages/logout.php'
];

// Check if requested page is valid and include it
if (array_key_exists($page, $valid_pages) && file_exists(__DIR__ . '/' . $valid_pages[$page])) {
    require_once __DIR__ . '/' . $valid_pages[$page];
} else {
    // Redirect to home if page not found
    header('Location: ' . SITE_URL . '/index.php?page=home');
    exit;
}
