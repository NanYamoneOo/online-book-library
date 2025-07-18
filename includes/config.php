<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'online_library');

// Open Library API configuration
define('OPEN_LIBRARY_API', 'https://openlibrary.org/');

// Site configuration
define('SITE_NAME', 'Online Book Library');
define('SITE_URL', 'http://localhost/online-book-library');

// Start session
session_start();

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include classes
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Book.php';

// Create database connection
$db = new Database();

// Initialize User and Book classes
$user = new User($db);
$book = new Book($db);
?>