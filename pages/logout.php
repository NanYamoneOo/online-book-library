<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';


// Destroy session
session_start();
session_unset();
session_destroy();

// Redirect to home page
header('Location: ' . SITE_URL . '/index.php');
exit;
?>