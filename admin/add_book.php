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
        <!-- Header with back button -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Add New Book</h1>
            <a href="manage_books.php" class="flex items-center text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Books
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Card Header -->
            <div class="bg-[var(--accent)] px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-800">Book Information</h2>
            </div>
            
            <!-- Form Content -->
            <div class="p-6">
                <?php if (!empty($errors['general'])): ?>
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                        <p><?php echo htmlspecialchars($errors['general']); ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Title Field -->
                        <div class="sm:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <div class="relative">
                                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--accent)] focus:border-transparent transition-all <?php echo !empty($errors['title']) ? 'border-red-500' : ''; ?>"
                                    placeholder="Enter book title">
                                <?php if (!empty($errors['title'])): ?>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($errors['title'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['title']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Author Field -->
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author *</label>
                            <div class="relative">
                                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($author); ?>"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--accent)] focus:border-transparent transition-all <?php echo !empty($errors['author']) ? 'border-red-500' : ''; ?>"
                                    placeholder="Author name">
                                <?php if (!empty($errors['author'])): ?>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($errors['author'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['author']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Publication Year Field -->
                        <div>
                            <label for="publish_year" class="block text-sm font-medium text-gray-700 mb-1">Publication Year</label>
                            <div class="relative">
                                <input type="number" id="publish_year" name="publish_year" value="<?php echo htmlspecialchars($publish_year); ?>"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--accent)] focus:border-transparent transition-all <?php echo !empty($errors['publish_year']) ? 'border-red-500' : ''; ?>"
                                    placeholder="e.g. 2023">
                                <?php if (!empty($errors['publish_year'])): ?>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($errors['publish_year'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['publish_year']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Description Field -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="5"
                                class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--accent)] focus:border-transparent transition-all"
                                placeholder="Book description"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>

                        <!-- Cover Image Upload -->
                        <div class="sm:col-span-2">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                            <div class="mt-1 flex items-center">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500">JPG, PNG or GIF (MAX. 2MB)</p>
                                    </div>
                                    <input id="cover_image" name="cover_image" type="file" class="hidden" accept="image/*">
                                </label>
                            </div>
                            <?php if (!empty($errors['cover_image'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['cover_image']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="manage_books.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>