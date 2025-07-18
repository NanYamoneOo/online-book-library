<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

// Redirect if already logged in
redirectIfLoggedIn();

$page_title = 'Login';
$current_page = 'login';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST = filter_input_array(INPUT_POST, [
        'username' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'password' => FILTER_UNSAFE_RAW
    ]);

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if(empty($username) || empty($password)) {
        flash('login_error', 'Please fill in all fields', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
    } else {
        $loggedInUser = $user->login($username, $password);

        if($loggedInUser) {
            $_SESSION['user_id'] = $loggedInUser->id;
            $_SESSION['user_username'] = $loggedInUser->username;
            $_SESSION['user_email'] = $loggedInUser->email;
            $_SESSION['user_role'] = $loggedInUser->role;
            
            flash('login_success', 'You are now logged in', 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative');
            header('Location: ' . SITE_URL . '/index.php');
            exit;
        } else {
            flash('login_error', 'Invalid username or password', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 py-6 px-8 text-center">
                <h1 class="text-2xl font-bold text-white">Welcome Back</h1>
                <p class="text-indigo-100 mt-1">Sign in to your account</p>
            </div>
            
            <!-- Flash messages -->
            <div class="px-8 pt-6 space-y-3">
                <?php Flash('login_error'); ?>
                <?php Flash('login_success'); ?>
            </div>
            
            <!-- Login form -->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="p-8 space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required
                               class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                               placeholder="Enter your username">
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
                               placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Forgot password?
                        </a>
                    </div>
                </div>
                
                <div>
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                    </button>
                </div>
            </form>
            
            <div class="px-8 pb-8 text-center">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="<?php echo SITE_URL; ?>/pages/register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Register here
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
