-- Create database
CREATE DATABASE IF NOT EXISTS online_library;
USE online_library;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Books table (for storing book details from Open Library)
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ol_id VARCHAR(20) NOT NULL UNIQUE, -- Open Library ID
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    publish_year INT,
    cover_url VARCHAR(255),
    description TEXT,
    pages INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User books (tracking user's reading progress)
CREATE TABLE user_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    status ENUM('want_to_read', 'reading', 'finished') NOT NULL DEFAULT 'want_to_read',
    current_page INT DEFAULT 0,
    rating INT,
    review TEXT,
    start_date DATE,
    finish_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, book_id)
);

-- Create indexes for better performance
CREATE INDEX idx_user_books_user ON user_books(user_id);
CREATE INDEX idx_user_books_book ON user_books(book_id);