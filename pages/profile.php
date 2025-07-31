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
    flash('profile_message', 'User data not found', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Process profile update form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validate inputs
    if (empty($username) || empty($email)) {
        flash('profile_message', 'Please fill in all fields', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
    } else {
        // Check if username/email already exists (excluding current user)
        $existing_user = $user->getUserById($_SESSION['user_id']);
        
        if ($username !== $existing_user->username && $user->findUserByUsername($username)) {
            flash('profile_message', 'Username already taken', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
        } elseif ($email !== $existing_user->email && $user->findUserByEmail($email)) {
            flash('profile_message', 'Email already registered', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
        } else {
            // Update profile
            if ($user->updateProfile([
                'id' => $_SESSION['user_id'],
                'username' => $username,
                'email' => $email
            ])) {
                $_SESSION['user_username'] = $username;
                $_SESSION['user_email'] = $email;
                flash('profile_message', 'Profile updated successfully', 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4');
            } else {
                flash('profile_message', 'Something went wrong. Please try again.', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
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
        flash('password_message', 'Please fill in all fields', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
    } elseif ($new_password !== $confirm_password) {
        flash('password_message', 'New passwords do not match', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
    } elseif (strlen($new_password) < 6) {
        flash('password_message', 'Password must be at least 6 characters', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
    } else {
        // Verify current password
        $loggedInUser = $user->login($user_data->username, $current_password);
        
        if ($loggedInUser) {
            // Change password
            if ($user->changePassword($user_data->id, $new_password)) {
                flash('password_message', 'Password changed successfully', 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4');
            } else {
                flash('password_message', 'Something went wrong. Please try again.', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
            }
        } else {
            flash('password_message', 'Current password is incorrect', 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Header -->
        <div class="flex flex-col sm:flex-row items-center gap-6 mb-8 p-6 bg-white rounded-xl shadow-sm">
            <div class="relative">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-200 shadow-md flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <div class="text-center sm:text-left">
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($user_data->username); ?></h1>
                <p class="text-gray-600 mt-1">Member since <?php echo date('F Y', strtotime($user_data->created_at)); ?></p>
                <div class="mt-3 flex justify-center sm:justify-start gap-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Active</span>
                    <?php if (isAdmin()): ?>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Admin</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="space-y-4 mb-8">
            <?php flash('profile_message'); ?>
            <?php flash('password_message'); ?>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Profile Information Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Profile Information
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user_data->username ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user_data->email ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" name="update_profile"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Change Password
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required>
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters</p>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" name="change_password"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (!isAdmin()): ?>
        <!-- Danger Zone Card -->
        <div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden border border-red-100">
            <div class="px-6 py-4 border-b border-red-200 bg-gradient-to-r from-red-50 to-white">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Danger Zone
                </h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900">Delete Account</h4>
                        <p class="text-sm text-gray-600 mt-1">Permanently remove your account and all associated data</p>
                    </div>
                    <button onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')"
                            class="flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Confirm Account Deletion</h3>
            <button onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Are you absolutely sure?</h3>
            <p class="text-gray-600 mb-4">This will permanently delete your account and all associated data.</p>
            <p class="text-sm text-gray-500">This action cannot be undone.</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition duration-200 mr-3">
                Cancel
            </button>
            <form action="<?php echo SITE_URL; ?>/pages/delete_account.php" method="POST">
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                    Delete Account
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("click", function (e) {
    const modal = document.getElementById("deleteAccountModal");
    if (e.target === modal) {
        modal.classList.add("hidden");
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>