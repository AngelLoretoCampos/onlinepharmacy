<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Set logout notification
$_SESSION['notification'] = 'You have been logged out successfully!';

header("Location: login.php");
?>
