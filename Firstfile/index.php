<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h2>Welcome to EcoDish</h2>
        <p>Delicious, sustainable vegan cuisine made with love for you and our planet.</p>
        <a href="menu.php" class="btn">View Our Menu</a>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="page-title">Why Choose EcoDish?</h2>
        <div class="grid">
            <div class="card">
                <div class="card-content">
                    <h3 class="card-title">100% Plant-Based</h3>
                    <p>All our dishes are made from plant-based ingredients, ensuring a cruelty-free dining experience.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <h3 class="card-title">Locally Sourced</h3>
                    <p>We partner with local farmers to bring you the freshest seasonal produce with minimal carbon footprint.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <h3 class="card-title">Eco-Friendly Packaging</h3>
                    <p>All our takeaway containers and utensils are compostable and made from sustainable materials.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="featured-menu">
    <div class="container">
        <h2 class="page-title">Featured Menu Items</h2>
        
        <?php
        // Connect to database
        require_once 'includes/db.php';
        $database = new Database();
        $db = $database->connect();

        // Get featured menu items (limit to 3)
        $query = "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY RAND() LIMIT 3";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo '<div class="grid">';
            
            while ($item = $stmt->fetch()) {
                $imagePath = !empty($item['image_path']) ? 'assets/images/' . $item['image_path'] : '/placeholder.svg?height=200&width=300';
                ?>
                <div class="card">
                    <div class="card-image" style="background-image: url('<?php echo $imagePath; ?>')"></div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $item['name']; ?></h3>
                        <p><?php echo $item['description']; ?></p>
                        <div class="card-price"><?php echo formatPrice($item['price']); ?></div>
                        <button class="btn add-to-cart" data-id="<?php echo $item['id']; ?>" data-type="menu">Add to Cart</button>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
        } else {
            echo '<p>No featured items available at the moment.</p>';
        }
        ?>
        
        <div class="text-center" style="margin-top: 30px; text-align: center;">
            <a href="menu.php" class="btn">View Full Menu</a>
        </div>
    </div>
</section>

<section class="fresh-products-preview">
    <div class="container">
        <h2 class="page-title">Fresh Products</h2>
        <p style="text-align: center; margin-bottom: 30px;">We also offer fresh, organic produce directly from local farms.</p>
        
        <div class="text-center" style="text-align: center;">
            <a href="fresh-products.php" class="btn">Shop Fresh Products</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>