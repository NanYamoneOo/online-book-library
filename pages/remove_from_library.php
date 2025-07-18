<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';


// Require login
requireLogin();

// Only allow GET requests with required parameters
if($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_GET['book_id'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$book_id = $_GET['book_id'];

// Remove from library
$book->removeFromUserLibrary($_SESSION['user_id'], $book_id);

flash('book_message', 'Book removed from your library');

// Redirect back to previous page or my books page
if(isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ' . SITE_URL . '/pages/my_books.php');
}
exit;
?>