<?php
session_start();

// Redirect if already logged in
//if (isset($_SESSION['user_id'])) {
  //  header("Location: dashboard.php");
//    exit();
//}

$conn = new mysqli("localhost", "root", "", "job_portal");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate credentials
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (plain text comparison - UNSAFE, needs hashing)
        if ($password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'student':
                    header("Location: student_dashboard.php");
                    break;
                case 'employer':
                    header("Location: employer.php");
                    break;
                default:
                    header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login to Job Portal</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="forgot_password.php">Forgot password?</a></p>
            </div>
        </form>
    </div>

    <script>
        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields!');
            }
        });
    </script>
</body>
</html>