<?php
// Start session if not already started
session_start();

// Clear all faculty-specific session variables
if(isset($_SESSION['faculty_logged_in'])) {
    unset($_SESSION['faculty_logged_in']);
}
if(isset($_SESSION['faculty_id'])) {
    unset($_SESSION['faculty_id']);
}
if(isset($_SESSION['first_name'])) {
    unset($_SESSION['first_name']);
}
if(isset($_SESSION['last_name'])) {
    unset($_SESSION['last_name']);
}
if(isset($_SESSION['email'])) {
    unset($_SESSION['email']);
}
if(isset($_SESSION['role_id'])) {
    unset($_SESSION['role_id']);
}

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>
