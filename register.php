<?php
session_start();
$conn = new mysqli("localhost", "root", "", "job_portal");

$errors = array();

$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];

    
    if (empty($name)) {
        $errors['name'] = "Full name is required";
    }

    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors['username'] = "Username must be at least 4 characters";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Check if username/email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['general'] = "Username or email already exists";
        }
        $stmt->close();
    }

    // Insert new user if no errors
    if (empty($errors)) {

        $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("sssss", $name, $username, $email, $password, $role);

        
        if ($stmt->execute()) {
            $success = "Registration successful! Please login.";
            header("refresh:2;url=login.php");
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="register-container">
        <h1>Create an Account</h1>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="error-message"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php else: ?>
            <form action="register.php" method="POST" id="registerForm">
                <div class="form-group">
                    <label for="role">I am a:</label>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="employer">Employer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="name">Name:</label>
                      <input type="text" id="name" name="name" value="" required>
                    <?php if (!empty($errors['name'])): ?>
                        <span class="error"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="" required>
                    <?php if (!empty($errors['username'])): ?>
                        <span class="error"><?php echo $errors['username']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit">Register</button>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Client-side validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            
            if (password !== confirm_password) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters!');
            }
        });
    </script>
</body>
</html>