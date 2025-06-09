<?php
session_start();
$conn = new mysqli("localhost", "root", "", "job_portal");

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();

    if (!$job) {
        header("Location: student_dashboard.php");
        exit();
    }
} else {
    header("Location: student_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="job-details-container">
        <h2><?php echo htmlspecialchars($job['title']); ?></h2>
        <p>Company: <?php echo htmlspecialchars($job['company']); ?></p>
        <p>Location: <?php echo htmlspecialchars($job['location']); ?></p>
        <p>Salary: <?php echo htmlspecialchars($job['salary']); ?></p>
        <p>Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?></p>
        <p>Description:</p>
        <p>-<?php echo strip_tags($job['description']); ?></p>
        <p>Requirements:</p>
        <p>-<?php echo strip_tags($job['requirements']); ?></p>
        <div class="job-actions">
            <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="apply-btn">Apply Now</a>
            <a href="student_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
.