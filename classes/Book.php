<?php
class Book {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Add book to database (from Open Library API)
    public function addBook($data) {
        $this->db->query('INSERT INTO books (ol_id, title, author, publish_year, cover_url, description, pages) 
                         VALUES (:ol_id, :title, :author, :publish_year, :cover_url, :description, :pages)');
        
        // Bind values
        $this->db->bind(':ol_id', $data['ol_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':author', $data['author']);
        $this->db->bind(':publish_year', $data['publish_year']);
        $this->db->bind(':cover_url', $data['cover_url']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':pages', $data['pages']);
        
        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Check if book exists in our database by Open Library ID
    public function findBookByOlId($ol_id) {
        $this->db->query('SELECT * FROM books WHERE ol_id = :ol_id');
        $this->db->bind(':ol_id', $ol_id);
        
        $row = $this->db->single();
        
        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }
    
    // Get book by ID
    public function getBookById($id) {
        $this->db->query('SELECT * FROM books WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get all books (for admin)
    public function getAllBooks() {
        $this->db->query('SELECT * FROM books ORDER BY title');
        return $this->db->resultSet();
    }
    
    // Add book to user's library
    public function addToUserLibrary($user_id, $book_id, $status = 'want_to_read') {
        // Initialize current_page to 0 if status is 'reading'
        $current_page = ($status == 'reading') ? 0 : null;
        
        $this->db->query('INSERT INTO user_books (user_id, book_id, status, current_page) 
                         VALUES (:user_id, :book_id, :status, :current_page)');
        
        // Bind values
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':book_id', $book_id);
        $this->db->bind(':status', $status);
        $this->db->bind(':current_page', $current_page);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Update book status in user's library
    public function updateUserBookStatus($user_id, $book_id, $status) {
        // Initialize current_page to 0 if changing to 'reading' status
        $current_page = ($status == 'reading') ? 0 : null;
        
        $this->db->query('UPDATE user_books 
                         SET status = :status, 
                             current_page = :current_page,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE user_id = :user_id AND book_id = :book_id');
        
        // Bind values
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':book_id', $book_id);
        $this->db->bind(':status', $status);
        $this->db->bind(':current_page', $current_page);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Update reading progress
    public function updateReadingProgress($user_id, $book_id, $current_page) {
        $this->db->query('UPDATE user_books 
                         SET current_page = :current_page,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE user_id = :user_id AND book_id = :book_id');
        
        // Bind values
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':book_id', $book_id);
        $this->db->bind(':current_page', $current_page);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Add review and rating
    public function addReview($user_id, $book_id, $rating, $review) {
        $this->db->query('UPDATE user_books 
                         SET rating = :rating, 
                             review = :review, 
                             finish_date = CURRENT_DATE,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE user_id = :user_id AND book_id = :book_id');
        
        // Bind values
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':book_id', $book_id);
        $this->db->bind(':rating', $rating);
        $this->db->bind(':review', $review);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Get user's books by status
    public function getUserBooksByStatus($user_id, $status = 'all') {
        if ($status == 'all') {
            $this->db->query('SELECT b.*, ub.status, ub.current_page, ub.rating, ub.review, ub.start_date, ub.finish_date 
                            FROM books b 
                            JOIN user_books ub ON b.id = ub.book_id 
                            WHERE ub.user_id = :user_id 
                            ORDER BY ub.updated_at DESC');
            $this->db->bind(':user_id', $user_id);
        } else {
            $this->db->query('SELECT b.*, ub.status, ub.current_page, ub.rating, ub.review, ub.start_date, ub.finish_date 
                            FROM books b 
                            JOIN user_books ub ON b.id = ub.book_id 
                            WHERE ub.user_id = :user_id AND ub.status = :status 
                            ORDER BY ub.updated_at DESC');
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':status', $status);
        }

        $books = $this->db->resultSet();
        
        // Ensure current_page is not null for reading books
        foreach ($books as $book) {
            if ($book->status == 'reading' && $book->current_page === null) {
                $book->current_page = 0;
            }
        }
        
        return $books;
    }
    
    // Check if book is in user's library
    public function isBookInUserLibrary($user_id, $book_id) {
        $this->db->query('SELECT * FROM user_books WHERE user_id = :user_id AND book_id = :book_id');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':book_id', $book_id);
        
        $row = $this->db->single();
        
        if($this->db->rowCount() > 0) {
            // Ensure current_page is not null for reading books
            if ($row->status == 'reading' && $row->current_page === null) {
                $row->current_page = 0;
            }
            return $row;
        } else {
            return false;
        }
    }
    
    // Remove book from user's library
    public function removeFromUserLibrary($user_id, $book_id) {
        $this->db->query('DELETE FROM user_books WHERE user_id = :user_id AND book_id = :book_id');
        
        // Bind values
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':book_id', $book_id);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Search books in Open Library API
    public function searchOpenLibrary($query, $page = 1, $limit = 10) {
        $url = OPEN_LIBRARY_API . 'search.json?q=' . urlencode($query) . '&page=' . $page . '&limit=' . $limit;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($output, true);
    }
    
    // Get book details from Open Library API
    public function getBookDetailsFromOpenLibrary($ol_id) {
        $url = rtrim(OPEN_LIBRARY_API, '/') . '/works/' . $ol_id . '.json';
        $output = @file_get_contents($url);
        return $output ? json_decode($output, true) : null;
    }

    // Get author details from Open Library API
    public function getAuthorDetailsFromOpenLibrary($author_key) {
    $author_key = trim($author_key, '/'); // remove extra slashes
    $url = rtrim(OPEN_LIBRARY_API, '/') . '/' . $author_key . '.json';
    $output = @file_get_contents($url);
    return $output ? json_decode($output, true) : null;
}

//  Fix books with "Unknown Author" by fetching from Open Library again
public function fixUnknownAuthors() {
    $this->db->query("SELECT id, ol_id FROM books WHERE author = 'Unknown Author'");
    $books = $this->db->resultSet();

    foreach ($books as $b) {
        if (!empty($b->ol_id)) {
            $api_data = $this->getBookDetailsFromOpenLibrary($b->ol_id);

            if (!empty($api_data['authors'][0])) {
                $author_key = $api_data['authors'][0]['author']['key'] ??
                              $api_data['authors'][0]['key'] ?? null;

                if ($author_key) {
                    $author_data = $this->getAuthorDetailsFromOpenLibrary($author_key);
                    $author_name = $author_data['name'] ?? null;

                    if (!empty($author_name)) {
                        $this->db->query("UPDATE books SET author = :author WHERE id = :id");
                        $this->db->bind(':author', $author_name);
                        $this->db->bind(':id', $b->id);
                        $this->db->execute();
                    }
                }
            }
        }
    }
}

// Update book information in database
public function updateBook($book_id, $data) {
    $this->db->query('UPDATE books SET 
        title = :title,
        author = :author,
        description = :description,
        publish_year = :publish_year,
        cover_url = :cover_url,
        pages = :pages
        WHERE id = :id');
    
    // Bind values
    $this->db->bind(':id', $book_id);
    $this->db->bind(':title', $data['title']);
    $this->db->bind(':author', $data['author'] ?? null);
    $this->db->bind(':description', $data['description'] ?? null);
    $this->db->bind(':publish_year', $data['publish_year'] ?? null);
    $this->db->bind(':cover_url', $data['cover_url'] ?? null);
    $this->db->bind(':pages', $data['pages'] ?? null);
    
    // Execute and return result
    return $this->db->execute();
}

    // Update total pages (manually by user)
    public function updateTotalPages($book_id, $total_pages) {
        $this->db->query('UPDATE books SET pages = :pages WHERE id = :book_id');
        $this->db->bind(':pages', $total_pages);
        $this->db->bind(':book_id', $book_id);

        return $this->db->execute();
    }


}