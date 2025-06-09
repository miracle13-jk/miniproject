<?php
session_start();
$conn = new mysqli("localhost", "root", "", "job_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';
$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['cv'])) {
    $target_dir = "uploads/cvs/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["cv"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

   
    if ($_FILES["cv"]["size"] > 5000000) { // 5MB max
        $error = "File is too large (max 5MB)";
    } elseif ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        $error = "Only PDF, DOC & DOCX files are allowed";
    } elseif (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
        // Save application details in the database
        $stmt = $conn->prepare("INSERT INTO applications (job_id, student_id, cv_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $job_id, $_SESSION['user_id'], $target_file);
        
        if ($stmt->execute()) {
            $success = "Application submitted successfully!";
        } else {
            $error = "Database error. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Error uploading file.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Job</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Apply for Job</h1>

    <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="cv">Upload Your CV:</label>
        <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
        <button type="submit">Submit Application</button>
    </form>

    <a href="student_dashboard.php">Back to Dashboard</a>
</body>
</html>
