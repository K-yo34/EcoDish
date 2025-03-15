<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
redirect('index.php');
?>