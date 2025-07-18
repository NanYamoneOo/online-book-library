<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../includes/book_functions.php';

// Require admin access
requireAdmin();

$page_title = 'Add New Book';
$current_page = 'manage_books';

// Initialize variables
$title = $author = $description = $publish_year = $isbn = $cover_url = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $publish_year = trim($_POST['publish_year'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    
    // Validate inputs
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($author)) {
        $errors['author'] = 'Author is required';
    }
    
    if (!empty($publish_year) && !is_numeric($publish_year)) {
        $errors['publish_year'] = 'Year must be a number';
    }
    
    // Handle file upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = handleBookCoverUpload($_FILES['cover_image']);
        if ($upload_result['success']) {
            $cover_url = $upload_result['path'];
        } else {
            $errors['cover_image'] = $upload_result['error'];
        }
    }
    
    // If no errors, save the book
    if (empty($errors)) {
        try {
            $book->addBook([
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'publish_year' => $publish_year ?: null,
                'isbn' => $isbn ?: null,
                'cover_url' => $cover_url ?: null
            ]);
            
            flash('book_message', 'Book added successfully!', 'bg-green-500');
            header('Location: manage_books.php');
            exit;
        } catch (Exception $e) {
            $errors['general'] = 'Error adding book: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Add New Book</h1>
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
                        <label for="author" class="block text-sm font-medium text-gray-700">Author *</label>
                        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($author); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php echo !empty($errors['author']) ? 'border-red-500' : ''; ?>">
                        <?php if (!empty($errors['author'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['author']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="publish_year" class="block text-sm font-medium text-gray-700">Publication Year</label>
                        <input type="number" id="publish_year" name="publish_year" value="<?php echo htmlspecialchars($publish_year); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php echo !empty($errors['publish_year']) ? 'border-red-500' : ''; ?>">
                        <?php if (!empty($errors['publish_year'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['publish_year']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
                        <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image</label>
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
                    <button type="submit" class="btn-primary">Save Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>