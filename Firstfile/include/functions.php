<?php
/**
 * Common functions used throughout the website
 */

/**
 * Sanitize user input
 * 
 * @param string $data The input to sanitize
 * @return string Sanitized input
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to a specific page
 * 
 * @param string $location The URL to redirect to
 */
function redirect($location) {
    header("Location: $location");
    exit;
}

/**
 * Display error message
 * 
 * @param string $message The error message
 */
function displayError($message) {
    return "<div class='error-message'>$message</div>";
}

/**
 * Display success message
 * 
 * @param string $message The success message
 */
function displaySuccess($message) {
    return "<div class='success-message'>$message</div>";
}

/**
 * Get cart items count
 * 
 * @return int Number of items in cart
 */
function getCartCount() {
    if (isset($_SESSION['cart'])) {
        $count = 0;
        foreach ($_SESSION['cart'] as $items) {
            $count += count($items);
        }
        return $count;
    }
    return 0;
}

/**
 * Format price with currency symbol
 * 
 * @param float $price The price to format
 * @return string Formatted price
 */
function formatPrice($price) {
    // Changed from $ to Rs
    return 'Rs' . number_format($price, 2);
}
?>
