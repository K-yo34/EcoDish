<?php
$pageTitle = 'Shopping Cart';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'menu' => [],
        'fresh' => []
    ];
}

// Connect to database
$database = new Database();
$db = $database->connect();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $itemType = isset($_POST['item_type']) ? $_POST['item_type'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate item type
    if ($itemType !== 'menu' && $itemType !== 'fresh') {
        echo json_encode(['success' => false, 'message' => 'Invalid item type']);
        exit;
    }
    
    // Add item to cart
    if ($action === 'add') {
        // Check if item already exists in cart
        if (isset($_SESSION['cart'][$itemType][$itemId])) {
            $_SESSION['cart'][$itemType][$itemId]['quantity'] += $quantity;
        } else {
            // Get item details from database
            $table = $itemType === 'menu' ? 'menu_items' : 'fresh_products';
            $query = "SELECT * FROM $table WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $itemId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $item = $stmt->fetch();
                $_SESSION['cart'][$itemType][$itemId] = [
                    'id' => $itemId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $quantity
                ];
                
                // Add unit for fresh products
                if ($itemType === 'fresh') {
                    $_SESSION['cart'][$itemType][$itemId]['unit'] = $item['unit'];
                }
            }
        }
        
        // Return success response
        echo json_encode([
            'success' => true,
            'count' => getCartCount()
        ]);
        exit;
    }
    
    // Update item quantity
    if ($action === 'update' && isset($_SESSION['cart'][$itemType][$itemId])) {
        $_SESSION['cart'][$itemType][$itemId]['quantity'] = $quantity;
        
        // Calculate subtotal and total
        $subtotal = $_SESSION['cart'][$itemType][$itemId]['price'] * $quantity;
        $total = 0;
        
        foreach ($_SESSION['cart'] as $type => $items) {
            foreach ($items as $id => $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }
        
        // Return success response
        echo json_encode([
            'success' => true,
            'subtotal' => formatPrice($subtotal),
            'total' => formatPrice($total),
            'count' => getCartCount()
        ]);
        exit;
    }
    
    // Remove item from cart
    if ($action === 'remove' && isset($_SESSION['cart'][$itemType][$itemId])) {
        unset($_SESSION['cart'][$itemType][$itemId]);
        
        // Redirect to cart page
        redirect('cart.php');
    }
}

// Calculate cart total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $type => $items) {
    foreach ($items as $id => $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
}

// Check if cart is empty
$isCartEmpty = true;
foreach ($_SESSION['cart'] as $type => $items) {
    if (!empty($items)) {
        $isCartEmpty = false;
        break;
    }
}
?>

<h1 class="page-title">Your Shopping Cart</h1>

<div class="container">
    <?php if ($isCartEmpty): ?>
        <div style="text-align: center; margin: 50px 0;">
            <p>Your cart is empty.</p>
            <a href="menu.php" class="btn" style="margin-top: 20px;">Browse Menu</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <!-- Menu items in cart -->
            <?php if (!empty($_SESSION['cart']['menu'])): ?>
                <h2 style="margin-bottom: 20px;">Menu Items</h2>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart']['menu'] as $id => $item): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td>
                                    <div class="quantity-control">
                                        <button class="quantity-btn" data-action="decrease" data-id="<?php echo $id; ?>" data-type="menu">-</button>
                                        <input type="number" name="quantity[menu][<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="99" readonly>
                                        <button class="quantity-btn" data-action="increase" data-id="<?php echo $id; ?>" data-type="menu">+</button>
                                    </div>
                                </td>
                                <td id="subtotal-menu-<?php echo $id; ?>"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="item_type" value="menu">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-secondary">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <!-- Fresh products in cart -->
            <?php if (!empty($_SESSION['cart']['fresh'])): ?>
                <h2 style="margin: 30px 0 20px;">Fresh Products</h2>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart']['fresh'] as $id => $item): ?>
                            <tr>
                                <td><?php echo $item['name']; ?> (<?php echo $item['unit']; ?>)</td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td>
                                    <div class="quantity-control">
                                        <button class="quantity-btn" data-action="decrease" data-id="<?php echo $id; ?>" data-type="fresh">-</button>
                                        <input type="number" name="quantity[fresh][<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="99" readonly>
                                        <button class="quantity-btn" data-action="increase" data-id="<?php echo $id; ?>" data-type="fresh">+</button>
                                    </div>
                                </td>
                                <td id="subtotal-fresh-<?php echo $id; ?>"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="item_type" value="fresh">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-secondary">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <!-- Cart total and checkout button -->
            <div class="cart-total">
                Total: <span id="cart-total"><?php echo formatPrice($cartTotal); ?></span>
            </div>
            
            <div style="text-align: right;">
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>