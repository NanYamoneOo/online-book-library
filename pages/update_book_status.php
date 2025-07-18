<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';


// Require login
requireLogin();

// Only allow GET requests with required parameters
if($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_GET['book_id']) || !isset($_GET['status'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$book_id = $_GET['book_id'];
$status = $_GET['status'];
$valid_statuses = ['want_to_read', 'reading', 'finished'];

if(!in_array($status, $valid_statuses)) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Check if book exists in user's library
$user_book = $book->isBookInUserLibrary($_SESSION['user_id'], $book_id);

if($user_book) {
    // Update status
    $book->updateUserBookStatus($_SESSION['user_id'], $book_id, $status);
    flash('book_message', 'Book status updated');
}

// Redirect back to previous page or book detail page
if(isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ' . SITE_URL . '/pages/book_detail.php?id=' . $book_id);
}
exit;
?>