<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

// Require login
requireLogin();

$page_title = 'My Profile';
$current_page = 'profile';

// Get current user data
$user_data = $user->getUserById($_SESSION['user_id']);
if (!$user_data) {
    flash('profile_message', 'User data not found', 'bg-red-100 border-l-4 border-red-500 text-red-700');
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Process profile update form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validate inputs
    if (empty($username) || empty($email)) {
        flash('profile_message', 'Please fill in all fields', 'bg-red-100 border-l-4 border-red-500 text-red-700');
    } else {
        // Check if username/email already exists (excluding current user)
        $existing_user = $user->getUserById($_SESSION['user_id']);
        
        if ($username !== $existing_user->username && $user->findUserByUsername($username)) {
            flash('profile_message', 'Username already taken', 'bg-red-100 border-l-4 border-red-500 text-red-700');
        } elseif ($email !== $existing_user->email && $user->findUserByEmail($email)) {
            flash('profile_message', 'Email already registered', 'bg-red-100 border-l-4 border-red-500 text-red-700');
        } else {
            // Update profile
            if ($user->updateProfile([
                'id' => $_SESSION['user_id'],
                'username' => $username,
                'email' => $email
            ])) {
                $_SESSION['user_username'] = $username;
                $_SESSION['user_email'] = $email;
                flash('profile_message', 'Profile updated successfully', 'bg-green-100 border-l-4 border-green-500 text-green-700');
            } else {
                flash('profile_message', 'Something went wrong. Please try again.', 'bg-red-100 border-l-4 border-red-500 text-red-700');
            }
        }
    }
}

// Process password change form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        flash('password_message', 'Please fill in all fields', 'bg-red-100 border-l-4 border-red-500 text-red-700');
    } elseif ($new_password !== $confirm_password) {
        flash('password_message', 'New passwords do not match', 'bg-red-100 border-l-4 border-red-500 text-red-700');
    } elseif (strlen($new_password) < 6) {
        flash('password_message', 'Password must be at least 6 characters', 'bg-red-100 border-l-4 border-red-500 text-red-700');
    } else {
        // Verify current password
        $loggedInUser = $user->login($user_data->username, $current_password);
        
        if ($loggedInUser) {
            // Change password
            if ($user->changePassword($user_data->id, $new_password)) {
                flash('password_message', 'Password changed successfully', 'bg-green-100 border-l-4 border-green-500 text-green-700');
            } else {
                flash('password_message', 'Something went wrong. Please try again.', 'bg-red-100 border-l-4 border-red-500 text-red-700');
            }
        } else {
            flash('password_message', 'Current password is incorrect', 'bg-red-100 border-l-4 border-red-500 text-red-700');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Profile Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-8">
            <div class="relative">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 shadow-md flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($user_data->username); ?></h1>
                <p class="text-gray-600 mt-1">Member since <?php echo date('F Y', strtotime($user_data->created_at)); ?></p>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="space-y-4 mb-8">
            <?php flash('profile_message'); ?>
            <?php flash('password_message'); ?>
        </div>

        <div class="space-y-6">
            <!-- Profile Information Card -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Profile Information
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user_data->username ?? ''); ?>"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200"
                                   required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user_data->email ?? ''); ?>"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200"
                                   required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" name="update_profile"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Change Password
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200"
                                   required>
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200"
                                   required>
                            <p class="mt-2 text-xs text-gray-500">Must be at least 6 characters</p>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200"
                                   required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" name="change_password"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!isAdmin()): ?>
            <!-- Danger Zone Card -->
            <div class="bg-white shadow rounded-lg overflow-hidden border border-red-200">
                <div class="px-6 py-5 border-b border-red-200 bg-red-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Danger Zone
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Delete Account</h4>
                            <p class="text-sm text-gray-600 mt-1">Permanently remove your account and all associated data</p>
                        </div>
                        <button class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 shadow-sm hover:shadow-md whitespace-nowrap"
                                data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-b border-gray-200 px-6 py-4">
                <h5 class="modal-title text-lg font-bold text-gray-900">Confirm Account Deletion</h5>
                <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Are you absolutely sure?</h3>
                <p class="text-gray-600 mb-4">This will permanently delete your account and all associated data.</p>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end">
                <button type="button" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition duration-200" 
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <form action="<?php echo SITE_URL; ?>/pages/delete_account.php" method="POST" class="ml-3">
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 shadow-sm">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>