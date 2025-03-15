<?php
$pageTitle = 'Register';
require_once 'includes/header.php';
require_once 'includes/db.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$username = $email = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        $database = new Database();
        $db = $database->connect();
        
        $query = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if ($user['username'] === $username) {
                $errors[] = 'Username already exists';
            }
            if ($user['email'] === $email) {
                $errors[] = 'Email already exists';
            }
        }
    }
    
    // Register user if no errors
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        if ($stmt->execute()) {
            // Set success message and redirect to login
            $_SESSION['success_message'] = 'Registration successful! You can now log in.';
            redirect('login.php');
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

<div class="form-container">
    <h2 class="page-title">Create an Account</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" data-validate>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <small>Password must be at least 6 characters long</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Register</button>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        Already have an account? <a href="login.php">Log in</a>
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>