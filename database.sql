-- Create database
CREATE DATABASE IF NOT EXISTS ecodish;
USE ecodish;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('appetizer', 'main', 'dessert', 'beverage') NOT NULL,
    image_path VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE
);

-- Fresh products table
CREATE TABLE IF NOT EXISTS fresh_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(20) NOT NULL, -- e.g., kg, bunch, piece
    image_path VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    item_type ENUM('menu', 'fresh') NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert sample menu items
INSERT INTO menu_items (name, description, price, category, image_path) VALUES
('Avocado Toast', 'Smashed avocado on sourdough bread with cherry tomatoes and microgreens', 8.99, 'appetizer', 'avocado_toast.jpg'),
('Buddha Bowl', 'Quinoa, roasted vegetables, chickpeas, and tahini dressing', 12.99, 'main', 'buddha_bowl.jpg'),
('Mushroom Risotto', 'Creamy arborio rice with wild mushrooms and truffle oil', 14.99, 'main', 'mushroom_risotto.jpg'),
('Chocolate Avocado Mousse', 'Rich chocolate dessert made with ripe avocados and raw cacao', 6.99, 'dessert', 'chocolate_mousse.jpg'),
('Green Smoothie', 'Spinach, banana, mango, and coconut water', 5.99, 'beverage', 'green_smoothie.jpg');

-- Insert sample fresh products
INSERT INTO fresh_products (name, description, price, unit, image_path) VALUES
('Organic Avocados', 'Locally sourced ripe avocados', 1.99, 'each', 'avocado.jpg'),
('Baby Spinach', 'Fresh organic baby spinach leaves', 3.99, 'bunch', 'spinach.jpg'),
('Cherry Tomatoes', 'Sweet and juicy cherry tomatoes', 2.99, 'pint', 'cherry_tomatoes.jpg'),
('Organic Quinoa', 'High-protein ancient grain', 5.99, 'lb', 'quinoa.jpg'),
('Fresh Herbs Mix', 'Mix of basil, cilantro, and parsley', 4.99, 'bunch', 'herbs.jpg');