<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "job_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$applied_jobs =array();

// Fetch applied jobs
if ($student_id) {
    $stmt = $conn->prepare("
        SELECT j.title, j.company, a.applied_at 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $applied_jobs[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs Applied</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h2>Jobs Applied by Student</h2>
    
    <?php if (!empty($applied_jobs)): ?>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Applied At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applied_jobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['company']); ?></td>
                        <td><?php echo date('M d, Y H:i:s', strtotime($job['applied_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No jobs applied yet.</p>
    <?php endif; ?>
</body>
</html>
