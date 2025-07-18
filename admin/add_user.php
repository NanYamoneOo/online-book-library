<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

// Require admin access
requireAdmin();

$page_title = 'Add New User';
$current_page = 'manage_users';

// Initialize variables
$username = $email = $role = '';
$errors = [];

// Process form submission (same as before)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $role = trim($_POST['role'] ?? 'user');

    // Validate inputs
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    // If no errors, create the user
    if (empty($errors)) {
        try {
            // Check if username/email already exists
            if ($user->findUserByUsername($username)) {
                $errors['username'] = 'Username already taken';
            } elseif ($user->findUserByEmail($email)) {
                $errors['email'] = 'Email already registered';
            } else {
                // Create user
                $user_data = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role' => $role
                ];

                if ($user->register($user_data)) {
                    flash('user_message', 'User created successfully!', 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4');
                    header('Location: manage_users.php');
                    exit;
                } else {
                    $errors['general'] = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Error creating user. Please try again.</div>';
                }
            }
        } catch (Exception $e) {
            $errors['general'] = 'Error: ' . $e->getMessage();
        }
    }
}


require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header with decorative elements -->
        <div class="text-center mb-12 relative">
            <div class="absolute -top-6 -left-6 w-16 h-16 rounded-full bg-indigo-200 opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full bg-blue-200 opacity-30 animate-pulse"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold text-gray-800 mb-3 tracking-tight">Create New User</h1>
                <p class="text-lg text-gray-600 max-w-md mx-auto">Fill in the details below to add a new user to the system</p>
            </div>
        </div>

        <!-- Main form card with shadow and animation -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <!-- Decorative header strip -->
            <div class="bg-gradient-to-r from-indigo-500 to-blue-600 h-2"></div>
            
            <div class="p-8 sm:p-10">
                <?php if (!empty($errors['general'])): ?>
                    <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($errors['general']); ?></h3>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" method="POST">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Username Field -->
                        <div class="sm:col-span-2">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Username
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($username); ?>" required
                                       class="form-input block w-full px-4 py-3 border <?php echo isset($errors['username']) ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'; ?> rounded-lg transition duration-150 ease-in-out"
                                       placeholder="e.g. john_doe">
                                <?php if (isset($errors['username'])): ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo htmlspecialchars($errors['username']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                Email Address
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input id="email" name="email" type="email" autocomplete="email" value="<?php echo htmlspecialchars($email); ?>" required
                                       class="form-input block w-full px-4 py-3 border <?php echo isset($errors['email']) ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'; ?> rounded-lg transition duration-150 ease-in-out"
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
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Password
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input id="password" name="password" type="password" autocomplete="new-password" required
                                       class="form-input block w-full px-4 py-3 border <?php echo isset($errors['password']) ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'; ?> rounded-lg transition duration-150 ease-in-out"
                                       placeholder="••••••••">
                                <?php if (isset($errors['password'])): ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo htmlspecialchars($errors['password']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Confirm Password
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                                       class="form-input block w-full px-4 py-3 border <?php echo isset($errors['confirm_password']) ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'; ?> rounded-lg transition duration-150 ease-in-out"
                                       placeholder="••••••••">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo htmlspecialchars($errors['confirm_password']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Role Selector -->
                        <div class="sm:col-span-2">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-4 w-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                User Role
                            </label>
                            <div class="mt-1">
                                <select id="role" name="role" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                                    <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>Regular User</option>
                                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-4 pt-8 border-t border-gray-200">
                        <a href="manage_users.php" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                            Back to Users
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Decorative footer -->
        <div class="mt-12 text-center text-sm text-gray-500">
            <p>Need help? <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 transition duration-150 ease-in-out">Contact support</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>