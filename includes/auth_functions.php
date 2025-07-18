<?php
// Redirect if not logged in
function isLoggedIn() {
    if(isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

// Redirect if not admin
function isAdmin() {
    if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
        return true;
    } else {
        return false;
    }
}

// Redirect to login page if not logged in
function requireLogin() {
    if(!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/pages/login.php');
        exit;
    }
}

// Redirect to home page if logged in
function redirectIfLoggedIn() {
    if(isLoggedIn()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

// Redirect to home page if not admin
function requireAdmin() {
    requireLogin();
    if(!isAdmin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

// Flash message helper
function flash($name = '', $message = '', $class = '') {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif(empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : 'bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4';
            echo '<div class="'.$class.' mb-4 flex items-start" role="alert">';
            
            // Add appropriate icon based on message type
            if (strpos($class, 'green') !== false) {
                echo '<svg class="h-6 w-6 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>';
            } elseif (strpos($class, 'red') !== false || strpos($class, 'yellow') !== false) {
                echo '<svg class="h-6 w-6 text-red-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>';
            } else {
                echo '<svg class="h-6 w-6 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>';
            }
            
            echo '<div><p class="font-medium">'.htmlspecialchars($_SESSION[$name]).'</p></div>';
            echo '</div>';
            
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}


?>