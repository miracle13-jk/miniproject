<?php
session_start();

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "job_portal");

$success = "";
$error = "";

// Handle CV upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv'])) {
    $target_dir = "uploads/cvs/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["cv"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validate file
    if ($_FILES["cv"]["size"] > 5000000) { // 5MB max
        $error = "File is too large (max 5MB)";
    } elseif ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        $error = "Only PDF, DOC & DOCX files are allowed";
    } elseif (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
        // Save to database
        $stmt = $conn->prepare("UPDATE users SET cv_path = ? WHERE id = ?");
        $stmt->bind_param("si", $target_file, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = "CV uploaded successfully!";
        } else {
            $error = "Error updating database";
        }
        $stmt->close();
    } else {
        $error = "Error uploading file";
    }
}

// Get student's CV path if exists
$cv_path = "";
$stmt = $conn->prepare("SELECT cv_path FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($cv_path);
$stmt->fetch();
$stmt->close();

// Get job listings
$jobs = array();
$result = $conn->query("SELECT * FROM jobs WHERE deadline >= CURDATE() ORDER BY posted_at DESC");
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <p>Student Account</p>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="student_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <!--<li><a href="search.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>!-->
                    <li><a href="applied.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <!--<li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>!-->
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h2>Dashboard Overview</h2>
                <!--<div class="search-notification">
                    <input type="text" placeholder="Search jobs..." ><a href="search.php">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge"></span>!-->
                    </div>
                </div>
            </header>

            <!-- CV Upload Section -->
            <section class="cv-section">
                <h3>My CV</h3>
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="cv-upload">
                    <?php if ($cv_path && file_exists($cv_path)): ?>
                        <p>Current CV: <a href="<?php echo $cv_path; ?>" target="_blank">View CV</a></p>
                    <?php else: ?>
                        <p>No CV uploaded yet</p>
                    <?php endif; ?>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
                        <button type="submit">Upload CV</button>
                    </form>
                </div>
            </section>

            <!-- Recent Job Listings -->
            <section class="job-listings">
                <h3>Recent Job Opportunities</h3>
                
                <div class="jobs-grid">
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-card">
                            <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                            <p class="company"><?php echo htmlspecialchars($job['company']); ?></p>
                            <p class="location">📍 <?php echo htmlspecialchars($job['location']); ?></p>
                            <p class="salary">💰 <?php echo htmlspecialchars($job['salary']); ?></p>
                            <p class="deadline">⏰ Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?></p>
                            <div class="job-actions">
                                <a href="job_details.php?id=<?php echo $job['id']; ?>" class="view-btn">View Details</a>
                                <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="apply-btn">Apply Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($jobs)): ?>
                        <p>No current job openings available. Check back later!</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>