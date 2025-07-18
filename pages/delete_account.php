<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';


// Require login
requireLogin();

// Only allow POST requests
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ' . SITE_URL . '/pages/profile.php');
    exit;
}

// Delete user account
if($user->deleteUser($_SESSION['user_id'])) {
    // Logout and destroy session
    session_start();
    session_unset();
    session_destroy();
    
    // Redirect to home page with success message
    flash('register_success', 'Your account has been deleted. We hope to see you again!');
    header('Location: ' . SITE_URL . '/index.php');
    exit;
} else {
    flash('profile_message', 'Something went wrong. Please try again.', 'alert alert-danger');
    header('Location: ' . SITE_URL . '/pages/profile.php');
    exit;
}
?>