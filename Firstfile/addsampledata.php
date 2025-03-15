<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Connect to database
$database = new Database();
$db = $database->connect();

// Sample fresh products data
$freshProducts = [
    [
        'name' => 'Organic Avocados',
        'description' => 'Locally sourced ripe avocados, perfect for guacamole or toast.',
        'price' => 1.99,
        'unit' => 'each',
        'image_path' => 'avocado.jpg',
        'is_available' => 1
    ],
    [
        'name' => 'Baby Spinach',
        'description' => 'Fresh organic baby spinach leaves, great for salads and smoothies.',
        'price' => 3.99,
        'unit' => 'bunch',
        'image_path' => 'spinach.jpg',
        'is_available' => 1
    ],
    [
        'name' => 'Cherry Tomatoes',
        'description' => 'Sweet and juicy cherry tomatoes, perfect for salads and snacking.',
        'price' => 2.99,
        'unit' => 'pint',
        'image_path' => 'cherry_tomatoes.jpg',
        'is_available' => 1
    ],
    [
        'name' => 'Organic Quinoa',
        'description' => 'High-protein ancient grain, versatile for many dishes.',
        'price' => 5.99,
        'unit' => 'lb',
        'image_path' => 'quinoa.jpg',
        'is_available' => 1
    ],
    [
        'name' => 'Fresh Herbs Mix',
        'description' => 'Mix of basil, cilantro, and parsley to enhance any dish.',
        'price' => 4.99,
        'unit' => 'bunch',
        'image_path' => 'herbs.jpg',
        'is_available' => 1
    ]
];

// Add sample data to the database
$successCount = 0;
$errorMessages = [];

try {
    // First check if table exists
    $stmt = $db->query("SHOW TABLES LIKE 'fresh_products'");
    if ($stmt->rowCount() == 0) {
        // Create the table if it doesn't exist
        $db->exec("CREATE TABLE IF NOT EXISTS fresh_products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            unit VARCHAR(20) NOT NULL,
            image_path VARCHAR(255),
            is_available BOOLEAN DEFAULT TRUE
        )");
        echo "<p>Created fresh_products table.</p>";
    }
    
    // Insert sample data
    foreach ($freshProducts as $product) {
        // Check if product already exists
        $stmt = $db->prepare("SELECT * FROM fresh_products WHERE name = :name");
        $stmt->bindParam(':name', $product['name']);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // Product doesn't exist, insert it
            $query = "INSERT INTO fresh_products (name, description, price, unit, image_path, is_available) 
                      VALUES (:name, :description, :price, :unit, :image_path, :is_available)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $product['name']);
            $stmt->bindParam(':description', $product['description']);
            $stmt->bindParam(':price', $product['price']);
            $stmt->bindParam(':unit', $product['unit']);
            $stmt->bindParam(':image_path', $product['image_path']);
            $stmt->bindParam(':is_available', $product['is_available']);
            
            if ($stmt->execute()) {
                $successCount++;
            } else {
                $errorMessages[] = "Failed to add {$product['name']}";
            }
        } else {
            $errorMessages[] = "{$product['name']} already exists in the database";
        }
    }
    
    // Set session message
    if ($successCount > 0) {
        $_SESSION['success_message'] = "Successfully added $successCount sample products to the database!";
    } else {
        $_SESSION['error_message'] = "No new products were added. " . implode(", ", $errorMessages);
    }
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
}

// Redirect back to fresh products page
redirect('fresh-products.php');
?>