<?php
$pageTitle = 'Menu';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Connect to database
$database = new Database();
$db = $database->connect();

// Get menu categories
$categories = ['appetizer', 'main', 'dessert', 'beverage'];
?>

<h1 class="page-title">Our Menu</h1>

<?php foreach ($categories as $category): ?>
    <section class="menu-section">
        <div class="container">
            <h2 style="margin-bottom: 20px; color: #4a8f3d; text-transform: capitalize;">
                <?php echo $category; ?>s
            </h2>
            
            <?php
            // Get menu items by category
            $query = "SELECT * FROM menu_items WHERE category = :category AND is_available = 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':category', $category);
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
                echo '<p>No ' . $category . 's available at the moment.</p>';
            }
            ?>
        </div>
    </section>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>