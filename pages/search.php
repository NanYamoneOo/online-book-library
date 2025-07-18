<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

$page_title = 'Search Books';
$current_page = 'search';

$logged_in = isLoggedIn();
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;

$results = [];
$total_results = 0;
$total_pages = 1;

if (!empty($query)) {
    $search_results = $book->searchOpenLibrary($query, $page, $limit);

    if (isset($search_results['docs'])) {
        $results = $search_results['docs'];
        $total_results = $search_results['numFound'] ?? 0;
        $total_pages = ceil($total_results / $limit);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Search Books</h1>
        <p class="text-gray-600">Find books from the Open Library catalog</p>
    </div>

    <!-- Search Form -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET" class="mb-6 flex space-x-2">
        <input type="text" name="q" placeholder="Search by title, author, or ISBN..."
            value="<?php echo htmlspecialchars($query); ?>"
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:outline-none">
        <button type="submit"
            class="bg-[var(--accent)] text-white px-4 py-2 rounded-lg hover:opacity-90 transition">Search</button>
    </form>

    <?php if (!empty($query)): ?>
        <div class="mb-4 text-sm text-gray-600">
            Found <strong><?php echo number_format($total_results); ?></strong> results for
            "<span class="italic"><?php echo htmlspecialchars($query); ?></span>"
        </div>

        <?php if (count($results) > 0): ?>
            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($results as $book_item): ?>
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 flex space-x-4">
                        <!-- Cover -->
                        <div class="w-24 h-32 flex-shrink-0">
                            <?php if (isset($book_item['cover_i'])): ?>
                                <img src="https://covers.openlibrary.org/b/id/<?php echo $book_item['cover_i']; ?>-M.jpg"
                                    alt="<?php echo htmlspecialchars($book_item['title']); ?>"
                                    class="w-full h-full object-cover rounded">
                            <?php else: ?>
                                <div
                                    class="w-full h-full bg-gray-100 flex items-center justify-center rounded text-gray-400 text-xs text-center">
                                    No cover
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($book_item['title']); ?>
                                </h3>
                                <p class="text-xs text-gray-500">
                                    <?php if (isset($book_item['author_name'][0])): ?>
                                        By <?php echo htmlspecialchars($book_item['author_name'][0]); ?><br>
                                    <?php endif; ?>
                                    <?php if (isset($book_item['first_publish_year'])): ?>
                                        Published: <?php echo $book_item['first_publish_year']; ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="mt-2 flex items-center space-x-2">
                                <a href="<?php echo SITE_URL; ?>/pages/book_detail.php?ol_id=<?php echo str_replace('/works/', '', $book_item['key']); ?>"
                                    class="px-3 py-1 text-xs bg-[var(--accent)] text-white rounded hover:opacity-90">View
                                    Details</a>

                                <?php if ($logged_in): ?>
                                    <?php
                                    $user_book = false;
                                    $ol_id_clean = str_replace('/works/', '', $book_item['key']);
                                    $existing_book = $book->findBookByOlId($ol_id_clean);

                                    if ($existing_book) {
                                        $user_book = $book->isBookInUserLibrary($_SESSION['user_id'], $existing_book->id);
                                    }
                                    ?>

                                    <div class="relative">
                                        <button type="button"
                                            class="px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100"
                                            onclick="this.nextElementSibling.classList.toggle('hidden')">
                                            <?php echo $user_book ? 'In Library' : 'Add'; ?>
                                        </button>
                                        <ul
                                            class="absolute z-10 bg-white border rounded shadow-md mt-1 w-40 hidden text-xs">
                                            <?php if ($user_book): ?>
                                                <li><a class="block px-3 py-2 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/update_book_status.php?book_id=<?php echo $existing_book->id; ?>&status=reading">Currently
                                                        Reading</a></li>
                                                <li><a class="block px-3 py-2 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/update_book_status.php?book_id=<?php echo $existing_book->id; ?>&status=want_to_read">Want
                                                        to Read</a></li>
                                                <li><a class="block px-3 py-2 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/update_book_status.php?book_id=<?php echo $existing_book->id; ?>&status=finished">Finished</a>
                                                </li>
                                                <li class="border-t"><a class="block px-3 py-2 text-red-500 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/remove_from_library.php?book_id=<?php echo $existing_book->id; ?>">Remove</a>
                                                </li>
                                            <?php else: ?>
                                                <li><a class="block px-3 py-2 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/add_to_library.php?ol_id=<?php echo $ol_id_clean; ?>&status=reading">Currently
                                                        Reading</a></li>
                                                <li><a class="block px-3 py-2 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/add_to_library.php?ol_id=<?php echo $ol_id_clean; ?>&status=want_to_read">Want
                                                        to Read</a></li>
                                                <li><a class="block px-3 py-2 hover:bg-gray-100"
                                                        href="<?php echo SITE_URL; ?>/pages/add_to_library.php?ol_id=<?php echo $ol_id_clean; ?>&status=finished">Finished</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>"
                            class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= min($total_pages, 5); $i++): ?>
                        <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>"
                            class="px-3 py-1 border rounded text-sm <?php echo $i == $page ? 'bg-[var(--accent)] text-white' : 'hover:bg-gray-100'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>"
                            class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded">
                No books found for your search.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded">
            Enter a search term to find books in the Open Library catalog.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
