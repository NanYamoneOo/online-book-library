<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

$page_title = 'Book Details';
$current_page = 'book_detail';
$logged_in = isLoggedIn();
$book_details = null;
$user_book = null;

// ===== GET BOOK DETAILS =====
if (isset($_GET['id'])) {
    $book_details = $book->getBookById($_GET['id']);
    if ($book_details && $logged_in) {
        $user_book = $book->isBookInUserLibrary($_SESSION['user_id'], $_GET['id']);
    }
} elseif (isset($_GET['ol_id'])) {
    $ol_id = str_replace('/works/', '', $_GET['ol_id']);
    $book_details = $book->findBookByOlId($ol_id);

    if (!$book_details) {
        $api_data = $book->getBookDetailsFromOpenLibrary($ol_id);
        if ($api_data) {
            $author_name = 'Unknown Author';

            if (!empty($api_data['authors'][0])) {
                if (!empty($api_data['authors'][0]['name'])) {
                    $author_name = $api_data['authors'][0]['name'];
                } else {
                    $author_key = $api_data['authors'][0]['author']['key']
                        ?? $api_data['authors'][0]['key'] ?? null;

                    if ($author_key) {
                        $author_data = $book->getAuthorDetailsFromOpenLibrary($author_key);
                        $author_name = $author_data['name'] ?? $author_name;
                    }
                }
            }

            $book_data = [
                'ol_id' => $ol_id,
                'title' => $api_data['title'] ?? 'Unknown Title',
                'author' => $author_name,
                'publish_year' => $api_data['first_publish_date'] ?? null,
                'cover_url' => isset($api_data['covers'][0])
                    ? 'https://covers.openlibrary.org/b/id/' . $api_data['covers'][0] . '-L.jpg'
                    : null,
                'description' => is_array($api_data['description'])
                    ? ($api_data['description']['value'] ?? '')
                    : ($api_data['description'] ?? 'No description available'),
                'pages' => $api_data['number_of_pages'] ?? null
            ];

            $book_id = $book->addBook($book_data);
            if ($book_id) {
                $book_details = $book->getBookById($book_id);
            }
        }
    }

    if ($book_details && $logged_in) {
        $user_book = $book->isBookInUserLibrary($_SESSION['user_id'], $book_details->id);
    }
}

if (!$book_details) {
    flash('book_message', 'Book not found', 'alert alert-danger');
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// ===== FORM HANDLERS =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $logged_in) {
    if (isset($_POST['update_status'])) {
        $status = $_POST['update_status'];
        if ($user_book) {
            $book->updateUserBookStatus($_SESSION['user_id'], $book_details->id, $status);
        } else {
            $book->addToUserLibrary($_SESSION['user_id'], $book_details->id, $status);
        }
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['update_progress'], $_POST['current_page'])) {
        $current_page = max(0, intval($_POST['current_page']));
        if ($user_book) {
            $book->updateReadingProgress($_SESSION['user_id'], $book_details->id, $current_page);
        }
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['update_total_pages'], $_POST['total_pages'])) {
        $total_pages = max(1, intval($_POST['total_pages']));
        $book->updateTotalPages($book_details->id, $total_pages);
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['add_review'], $_POST['rating'], $_POST['review'])) {
        $rating = intval($_POST['rating']);
        $review = trim($_POST['review']);
        if ($user_book && $rating >= 1 && $rating <= 5) {
            $book->addReview($_SESSION['user_id'], $book_details->id, $rating, $review);
        }
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['remove_book']) && $user_book) {
        $book->removeFromUserLibrary($_SESSION['user_id'], $book_details->id);
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Banner -->
    <div class="relative bg-gray-800 text-white rounded-xl overflow-hidden mb-6">
        <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1200&q=60"
            class="absolute inset-0 w-full h-full object-cover opacity-50" alt="Books">
        <div class="relative p-8">
            <h1 class="text-3xl font-bold">Book Details</h1>
            <p class="text-gray-200">Dive into the world of knowledge</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="md:flex">
            <!-- Cover -->
            <div class="md:w-1/3 flex justify-center items-center bg-gray-100 p-4">
                <?php if (!empty($book_details->cover_url)): ?>
                    <img src="<?php echo htmlspecialchars($book_details->cover_url); ?>"
                        alt="<?php echo htmlspecialchars($book_details->title); ?>"
                        class="rounded-lg shadow-md max-h-80 object-cover">
                <?php else: ?>
                    <div class="text-gray-400 text-center italic">No cover available</div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="md:w-2/3 p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <?php echo htmlspecialchars($book_details->title); ?>
                </h1>
                <p class="text-gray-600 mb-3">
                    by <span class="font-medium text-gray-800"><?php echo htmlspecialchars($book_details->author); ?></span>
                </p>

                <?php if (!empty($book_details->publish_year)): ?>
                    <p class="text-sm text-gray-500"><strong>Published:</strong> <?php echo $book_details->publish_year; ?></p>
                <?php endif; ?>
                <p class="text-sm text-gray-500">
                    <strong>Pages:</strong>
                    <?php echo $book_details->pages ?? 'Not set'; ?>
                </p>

                <!-- Actions -->
                <?php if ($logged_in): ?>
                    <div class="mt-5 space-x-2">
                        <form method="POST" class="inline">
                            <?php
                            $statuses = [
                                "want_to_read" => "Want to Read",
                                "reading" => "Currently Reading",
                                "finished" => "Finished"
                            ];
                            foreach ($statuses as $value => $label):
                                $active = $user_book && $user_book->status === $value;
                            ?>
                                <button type="submit" name="update_status" value="<?php echo $value; ?>"
                                    class="px-3 py-1 text-sm rounded-md 
                                    <?php echo $active ? 'bg-[var(--accent)] text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-100'; ?>">
                                    <?php echo $label; ?>
                                </button>
                            <?php endforeach; ?>
                        </form>

                        <?php if ($user_book): ?>
                            <form method="POST" class="inline">
                                <button type="submit" name="remove_book"
                                    class="px-3 py-1 text-sm bg-red-500 text-white rounded-md hover:bg-red-600">
                                    Remove
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Reading Progress -->
                    <?php if ($user_book && $user_book->status === 'reading'): ?>
                        <div class="mt-4 space-y-3">
                            <!-- Update Total Pages (if missing) -->
                            <?php if (empty($book_details->pages)): ?>
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="number" name="total_pages" placeholder="Total pages"
                                        class="w-24 border-gray-300 rounded-md text-sm px-2 py-1" required>
                                    <button type="submit" name="update_total_pages"
                                        class="px-2 py-1 bg-gray-200 text-sm rounded hover:bg-gray-300">
                                        Save Total Pages
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if (!empty($book_details->pages)): ?>
                                <form method="POST" class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-600">Progress:</label>
                                    <input type="number" name="current_page" value="<?php echo $user_book->current_page; ?>"
                                        class="w-20 border-gray-300 rounded-md text-sm px-2 py-1">
                                    <span class="text-xs text-gray-500">/ <?php echo $book_details->pages; ?> pages</span>
                                    <button type="submit" name="update_progress"
                                        class="px-2 py-1 bg-gray-200 text-sm rounded hover:bg-gray-300">Update</button>
                                </form>
                                <?php if ($user_book->current_page > 0): ?>
                                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-500 h-2.5 rounded-full"
                                            style="width: <?php 
                                                $percentage = min(100, max(0, round(($user_book->current_page / $book_details->pages) * 100)));
                                                echo $percentage; 
                                            ?>%">
                                        </div>
                                    </div>
                                    <small class="text-gray-500 text-xs">
                                        <?php echo $percentage; ?>% complete
                                    </small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Description & Review in Same Card -->
        <div class="p-6 border-t">
            <h2 class="text-lg font-semibold mb-2">Description</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                <?php echo nl2br(htmlspecialchars($book_details->description)); ?>
            </p>

            <?php if ($logged_in && $user_book && $user_book->status === 'finished'): ?>
                <hr class="my-4">
                <h3 class="text-lg font-semibold mb-3">Your Review</h3>
                <?php if (!empty($user_book->rating) || !empty($user_book->review)): ?>
                    <div class="mb-3">
                        <strong class="text-gray-700">Rating:</strong>
                        <div class="flex mt-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-5 h-5 <?php echo ($i <= $user_book->rating) ? 'text-yellow-400' : 'text-gray-300'; ?>"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.163c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.955c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.287-3.955a1 1 0 00-.364-1.118L2.17 9.382c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.955z" />
                                </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php if (!empty($user_book->review)): ?>
                        <div class="mb-3">
                            <strong class="text-gray-700">Review:</strong>
                            <p class="mt-1 text-gray-600 italic">
                                <?php echo nl2br(htmlspecialchars($user_book->review)); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                            <div class="flex flex-row-reverse justify-end space-x-reverse space-x-1">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating"
                                        value="<?php echo $i; ?>" class="hidden peer/star<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>"
                                        class="cursor-pointer text-gray-300 hover:text-yellow-400 peer-checked/star<?php echo $i; ?>:text-yellow-400">
                                        ★
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="review" class="block text-sm font-medium text-gray-700 mb-1">Review (Optional)</label>
                            <textarea id="review" name="review" rows="3"
                                class="w-full rounded-lg border-gray-300 text-sm p-2 focus:ring-2 focus:ring-[var(--accent)]"></textarea>
                        </div>
                        <button type="submit" name="add_review"
                            class="bg-[var(--accent)] text-white px-4 py-2 rounded-md hover:opacity-90">
                            Submit Review
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
