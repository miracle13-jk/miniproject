Here's a complete example with HTML and PHP:

Update Job Listing
HTML Form

<!DOCTYPE html>
<html>
<head>
    <title>Update Job Listing</title>
</head>
<body>
<form action="update_job.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $job_id; ?>">
    <label for="title">Title:</label>
    <input type="text" name="title" value="<?php echo $job_title; ?>"><br><br>
    
    <label for="company">Company:</label>
    <input type="text" name="company" value="<?php echo $job_company; ?>"><br><br>

    <label for="description">Description:</label>
    <textarea name="description"><?php echo $job_description; ?></textarea><br><br>

    <label for="requirements">Requirements:</label>
    <textarea name="requirements"><?php echo $job_requirements; ?></textarea><br><br>

    <label for="location">Location:</label>
    <input type="text" name="location" value="<?php echo $job_location; ?>"><br><br>

    <label for="deadline">Deadline:</label>
    <input type="date" name="deadline" value="<?php echo $job_deadline; ?>"><br><br>

    <label for="salary">Salary:</label>
    <input type="number" name="salary" value="<?php echo $job_salary; ?>"><br><br>

    <label for="job_type">Job Type:</label>
    <select name="job_type">
        <option value="Full-time" <?php if ($job_type == 'Full-time') echo 'selected'; ?>>Full-time</option>
        <option value="Part-time" <?php if ($job_type == 'Part-time') echo 'selected'; ?>>Part-time</option>
        <option value="Contract" <?php if ($job_type == 'Contract') echo 'selected'; ?>>Contract</option>
        <option value="Internship" <?php if ($job_type == 'Internship') echo 'selected'; ?>>Internship</option>
    </select><br><br>

    <label for="is_active">Is Active:</label>
    <select name="is_active">
        <option value="1" <?php if ($is_active == 1) echo 'selected'; ?>>Yes</option>
        <option value="0" <?php if ($is_active == 0) echo 'selected'; ?>>No</option>
    </select><br><br>

    <input type="submit" value="Update Job">
</form>
</body>
</html>
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "job_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure `id` is received via GET request
if (!isset($_GET['id'])) {
    die("Error: No job ID provided.");
}

$job_id = $_GET['id'];

// Fetch existing job details
$sql = "SELECT * FROM jobs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $job = $result->fetch_assoc();
    $job_title = $job['title'];
    $job_company = $job['company'];
    $job_description = $job['description'];
    $job_requirements = $job['requirements'];
    $job_location = $job['location'];
    $job_deadline = $job['deadline'];
    $job_salary = $job['salary'];
    $job_type = $job['job_type'];
    $is_active = $job['is_active'];
} else {
    die("Error: Job not found.");
}

$stmt->close();
?>
