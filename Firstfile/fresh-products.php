<?php
$pageTitle = 'Fresh Products';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Connect to database
$database = new Database();
$db = $database->connect();
?>

<h1 class="page-title">Fresh Products</h1>

<section class="fresh-products-section">
    <div class="container">
        <p style="text-align: center; margin-bottom: 30px;">
            We partner with local farmers to bring you the freshest organic produce. 
            All items are harvested within 24 hours of delivery.
        </p>
        
        <?php
        // Get all fresh products
        $query = "SELECT * FROM fresh_products WHERE is_available = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo '<div class="grid">';
            
            while ($product = $stmt->fetch()) {
                $imagePath = !empty($product['image_path']) ? 'assets/images/' . $product['image_path'] : '/placeholder.svg?height=200&width=300';
                ?>
                <div class="card">
                    <div class="card-image" style="background-image: url('<?php echo $imagePath; ?>')"></div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <div class="card-price">
                            <?php echo formatPrice($product['price']); ?> per <?php echo $product['unit']; ?>
                        </div>
                        <button class="btn add-to-cart" data-id="<?php echo $product['id']; ?>" data-type="fresh">Add to Cart</button>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
        } else {
            echo '<p>No fresh products available at the moment.</p>';
        }
        ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>