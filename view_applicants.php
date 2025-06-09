
<?php 
$conn = new mysqli("localhost","root","","job_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);

    // Query to get job details
    $job_stmt = $conn->prepare("SELECT title FROM jobs WHERE id = ?");
    $job_stmt->bind_param("i", $job_id);
    $job_stmt->execute();
    $job_result = $job_stmt->get_result();
    $job = $job_result->fetch_assoc();

    // Query to get applicants
    $applicants_stmt = $conn->prepare("SELECT * FROM applications WHERE job_id = ?");
    $applicants_stmt->bind_param("i", $job_id);
    $applicants_stmt->execute();
    $applicants_result = $applicants_stmt->get_result();
    $applicants = array();
    while ($row = $applicants_result->fetch_assoc()) {
        $applicants[] = $row;
    }
} else {
    echo "No job ID specified.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Applicants for <?php echo htmlspecialchars($job['title']); ?></h2>
    <table border="2">
        <thead>
            <tr>
                <th>CV</th>
                <th>Applied At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applicants as $applicant): ?>
            <tr>
                <td><a href="<?php echo htmlspecialchars($applicant['cv_path']); ?>">View CV</a></td>
                <td><?php echo date('M d, Y H:i:s', strtotime($applicant['applied_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>