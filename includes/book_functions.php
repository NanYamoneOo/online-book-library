<?php
function handleBookCoverUpload($file) {
    $upload_dir = '/images/book_covers/';
    $absolute_dir = __DIR__ . '/..' . $upload_dir;
    
    // Create directory if it doesn't exist
    if (!file_exists($absolute_dir)) {
        mkdir($absolute_dir, 0755, true);
    }
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Only JPG, PNG, and GIF files are allowed'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size must be less than 2MB'];
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('book_cover_', true) . '.' . $ext;
    $destination = $absolute_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'path' => $upload_dir . $filename];
    }
    
    return ['success' => false, 'error' => 'Error uploading file'];
}