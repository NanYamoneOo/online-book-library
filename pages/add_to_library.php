<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';


// Require login
requireLogin();

// Only allow GET requests with required parameters
if($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_GET['ol_id']) || !isset($_GET['status'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$ol_id = str_replace('/works/', '', $_GET['ol_id']);
$status = $_GET['status'];
$valid_statuses = ['want_to_read', 'reading', 'finished'];

if(!in_array($status, $valid_statuses)) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Check if book exists in our database
$existing_book = $book->findBookByOlId($ol_id);

if(!$existing_book) {
    // Fetch from Open Library API
    $api_data = $book->getBookDetailsFromOpenLibrary($ol_id);
    
    if($api_data) {
        // Prepare data for our database
        $book_data = [
            'ol_id' => $ol_id,
            'title' => $api_data['title'] ?? 'Unknown Title',
            'author' => isset($api_data['authors'][0]['author']['key']) ? 
                $book->getAuthorDetailsFromOpenLibrary($api_data['authors'][0]['author']['key'])['name'] ?? 'Unknown Author' : 
                'Unknown Author',
            'publish_year' => $api_data['first_publish_date'] ?? null,
            'cover_url' => isset($api_data['covers'][0]) ? 
                'https://covers.openlibrary.org/b/id/' . $api_data['covers'][0] . '-L.jpg' : null,
            'description' => $api_data['description']['value'] ?? $api_data['description'] ?? 'No description available',
            'pages' => $api_data['number_of_pages'] ?? null
        ];
        
        // Add to our database
        $book_id = $book->addBook($book_data);
        if($book_id) {
            $existing_book = $book->getBookById($book_id);
        }
    }
}

if($existing_book) {
    // Check if book is already in user's library
    $user_book = $book->isBookInUserLibrary($_SESSION['user_id'], $existing_book->id);
    
    if($user_book) {
        // Update status
        $book->updateUserBookStatus($_SESSION['user_id'], $existing_book->id, $status);
    } else {
        // Add to library
        $book->addToUserLibrary($_SESSION['user_id'], $existing_book->id, $status);
    }
    
    flash('book_message', 'Book added to your library');
}

// Redirect back to previous page or book detail page
if(isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ' . SITE_URL . '/pages/book_detail.php?id=' . $existing_book->id);
}
exit;
?>