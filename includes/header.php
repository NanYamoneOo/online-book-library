<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | <?php echo $page_title ?? 'Welcome'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            font-family: 'Inter', sans-serif;
            border-radius: 0.5rem;
        }
        
        /* Smooth dropdown transitions */
        /* .dropdown-enter {
            transition: all 0.2s ease-out;
        }
        .dropdown-enter-from {
            opacity: 0;
            transform: translateY(-10px);
        }
        .dropdown-enter-to {
            opacity: 1;
            transform: translateY(0);
        }
        .dropdown-leave {
            transition: all 0.15s ease-in;
        }
        .dropdown-leave-from {
            opacity: 1;
            transform: translateY(0);
        }
        .dropdown-leave-to {
            opacity: 0;
            transform: translateY(-10px);
        } */

        /* Custom dropdown transitions */
        .dropdown-menu {
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
        }
        
        .dropdown:hover .dropdown-menu,
        .dropdown:focus-within .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        /* Smooth chevron rotation */
        .dropdown-chevron {
            transition: transform 0.3s ease;
        }
        
        .dropdown:hover .dropdown-chevron,
        .dropdown:focus-within .dropdown-chevron {
            transform: rotate(180deg);
        }

    </style>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        dark: '#1E293B',
                        light: '#F8FAFC',
                        accent: '#F59E0B'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Merriweather', 'serif']
                    }
                }
            }
        }
    </script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <!-- Alpine.js for dropdown interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-dark text-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-between py-4">
                <a href="<?php echo SITE_URL; ?>/index.php" class="flex items-center space-x-2 hover:opacity-90 transition-opacity">
                    <i class="fas fa-book-open text-2xl text-accent"></i>
                    <span class="text-2xl font-bold"><?php echo SITE_NAME; ?></span>
                </a>
                
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="nav-link hover:text-accent transition-colors">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/search.php" class="nav-link hover:text-accent transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </a>
                    <?php if(isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/my_books.php" class="nav-link hover:text-accent transition-colors">
                        <i class="fas fa-bookmark mr-2"></i>My Books
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if(isLoggedIn()): ?>
                        <?php if(isAdmin()): ?>
                            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="px-3 py-1 rounded-md bg-primary bg-opacity-20 hover:bg-opacity-30 transition-all flex items-center">
                                <i class="fas fa-cog mr-2"></i>Admin
                            </a>
                        <?php endif; ?>
                        
                        <!-- Improved Dropdown -->
                        <div class="dropdown relative">
                            <button class="flex items-center space-x-2 focus:outline-none group"
                                    aria-haspopup="true" aria-expanded="false">
                                <span class="font-medium hover:text-accent transition-colors">
                                    <?php echo htmlspecialchars($_SESSION['user_username']); ?>
                                </span>
                                <i class="fas fa-chevron-down text-sm dropdown-chevron"></i>
                            </button>
                            
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                                <a href="<?php echo SITE_URL; ?>/pages/profile.php" 
                                   class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-500 w-5 text-center"></i>Profile
                                </a>
                                <a href="<?php echo SITE_URL; ?>/pages/logout.php" 
                                   class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2 text-gray-500 w-5 text-center"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/pages/login.php" class="nav-link hover:text-accent transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn-primary hover:opacity-90 transition-opacity">
                            Register
                        </a>
                    <?php endif; ?>
                    
                    <button class="md:hidden focus:outline-none text-white hover:text-accent transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        <?php flash('register_success'); ?>
        <?php flash('login_success'); ?>
        <?php flash('login_error'); ?>
        <?php flash('book_message'); ?>
        <?php flash('profile_message'); ?>

    
</body>
</html>