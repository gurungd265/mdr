<?php
// auth_check.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

// Role-based access control
$current_file = basename($_SERVER['PHP_SELF']); // Get the current file name

// Restrict access to admin-only pages for non-admin users
if ($current_file == 'admin.php' && !isset($_SESSION['admin_id'])) {
    header('Location: home.php');
    exit();
}

// Restrict access to user-only pages for admin users
if ($current_file == 'home.php' && isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit();
}
?>