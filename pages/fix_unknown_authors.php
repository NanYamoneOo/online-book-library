<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Book.php';

$book = new Book($db);
$book->fixUnknownAuthors();

echo "✅ Unknown authors updated successfully!";
