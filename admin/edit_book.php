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
            
            flash('book_message', 'Book updated successfully!', 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative');
            header('Location: manage_books.php');
            exit;
        } catch (Exception $e) {
            $errors['general'] = 'Error updating book: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-light text-gray-900">Edit Book</h1>
                    <p class="mt-1 text-gray-500">Update the details of this literary work</p>
                </div>
                <a href="manage_books.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Books
                </a>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 bg-white">
                <h3 class="text-lg font-medium text-gray-900">Book Information</h3>
            </div>
            
            <div class="px-6 py-6">
                <?php if (!empty($errors['general'])): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                        <p><?php echo htmlspecialchars($errors['general']); ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Title Field -->
                        <div class="sm:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 <?php echo !empty($errors['title']) ? 'border-red-500' : ''; ?>"
                                       placeholder="The Great Novel">
                            </div>
                            <?php if (!empty($errors['title'])): ?>
                                <p class="mt-2 text-sm text-red-600">
                                    <?php echo htmlspecialchars($errors['title']); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Author Field -->
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                            <div class="mt-1">
                                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($author); ?>"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Author name">
                            </div>
                        </div>

                        <!-- Year Field -->
                        <div>
                            <label for="publish_year" class="block text-sm font-medium text-gray-700">Publication Year</label>
                            <div class="mt-1">
                                <input type="number" id="publish_year" name="publish_year" value="<?php echo htmlspecialchars($publish_year); ?>"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="YYYY">
                            </div>
                        </div>

                        <!-- Pages Field -->
                        <div>
                            <label for="pages" class="block text-sm font-medium text-gray-700">Pages</label>
                            <div class="mt-1">
                                <input type="number" id="pages" name="pages" value="<?php echo htmlspecialchars($pages); ?>"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Number of pages">
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="5"
                                          class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="A captivating story about..."><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                        </div>

                        <!-- Current Cover -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                                Current Cover
                            </label>
                            <div class="mt-2 flex items-center space-x-6">
                                <?php if ($cover_url): ?>
                                    <div class="relative group">
                                        <img src="<?php echo htmlspecialchars($cover_url); ?>" class="h-40 w-28 object-cover rounded-lg shadow-md border-2 border-gray-200 group-hover:border-blue-400 transition duration-200">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-lg transition duration-200"></div>
                                    </div>
                                    <div class="flex items-center h-10">
                                        <input id="remove_cover" name="remove_cover" type="checkbox" 
                                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="remove_cover" class="ml-3 block text-sm font-medium text-gray-700">
                                            Remove this cover
                                        </label>
                                    </div>
                                <?php else: ?>
                                    <div class="h-40 w-28 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-500 italic">No cover image</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- New Cover Upload -->
                        <div class="sm:col-span-2">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700">Upload New Cover</label>
                            <div class="mt-2 flex justify-center px-6 pt-8 pb-10 border-2 border-gray-300 border-dashed rounded-md bg-gray-50">
                                <div class="space-y-1 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Click to upload</span>
                                            <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            <?php if (!empty($errors['cover_image'])): ?>
                                <p class="mt-2 text-sm text-red-600">
                                    <?php echo htmlspecialchars($errors['cover_image']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="manage_books.php" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>