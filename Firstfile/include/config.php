<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Change to your MySQL username
define('DB_PASS', '');         // Change to your MySQL password
define('DB_NAME', 'ecodish');

// Website settings
define('SITE_NAME', 'EcoDish');
define('SITE_URL', 'http://localhost/ecodish'); // Change to your actual URL

// Session settings
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>