<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

requireAdmin();

$page_title = 'Admin Dashboard';
$current_page = 'admin_dashboard';

// Get statistics
$db->query('SELECT COUNT(*) as total_users FROM users');
$total_users = $db->single()->total_users;

$db->query('SELECT COUNT(*) as total_books FROM books');
$total_books = $db->single()->total_books;

$db->query('SELECT COUNT(*) as total_user_books FROM user_books');
$total_user_books = $db->single()->total_user_books;

// Get recent users
$db->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5');
$recent_users = $db->resultSet();

// Get recent books added to the system
$db->query('SELECT * FROM books ORDER BY created_at DESC LIMIT 5');
$recent_books = $db->resultSet();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                <p class="text-gray-600">Overview of your library system</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    <i class="fas fa-shield-alt mr-2"></i> Administrator
                </span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Users Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600 mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <h3 class="text-2xl font-semibold text-gray-800"><?= $total_users ?></h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= SITE_URL ?>/admin/manage_users.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            View all users <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Books Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-50 text-green-600 mr-4">
                            <i class="fas fa-book text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Books</p>
                            <h3 class="text-2xl font-semibold text-gray-800"><?= $total_books ?></h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= SITE_URL ?>/admin/manage_books.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            View all books <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Books Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-50 text-blue-600 mr-4">
                            <i class="fas fa-bookmark text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">User Books</p>
                            <h3 class="text-2xl font-semibold text-gray-800"><?= $total_user_books ?></h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= SITE_URL ?>/admin/manage_books.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            View records <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user-clock text-indigo-500 mr-3"></i> Recent Users
                    </h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if(count($recent_users) > 0): ?>
                        <?php foreach($recent_users as $user): ?>
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                        <?= strtoupper(substr($user->username, 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-sm font-medium text-gray-800"><?= htmlspecialchars($user->username) ?></h4>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($user->email) ?></p>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?= $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                                <?= ucfirst($user->role) ?>
                                            </span>
                                            <span class="text-xs text-gray-500 ml-2">
                                                Joined <?= date('M j, Y', strtotime($user->created_at)) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="px-6 py-4 border-t border-gray-200 text-right">
                            <a href="<?= SITE_URL ?>/admin/manage_users.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all users <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-6 text-center text-gray-500">
                            <i class="fas fa-user-slash text-3xl mb-2"></i>
                            <p>No recent users found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Books -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-book-open text-green-500 mr-3"></i> Recently Added Books
                    </h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if(count($recent_books) > 0): ?>
                        <?php foreach($recent_books as $book_item): ?>
                            <a href="<?= SITE_URL ?>/pages/book_detail.php?id=<?= $book_item->id ?>" 
                               class="block p-4 hover:bg-gray-50 transition">
                                <div class="flex">
                                    <?php if(!empty($book_item->cover_url)): ?>
                                        <img src="<?= htmlspecialchars($book_item->cover_url) ?>" 
                                             class="h-16 w-12 object-cover rounded mr-4"
                                             alt="<?= htmlspecialchars($book_item->title) ?>">
                                    <?php else: ?>
                                        <div class="h-16 w-12 bg-gray-100 rounded mr-4 flex items-center justify-center text-gray-400">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-800"><?= htmlspecialchars($book_item->title) ?></h4>
                                        <p class="text-sm text-gray-500">by <?= htmlspecialchars($book_item->author) ?></p>
                                        <div class="mt-1 text-xs text-gray-500">
                                            Added <?= date('M j, Y', strtotime($book_item->created_at)) ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <div class="px-6 py-4 border-t border-gray-200 text-right">
                            <a href="<?= SITE_URL ?>/admin/manage_books.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all books <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-6 text-center text-gray-500">
                            <i class="fas fa-book-dead text-3xl mb-2"></i>
                            <p>No recent books found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>