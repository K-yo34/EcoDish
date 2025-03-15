<?php
$pageTitle = 'Login';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$username = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // Authenticate user
    if (empty($errors)) {
        $database = new Database();
        $db = $database->connect();
        
        $query = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username); // Allow login with email too
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to home page
                $_SESSION['success_message'] = 'You are now logged in!';
                redirect('index.php');
            } else {
                $errors[] = 'Invalid password';
            }
        } else {
            $errors[] = 'User not found';
        }
    }
}
?>

<div class="form-container">
    <h2 class="page-title">Login to Your Account</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" data-validate>
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        Don't have an account? <a href="register.php">Register</a>
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>