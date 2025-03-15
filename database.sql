-- EcoDish Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS ecodish;
USE ecodish;

-- Table structure for table `users`

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `menu_items`

CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `category` ENUM('appetizer', 'main', 'dessert', 'beverage') NOT NULL,
    `image_path` VARCHAR(255),
    `is_available` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `fresh_products`

CREATE TABLE IF NOT EXISTS `fresh_products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `unit` VARCHAR(20) NOT NULL, -- e.g., kg, bunch, piece
    `image_path` VARCHAR(255),
    `is_available` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `orders`

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `order_items`

CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `item_type` ENUM('menu', 'fresh') NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for table `menu_items` (prices in rupees)

INSERT INTO `menu_items` (`name`, `description`, `price`, `category`, `image_path`, `is_available`) VALUES
('Avocado Toast', 'Smashed avocado on sourdough bread with cherry tomatoes and microgreens', 699.00, 'appetizer', 'avocado_toast.jpg', 1),
('Buddha Bowl', 'Quinoa, roasted vegetables, chickpeas, and tahini dressing', 899.00, 'main', 'buddha_bowl.jpg', 1),
('Mushroom Risotto', 'Creamy arborio rice with wild mushrooms and truffle oil', 1099.00, 'main', 'mushroom_risotto.jpg', 1),
('Chocolate Avocado Mousse', 'Rich chocolate dessert made with ripe avocados and raw cacao', 499.00, 'dessert', 'chocolate_mousse.jpg', 1),
('Green Smoothie', 'Spinach, banana, mango, and coconut water', 399.00, 'beverage', 'green_smoothie.jpg', 1),
('Vegan Burger', 'Plant-based patty with lettuce, tomato, and vegan mayo on a whole grain bun', 799.00, 'main', 'vegan_burger.jpg', 1),
('Coconut Curry', 'Vegetables in a rich coconut curry sauce served with brown rice', 899.00, 'main', 'coconut_curry.jpg', 1),
('Fruit Parfait', 'Layers of seasonal fruits, coconut yogurt, and granola', 599.00, 'dessert', 'fruit_parfait.jpg', 1),
('Masala Chai', 'Traditional Indian spiced tea with plant milk', 299.00, 'beverage', 'masala_chai.jpg', 1),
('Samosa Chaat', 'Crispy samosas topped with chickpeas, chutneys, and spices', 599.00, 'appetizer', 'samosa_chaat.jpg', 1),
('Falafel Wrap', 'Homemade falafel with tahini sauce and fresh vegetables', 699.00, 'main', 'falafel_wrap.jpg', 1),
('Mango Lassi', 'Refreshing yogurt drink with fresh mango and cardamom', 349.00, 'beverage', 'mango_lassi.jpg', 1);

-- Sample data for table `fresh_products` (prices in rupees)

INSERT INTO `fresh_products` (`name`, `description`, `price`, `unit`, `image_path`, `is_available`) VALUES
('Organic Avocados', 'Locally sourced ripe avocados', 149.00, 'each', 'avocado.jpg', 1),
('Baby Spinach', 'Fresh organic baby spinach leaves', 299.00, 'bunch', 'spinach.jpg', 1),
('Cherry Tomatoes', 'Sweet and juicy cherry tomatoes', 199.00, 'pint', 'cherry_tomatoes.jpg', 1),
('Organic Quinoa', 'High-protein ancient grain', 449.00, 'kg', 'quinoa.jpg', 1),
('Fresh Herbs Mix', 'Mix of basil, cilantro, and parsley', 349.00, 'bunch', 'herbs.jpg', 1),
('Organic Potatoes', 'Farm-fresh potatoes, perfect for roasting or mashing', 99.00, 'kg', 'potatoes.jpg', 1),
('Red Onions', 'Sweet red onions, great for salads and cooking', 79.00, 'kg', 'onions.jpg', 1),
('Organic Carrots', 'Fresh and crunchy carrots', 89.00, 'kg', 'carrots.jpg', 1),
('Bell Peppers', 'Colorful bell peppers, perfect for stir-fries', 199.00, 'kg', 'bell_peppers.jpg', 1),
('Organic Bananas', 'Sweet and ripe bananas', 99.00, 'dozen', 'bananas.jpg', 1),
('Coconut Milk', 'Rich and creamy coconut milk', 249.00, 'liter', 'coconut_milk.jpg', 1),
('Organic Brown Rice', 'Nutritious whole grain rice', 199.00, 'kg', 'brown_rice.jpg', 1),
('Organic Chickpeas', 'Protein-rich legumes', 179.00, 'kg', 'chickpeas.jpg', 1),
('Organic Tofu', 'Firm tofu, perfect for stir-fries and curries', 249.00, 'kg', 'tofu.jpg', 1);

-- --------------------------------------------------------
-- End of SQL Script
-- --------------------------------------------------------
