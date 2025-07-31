<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

// Require login
requireLogin();

$page_title = 'My Books';
$current_page = 'my_books';

// Get status filter
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$valid_statuses = ['all', 'want_to_read', 'reading', 'finished'];

if(!in_array($status, $valid_statuses)) {
    $status = 'all';
}

// Get user's books
$title_suffix = $status == 'all' ? 'All Books' : ucwords(str_replace('_', ' ', $status));
$books = $book->getUserBooksByStatus($_SESSION['user_id'], $status);

require_once __DIR__ . '/../includes/header.php';
?>

<main class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-bookmark text-indigo-500 mr-3"></i> My Library
        </h1>
        
        <!-- Filter Dropdown -->
        <div class="relative mt-4 md:mt-0">
            <div class="inline-flex items-center overflow-hidden rounded-md border bg-white">
                <span class="px-4 py-2 text-sm text-gray-600">
                    <?php echo $title_suffix; ?>
                </span>
                <button class="h-full p-2 text-gray-600 hover:bg-gray-50" onclick="toggleFilterDropdown(event)">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <div id="filterDropdown" class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md border border-gray-100 bg-white shadow-lg hidden">
                <div class="p-2">
                    <a href="?status=all" class="block rounded-lg px-4 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700">
                        <i class="fas fa-books mr-2"></i> All Books
                    </a>
                    <a href="?status=want_to_read" class="block rounded-lg px-4 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700">
                        <i class="fas fa-bookmark mr-2"></i> Want to Read
                    </a>
                    <a href="?status=reading" class="block rounded-lg px-4 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700">
                        <i class="fas fa-book-reader mr-2"></i> Currently Reading
                    </a>
                    <a href="?status=finished" class="block rounded-lg px-4 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700">
                        <i class="fas fa-check-circle mr-2"></i> Finished
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($books) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($books as $book_item): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?id=<?php echo $book_item->id; ?>" class="block">
                        <div class="p-4 flex">
                            <!-- Book Cover -->
                            <?php if($book_item->cover_url): ?>
                                <img src="<?php echo htmlspecialchars($book_item->cover_url); ?>" 
                                     class="w-20 h-28 object-cover rounded-lg shadow-sm mr-4"
                                     alt="<?php echo htmlspecialchars($book_item->title); ?>">
                            <?php else: ?>
                                <div class="w-20 h-28 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center text-gray-400 mr-4">
                                    <i class="fas fa-book text-2xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Book Details -->
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 mb-1 line-clamp-2"><?php echo htmlspecialchars($book_item->title); ?></h3>
                                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($book_item->author); ?></p>
                                
                                <!-- Status Badge -->
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-2 
                                    <?php echo $book_item->status == 'want_to_read' ? 'bg-indigo-100 text-indigo-800' : 
                                          ($book_item->status == 'reading' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $book_item->status)); ?>
                                </span>
                                
                                <!-- Progress or Rating -->
                                <?php if($book_item->status == 'reading' && $book_item->pages > 0): ?>
                                    <div class="mt-2">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span><?php echo $book_item->current_page; ?> of <?php echo $book_item->pages; ?></span>
                                            <span><?php echo round(($book_item->current_page / $book_item->pages) * 100); ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-500 h-1.5 rounded-full" 
                                                 style="width: <?php echo round(($book_item->current_page / $book_item->pages) * 100); ?>%"></div>
                                        </div>
                                    </div>
                                <?php elseif($book_item->status == 'finished' && $book_item->rating): ?>
                                    <div class="mt-1">
                                        <div class="text-yellow-400">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?php echo $i <= $book_item->rating ? 'fas fa-star' : 'far fa-star'; ?> text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if(!empty($book_item->finish_date)): ?>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Finished <?php echo date('M j, Y', strtotime($book_item->finish_date)); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Quick Actions -->
                    <div class="border-t border-gray-100 px-4 py-3 flex justify-between items-center bg-gray-50">
                        <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?id=<?php echo $book_item->id; ?>" 
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
                            <i class="fas fa-eye mr-2"></i> View
                        </a>
                        <form method="POST" action="<?php echo SITE_URL; ?>/pages/remove_from_library.php">
                            <input type="hidden" name="book_id" value="<?php echo $book_item->id; ?>">
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 flex items-center">
                                <i class="fas fa-times mr-2"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            <div class="mx-auto w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-book-open text-3xl text-indigo-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">
                <?php if($status == 'all'): ?>
                    Your library is empty
                <?php else: ?>
                    No books in this category
                <?php endif; ?>
            </h3>
            <p class="text-gray-500 mb-4">
                <?php if($status == 'all'): ?>
                    You haven't added any books to your library yet.
                <?php else: ?>
                    You don't have any books marked as <?php echo str_replace('_', ' ', $status); ?>.
                <?php endif; ?>
            </p>
            <a href="<?php echo SITE_URL; ?>/pages/search.php" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                <i class="fas fa-search mr-2"></i> Find Books
            </a>
        </div>
    <?php endif; ?>
</main>

<script>
    function toggleFilterDropdown(event) {
        event.stopPropagation(); // ✅ Stop click from bubbling to document
        const dropdown = document.getElementById('filterDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('filterDropdown');
        const filterWrapper = document.querySelector('.relative'); 

        if (!filterWrapper.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>