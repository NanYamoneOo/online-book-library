<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

$page_title = 'Home';
$current_page = 'home';

$logged_in = isLoggedIn();

if ($logged_in) {
    $reading_books = $book->getUserBooksByStatus($_SESSION['user_id'], 'reading');
    $want_to_read_books = $book->getUserBooksByStatus($_SESSION['user_id'], 'want_to_read');
    $finished_books = $book->getUserBooksByStatus($_SESSION['user_id'], 'finished');
}

$popular_books = $book->searchOpenLibrary('subject:fiction', 1, 6);

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-indigo-900 to-purple-800 text-white py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-8 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Discover Your Next<br>Favorite Book</h1>
                <p class="text-xl text-indigo-100 mb-8">Track your reading journey, explore recommendations, and join our community of book lovers.</p>
                <div class="flex space-x-4">
                    <a href="<?php echo SITE_URL; ?>/pages/search.php" class="btn-primary inline-flex items-center">
                        <i class="fas fa-search mr-2"></i> Browse Books
                    </a>
                    <?php if (!$logged_in): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn-outline inline-flex items-center">
                        <i class="fas fa-user-plus mr-2"></i> Join Free
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <div class="relative w-64 h-80 md:w-80 md:h-96">
                    <div class="absolute -top-4 -left-4 w-full h-full border-2 border-indigo-300 rounded-lg"></div>
                    <div class="absolute w-full h-full bg-indigo-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-6xl text-indigo-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT MAIN CONTENT -->
        <div class="lg:col-span-2">
            <!-- Welcome Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-3">Welcome to <?php echo SITE_NAME; ?></h2>
                <p class="text-gray-600 mb-4">Track your reading journey, discover new books, and share your thoughts with our community.</p>
                
                <?php if (!$logged_in): ?>
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 text-indigo-800 p-4 rounded-md">
                        <p>
                            <i class="fas fa-info-circle mr-2"></i>
                            Please 
                            <a href="<?php echo SITE_URL; ?>/pages/login.php" class="font-semibold underline hover:text-indigo-900">login</a> 
                            or 
                            <a href="<?php echo SITE_URL; ?>/pages/register.php" class="font-semibold underline hover:text-indigo-900">register</a> 
                            to start tracking your books.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- POPULAR BOOKS -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h4 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-fire text-red-500 mr-3"></i> Popular Books
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (isset($popular_books['docs']) && count($popular_books['docs']) > 0): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach (array_slice($popular_books['docs'], 0, 6) as $popular_book): ?>
                                <div class="book-card group">
                                    <div class="relative overflow-hidden rounded-t-lg">
                                        <?php if (isset($popular_book['cover_i'])): ?>
                                            <img src="https://covers.openlibrary.org/b/id/<?php echo $popular_book['cover_i']; ?>-M.jpg"
                                                alt="<?php echo htmlspecialchars($popular_book['title']); ?>"
                                                class="w-full h-56 object-cover transition duration-500 group-hover:scale-105">
                                        <?php else: ?>
                                            <div class="w-full h-56 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500">
                                                <i class="fas fa-book-open text-4xl"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black bg-opacity-20 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                                            <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?ol_id=<?php echo urlencode($popular_book['key']); ?>"
                                            class="btn-primary inline-flex items-center transform translate-y-3 group-hover:translate-y-0 transition duration-300">
                                                <i class="fas fa-eye mr-2"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-white">
                                        <h5 class="text-md font-bold text-gray-800 mb-1 truncate">
                                            <?php echo htmlspecialchars($popular_book['title']); ?>
                                        </h5>
                                        <p class="text-sm text-gray-500 mb-3">
                                            <?php echo isset($popular_book['author_name'][0]) 
                                                ? htmlspecialchars($popular_book['author_name'][0]) 
                                                : 'Unknown Author'; ?>
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-indigo-600 font-medium">
                                                <?php echo isset($popular_book['first_publish_year']) 
                                                    ? htmlspecialchars($popular_book['first_publish_year']) 
                                                    : 'Year unknown'; ?>
                                            </span>
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                                <span class="text-xs text-gray-500">
                                                    <?php echo isset($popular_book['ratings_average']) 
                                                        ? round($popular_book['ratings_average'], 1) 
                                                        : 'N/A'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No popular books found at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR (Reading Progress) -->
        <?php if ($logged_in): ?>
        <div class="space-y-6">
            <!-- Reading Progress Widget -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-indigo-500 mr-3"></i> My Reading Progress
                    </h5>
                </div>
                <div class="p-6">
                    <!-- Tabs -->
                    <div class="mb-4">
                        <div class="flex border-b border-gray-200">
                            <button class="py-2 px-4 font-medium text-sm border-b-2 border-indigo-500 text-indigo-600"
                                    onclick="switchTab('reading')">
                                Reading (<?php echo count($reading_books); ?>)
                            </button>
                            <button class="py-2 px-4 font-medium text-sm text-gray-500 hover:text-indigo-600"
                                    onclick="switchTab('want-to-read')">
                                Want to Read (<?php echo count($want_to_read_books); ?>)
                            </button>
                            <button class="py-2 px-4 font-medium text-sm text-gray-500 hover:text-indigo-600"
                                    onclick="switchTab('finished')">
                                Finished (<?php echo count($finished_books); ?>)
                            </button>
                        </div>
                    </div>

                    <!-- Tab Contents -->
                    <div>
                        <!-- Reading Tab -->
                        <div id="tab-reading" class="tab-content space-y-4">
                            <?php if (count($reading_books) > 0): ?>
                                <?php foreach (array_slice($reading_books, 0, 3) as $book_item): ?>
                                    <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?id=<?php echo $book_item->id; ?>" 
                                    class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                        <div class="flex items-start">
                                            <?php if (!empty($book_item->cover_url)): ?>
                                                <img src="<?php echo htmlspecialchars($book_item->cover_url); ?>" 
                                                    alt="<?php echo htmlspecialchars($book_item->title); ?>"
                                                    class="w-12 h-16 object-cover rounded mr-3 group-hover:opacity-90 transition">
                                            <?php else: ?>
                                                <div class="w-12 h-16 bg-gray-200 rounded mr-3 flex items-center justify-center text-gray-400 group-hover:bg-gray-300 transition">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-1">
                                                <h6 class="font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition">
                                                    <?php echo htmlspecialchars($book_item->title); ?>
                                                </h6>
                                                <small class="text-gray-500 block mb-2"><?php echo htmlspecialchars($book_item->author); ?></small>
                                                <div class="flex items-center justify-between">
                                                    <small class="text-gray-500">
                                                        <?php echo $book_item->current_page; ?> / <?php echo $book_item->pages; ?> pages
                                                    </small>
                                                    <small class="font-medium text-indigo-600">
                                                        <?php echo ($book_item->pages > 0) ? round(($book_item->current_page / $book_item->pages) * 100) : 0; ?>%
                                                    </small>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="bg-indigo-600 h-1.5 rounded-full"
                                                        style="width: <?php echo ($book_item->pages > 0) ? round(($book_item->current_page / $book_item->pages) * 100) : 0; ?>%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                                <?php if (count($reading_books) > 3): ?>
                                    <div class="text-center mt-4">
                                        <a href="<?php echo SITE_URL; ?>/pages/my_books.php?status=reading"
                                        class="text-indigo-600 hover:underline text-sm font-medium inline-flex items-center">
                                            View All <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-6">
                                    <i class="fas fa-book-reader text-3xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No books currently being read.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Want to Read Tab -->
                        <div id="tab-want-to-read" class="tab-content hidden space-y-4">
                            <?php if (count($want_to_read_books) > 0): ?>
                                <?php foreach (array_slice($want_to_read_books, 0, 3) as $book_item): ?>
                                    <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?id=<?php echo $book_item->id; ?>" 
                                    class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                        <div class="flex items-start">
                                            <?php if (!empty($book_item->cover_url)): ?>
                                                <img src="<?php echo htmlspecialchars($book_item->cover_url); ?>" 
                                                    alt="<?php echo htmlspecialchars($book_item->title); ?>"
                                                    class="w-12 h-16 object-cover rounded mr-3 group-hover:opacity-90 transition">
                                            <?php else: ?>
                                                <div class="w-12 h-16 bg-gray-200 rounded mr-3 flex items-center justify-center text-gray-400 group-hover:bg-gray-300 transition">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-1">
                                                <h6 class="font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition">
                                                    <?php echo htmlspecialchars($book_item->title); ?>
                                                </h6>
                                                <small class="text-gray-500 block"><?php echo htmlspecialchars($book_item->author); ?></small>
                                            </div>
                                            <div class="ml-2 text-gray-400 group-hover:text-indigo-400 transition">
                                                <i class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                                <?php if (count($want_to_read_books) > 3): ?>
                                    <div class="text-center mt-4">
                                        <a href="<?php echo SITE_URL; ?>/pages/my_books.php?status=want_to_read"
                                        class="text-indigo-600 hover:underline text-sm font-medium inline-flex items-center">
                                            View All <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-6">
                                    <i class="fas fa-bookmark text-3xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No books in your want to read list.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Finished Tab -->
                        <div id="tab-finished" class="tab-content hidden space-y-4">
                            <?php if (count($finished_books) > 0): ?>
                                <?php foreach (array_slice($finished_books, 0, 3) as $book_item): ?>
                                    <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?id=<?php echo $book_item->id; ?>" 
                                    class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                        <div class="flex items-start">
                                            <?php if (!empty($book_item->cover_url)): ?>
                                                <img src="<?php echo htmlspecialchars($book_item->cover_url); ?>" 
                                                    alt="<?php echo htmlspecialchars($book_item->title); ?>"
                                                    class="w-12 h-16 object-cover rounded mr-3 group-hover:opacity-90 transition">
                                            <?php else: ?>
                                                <div class="w-12 h-16 bg-gray-200 rounded mr-3 flex items-center justify-center text-gray-400 group-hover:bg-gray-300 transition">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-1">
                                                <h6 class="font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition">
                                                    <?php echo htmlspecialchars($book_item->title); ?>
                                                </h6>
                                                <div class="flex items-center justify-between">
                                                    <small class="text-gray-500">
                                                        <?php if (!empty($book_item->finish_date)): ?>
                                                            <?php echo date('M j, Y', strtotime($book_item->finish_date)); ?>
                                                        <?php else: ?>
                                                            Finished
                                                        <?php endif; ?>
                                                    </small>
                                                    <div class="text-yellow-400">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="<?php echo $i <= $book_item->rating ? 'fas fa-star' : 'far fa-star'; ?> text-xs"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-2 text-gray-400 group-hover:text-indigo-400 transition">
                                                <i class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                                <?php if (count($finished_books) > 3): ?>
                                    <div class="text-center mt-4">
                                        <a href="<?php echo SITE_URL; ?>/pages/my_books.php?status=finished"
                                        class="text-indigo-600 hover:underline text-sm font-medium inline-flex items-center">
                                            View All <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-6">
                                    <i class="fas fa-check-circle text-3xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No finished books yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-500 mr-3"></i> Quick Actions
                </h5>
                <div class="grid grid-cols-2 gap-4">
                    <a href="<?php echo SITE_URL; ?>/pages/search.php" class="p-3 bg-indigo-50 rounded-lg text-center hover:bg-indigo-100 transition">
                        <i class="fas fa-search text-indigo-600 text-xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Find Books</p>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/my_books.php" class="p-3 bg-indigo-50 rounded-lg text-center hover:bg-indigo-100 transition">
                        <i class="fas fa-bookmark text-indigo-600 text-xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">My Library</p>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
    function switchTab(tab) {
        // Update active tab styling
        document.querySelectorAll('[onclick^="switchTab"]').forEach(btn => {
            btn.classList.remove('border-indigo-500', 'text-indigo-600');
            btn.classList.add('text-gray-500', 'hover:text-indigo-600');
        });
        event.currentTarget.classList.add('border-indigo-500', 'text-indigo-600');
        event.currentTarget.classList.remove('text-gray-500', 'hover:text-indigo-600');
        
        // Show selected tab content
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tab).classList.remove('hidden');
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>