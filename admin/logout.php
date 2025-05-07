<?php
// Start session if not already started
session_start();

// Clear all admin-specific session variables
if(isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
}
if(isset($_SESSION['admin_id'])) {
    unset($_SESSION['admin_id']);
}
if(isset($_SESSION['admin_name'])) {
    unset($_SESSION['admin_name']);
}
if(isset($_SESSION['admin_email'])) {
    unset($_SESSION['admin_email']);
}
if(isset($_SESSION['admin_role'])) {
    unset($_SESSION['admin_role']);
}

// Destroy the session
session_destroy();

// Redirect to the admin login page
header("Location: login.php");
exit();
?>
