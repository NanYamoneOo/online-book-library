<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

// Require admin access
requireAdmin();

$page_title = 'Edit User';
$current_page = 'manage_users';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$user_id) {
    header('Location: ' . SITE_URL . '/admin/manage_users.php');
    exit;
}

// Get user data
$user_data = $user->getUserById($user_id);
if(!$user_data) {
    header('Location: ' . SITE_URL . '/admin/manage_users.php');
    exit;
}

// Initialize variables
$errors = [];
$username = $user_data->username;
$email = $user_data->email;
$role = $user_data->role;

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    
    // Validate inputs
    if(empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif(strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters';
    }
    
    if(empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email';
    }
    
    if(empty($role)) {
        $errors['role'] = 'Role is required';
    }
    
    // Check if username/email already exists (excluding current user)
    if($username != $user_data->username && $user->findUserByUsername($username)) {
        $errors['username'] = 'Username already taken';
    }
    
    if($email != $user_data->email && $user->findUserByEmail($email)) {
        $errors['email'] = 'Email already registered';
    }
    
    // Update user if no errors
    if(empty($errors)) {
        $data = [
            'id' => $user_id,
            'username' => $username,
            'email' => $email,
            'role' => $role
        ];
        
        if($user->updateUser($data)) {
            flash('user_message', 'User updated successfully', 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative');
            header('Location: ' . SITE_URL . '/admin/manage_users.php');
            exit;
        } else {
            flash('user_message', 'Something went wrong. Please try again.', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Decorative Header -->
        <div class="text-center mb-12 relative">
            <div class="absolute -top-6 -left-6 w-16 h-16 rounded-full bg-indigo-200 opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full bg-blue-200 opacity-30 animate-pulse"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold text-gray-800 mb-3 tracking-tight">Edit User</h1>
                <p class="text-lg text-gray-600 max-w-md mx-auto">Modify the details below and update user information.</p>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <div class="bg-gradient-to-r from-indigo-500 to-blue-600 h-2"></div>

            <div class="p-8 sm:p-10">
                <!-- Flash Message -->
                <?php if ($message = Flash('user_message')): ?>
                    <div class="mb-6 p-4 rounded-lg text-sm flex items-center 
                        <?php echo strpos($message['class'], 'bg-green-100') !== false 
                            ? 'bg-green-50 border border-green-200 text-green-700' 
                            : 'bg-red-50 border border-red-200 text-red-700'; ?>">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endif; ?>

                <!-- Edit User Form -->
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo $user_id; ?>">

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Username
                        </label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>"
                            class="block w-full px-4 py-3 border rounded-lg transition duration-150 ease-in-out
                                <?php echo isset($errors['username']) 
                                    ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'; ?>"
                            placeholder="Enter username">
                        <?php if (isset($errors['username'])): ?>
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <?php echo htmlspecialchars($errors['username']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>"
                            class="block w-full px-4 py-3 border rounded-lg transition duration-150 ease-in-out
                                <?php echo isset($errors['email']) 
                                    ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'; ?>"
                            placeholder="user@example.com">
                        <?php if (isset($errors['email'])): ?>
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                            User Role
                        </label>
                        <select id="role" name="role"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                            <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>Regular User</option>
                            <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-4 pt-8 border-t border-gray-200">
                        <a href="<?php echo SITE_URL; ?>/admin/manage_users.php"
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                            Back to Users
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>

                <!-- Delete Option -->
                <?php if($user_data->id != $_SESSION['user_id']): ?>
                    <hr class="my-6 border-gray-100">
                    <button onclick="openDeleteModal()"
                        class="w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete User
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete User</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this user? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form method="POST" action="<?php echo SITE_URL; ?>/admin/manage_users.php" class="inline-flex">
                    <input type="hidden" name="user_id" value="<?php echo $user_data->id; ?>">
                    <button type="submit" name="delete_user"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
        <div class="mt-12 text-center text-sm text-gray-500">
            <p>Need help? <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 transition">Contact support</a></p>
        </div>
    </div>
</div>

<script>
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>