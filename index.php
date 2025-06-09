<?php
session_start();
$conn = new mysqli("localhost", "root", "", "job_portal");

$result = $conn->query("SELECT * FROM jobs");
$jobs = array();

while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">

<html xmlns = "http://www.w3.org/1999/xhtml">
    <head>
        <title>Homepage</title>
        <link rel = "stylesheet" type = "text/css" href = "style.css"/>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>Welcome to Job Portal</h1>

        <ul>
            <li><a href= "#home">Home</a></li>
            <li><a href= "login.php">Login</a></li>
            <li><a href= "register.php">Register</a></li>
        </ul>
    </body>
</html>

    <section class="job-listings">
        <h2>Latest Job Listings</h2>
        <ul>
            <?php foreach ($jobs as $job) : ?>
                <li>
                    <strong><?php echo $job['title']; ?></strong> - <?php echo $job['company']; ?>
                    <form action="apply.php" method="POST">
                        <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                        <button type="submit">Apply Now</button>
                        <script src="script.js"></script>

                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <footer>
        <p>&copy; 2025 Job Portal</p>
    </footer>
</body>
</html>
