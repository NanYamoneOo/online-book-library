<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../includes/book_functions.php';

// Require admin access
requireAdmin();

$page_title = 'Edit Book';
$current_page = 'manage_books';

// Get book ID from URL
$book_id = $_GET['id'] ?? 0;
$book_data = $book->getBookById($book_id);

if (!$book_data) {
    flash('book_message', 'Book not found', 'bg-red-500');
    header('Location: manage_books.php');
    exit;
}

// Initialize variables with book data
$title = $book_data->title;
$author = $book_data->author ?? '';
$description = $book_data->description ?? '';
$publish_year = $book_data->publish_year ?? '';
$pages = $book_data->pages ?? '';
$cover_url = $book_data->cover_url ?? '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $publish_year = trim($_POST['publish_year'] ?? '');
    $pages = trim($_POST['pages'] ?? '');
    
    // Validate inputs
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    // Handle file upload if new file was provided
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = handleBookCoverUpload($_FILES['cover_image']);
        if ($upload_result['success']) {
            // Delete old cover if it exists
            if ($cover_url && file_exists(__DIR__ . '/..' . $cover_url)) {
                unlink(__DIR__ . '/..' . $cover_url);
            }
            $cover_url = $upload_result['path'];
        } else {
            $errors['cover_image'] = $upload_result['error'];
        }
    }
    
    // Handle cover removal if requested
    if (isset($_POST['remove_cover'])) {
        if ($cover_url && file_exists(__DIR__ . '/..' . $cover_url)) {
            unlink(__DIR__ . '/..' . $cover_url);
        }
        $cover_url = null;
    }
    
    // If no errors, update the book
    if (empty($errors)) {
        try {
            $book->updateBook($book_id, [
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'publish_year' => $publish_year ?: null,
                'pages' => $pages ?: null,
                'cover_url' => $cover_url ?: null
            ]);
            
            flash('book_message', 'Book updated successfully!', 'bg-green-500');
            header('Location: manage_books.php');
            exit;
        } catch (Exception $e) {
            $errors['general'] = 'Error updating book: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Edit Book</h1>
            <a href="manage_books.php" class="btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Books
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
            <?php if (!empty($errors['general'])): ?>
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    <p><?php echo htmlspecialchars($errors['general']); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php echo !empty($errors['title']) ? 'border-red-500' : ''; ?>">
                        <?php if (!empty($errors['title'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['title']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($author); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="publish_year" class="block text-sm font-medium text-gray-700">Publication Year</label>
                        <input type="number" id="publish_year" name="publish_year" value="<?php echo htmlspecialchars($publish_year); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="pages" class="block text-sm font-medium text-gray-700">Pages</label>
                        <input type="number" id="pages" name="pages" value="<?php echo htmlspecialchars($pages); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Current Cover</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <?php if ($cover_url): ?>
                                <img src="<?php echo htmlspecialchars($cover_url); ?>" class="h-24 w-16 object-cover rounded shadow-sm">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="remove_cover" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Remove cover</span>
                                </label>
                            <?php else: ?>
                                <div class="h-24 w-16 bg-gray-100 rounded flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-500">No cover image</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="cover_image" class="block text-sm font-medium text-gray-700">New Cover Image</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" id="cover_image" name="cover_image" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <?php if (!empty($errors['cover_image'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['cover_image']); ?></p>
                        <?php endif; ?>
                        <p class="mt-2 text-sm text-gray-500">Upload a JPG, PNG, or GIF (max 2MB)</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="manage_books.php" class="btn-outline">Cancel</a>
                    <button type="submit" class="btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>