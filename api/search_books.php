<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Only allow GET requests
if($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get query parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(20, max(1, intval($_GET['limit']))) : 10;

if(empty($query)) {
    http_response_code(400);
    echo json_encode(['error' => 'Query parameter "q" is required']);
    exit;
}

// Search Open Library API
$search_results = $book->searchOpenLibrary($query, $page, $limit);

if(isset($search_results['docs'])) {
    // Format results
    $formatted_results = array_map(function($book) {
        return [
            'id' => $book['key'],
            'title' => $book['title'],
            'author' => $book['author_name'][0] ?? 'Unknown Author',
            'year' => $book['first_publish_year'] ?? null,
            'cover' => isset($book['cover_i']) ? 'https://covers.openlibrary.org/b/id/' . $book['cover_i'] . '-M.jpg' : null
        ];
    }, $search_results['docs']);
    
    echo json_encode([
        'results' => $formatted_results,
        'total' => $search_results['numFound'] ?? 0,
        'page' => $page,
        'limit' => $limit
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch results from Open Library API']);
}
?>