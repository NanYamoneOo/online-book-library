<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

redirectIfLoggedIn();

$page_title = 'Register';
$current_page = 'register';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST = filter_input_array(INPUT_POST, [
        'username' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'email' => FILTER_SANITIZE_EMAIL,
        'password' => FILTER_UNSAFE_RAW,
        'confirm_password' => FILTER_UNSAFE_RAW
    ]);

    $data = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password'])
    ];

    if(empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['confirm_password'])) {
        flash('register_error', 'Please fill in all fields', 'alert alert-danger');
    } elseif($data['password'] != $data['confirm_password']) {
        flash('register_error', 'Passwords do not match', 'alert alert-danger');
    } elseif(strlen($data['password']) < 6) {
        flash('register_error', 'Password must be at least 6 characters', 'alert alert-danger');
    } else {
        if($user->findUserByUsername($data['username'])) {
            flash('register_error', 'Username already taken', 'alert alert-danger');
        } elseif($user->findUserByEmail($data['email'])) {
            flash('register_error', 'Email already registered', 'alert alert-danger');
        } else {
            if($user->register($data)) {
                flash('register_success', 'Registration successful. Please login.');
                header('Location: ' . SITE_URL . '/pages/login.php');
                exit;
            } else {
                flash('register_error', 'Something went wrong. Please try again.', 'alert alert-danger');
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header with decorative gradient -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 py-6 px-8 text-center">
                <h1 class="text-2xl font-bold text-white">Create Account</h1>
                <p class="text-indigo-100 mt-1">Join our reading community</p>
            </div>
            
            <!-- Flash messages -->
            <div class="px-8 pt-6">
                <?php flash('register_error'); ?>
                <?php flash('register_success'); ?>
            </div>
            
            <!-- Registration form -->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-8 space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required
                               class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                               placeholder="Choose a username">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                               class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                               placeholder="your@email.com">
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                               placeholder="At least 6 characters">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-gray-400"></i>
                        </div>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                               placeholder="Confirm your password">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a>
                    </label>
                </div>
                
                <div>
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-user-plus mr-2"></i> Create Account
                    </button>
                </div>
            </form>
            
            <div class="px-8 pb-8 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="<?php echo SITE_URL; ?>/pages/login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>