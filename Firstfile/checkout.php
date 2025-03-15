<?php
$pageTitle = 'Checkout';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Redirect if cart is empty
$isCartEmpty = true;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $type => $items) {
        if (!empty($items)) {
            $isCartEmpty = false;
            break;
        }
    }
}

if ($isCartEmpty) {
    redirect('cart.php');
}

// Redirect if not logged in
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'Please log in to complete your order.';
    redirect('login.php');
}

// Connect to database
$database = new Database();
$db = $database->connect();

// Get user information
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch();

// Calculate cart total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $type => $items) {
    foreach ($items as $id => $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
}

// Process checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create order in database
    try {
        // Start transaction
        $db->beginTransaction();
        
        // Insert order
        $query = "INSERT INTO orders (user_id, total_amount, status) VALUES (:user_id, :total_amount, 'confirmed')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':total_amount', $cartTotal);
        $stmt->execute();
        
        // Get order ID
        $orderId = $db->lastInsertId();
        
        // Insert order items
        foreach ($_SESSION['cart'] as $type => $items) {
            foreach ($items as $id => $item) {
                $query = "INSERT INTO order_items (order_id, item_id, item_type, quantity, price) 
                          VALUES (:order_id, :item_id, :item_type, :quantity, :price)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':item_id', $id);
                $stmt->bindParam(':item_type', $type);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
            }
        }
        
        // Commit transaction
        $db->commit();
        
        // Clear cart
        $_SESSION['cart'] = [
            'menu' => [],
            'fresh' => []
        ];
        
        // Set success message
        $_SESSION['success_message'] = 'Your order has been placed successfully! Order #' . $orderId;
        
        // Redirect to home page
        redirect('index.php');
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        $_SESSION['error_message'] = 'An error occurred while processing your order. Please try again.';
    }
}
?>

<h1 class="page-title">Checkout</h1>

<div class="container">
    <div class="checkout-container" style="display: flex; flex-wrap: wrap; gap: 30px;">
        <!-- Order summary -->
        <div class="order-summary" style="flex: 1; min-width: 300px;">
            <h2 style="margin-bottom: 20px;">Order Summary</h2>
            
            <?php if (!empty($_SESSION['cart']['menu'])): ?>
                <h3 style="margin-bottom: 10px;">Menu Items</h3>
                <ul style="margin-bottom: 20px; list-style: none; padding: 0;">
                    <?php foreach ($_SESSION['cart']['menu'] as $id => $item): ?>
                        <li style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                            <span><?php echo $item['quantity']; ?> x <?php echo $item['name']; ?></span>
                            <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <?php if (!empty($_SESSION['cart']['fresh'])): ?>
                <h3 style="margin-bottom: 10px;">Fresh Products</h3>
                <ul style="margin-bottom: 20px; list-style: none; padding: 0;">
                    <?php foreach ($_SESSION['cart']['fresh'] as $id => $item): ?>
                        <li style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                            <span><?php echo $item['quantity']; ?> x <?php echo $item['name']; ?> (<?php echo $item['unit']; ?>)</span>
                            <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <div style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px; font-weight: bold; display: flex; justify-content: space-between;">
                <span>Total:</span>
                <span><?php echo formatPrice($cartTotal); ?></span>
            </div>
        </div>
        
        <!-- Checkout form -->
        <div class="checkout-form" style="flex: 1; min-width: 300px;">
            <h2 style="margin-bottom: 20px;">Delivery Information</h2>
            
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" data-validate>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $user['username']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="notes">Order Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2"></textarea>
                </div>
                
                <h3 style="margin: 20px 0;">Payment Information</h3>
                <p style="margin-bottom: 20px; color: #666;">
                    Note: This is a demo website. No actual payment will be processed.
                </p>
                
                <div class="form-group">
                    <label for="card_name">Name on Card</label>
                    <input type="text" id="card_name" name="card_name" required>
                </div>
                
                <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" required>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="expiry">Expiry Date</label>
                        <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="XXX" required>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>