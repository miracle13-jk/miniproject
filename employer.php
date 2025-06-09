<?php
session_start();

// Verify admin access
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  //  header("Location: login.php");
    //exit();
//}

$conn = new mysqli("localhost", "root", "", "job_portal");

$success = "";
$error = "";

// Handle job posting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_job'])) {
    $title = trim($_POST['title']);
    $company = trim($_POST['company']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $location = trim($_POST['location']);
    $deadline = $_POST['deadline'];
    $salary = trim($_POST['salary']);
    $job_type = $_POST['job_type'];
    
    // Validate input
    if (empty($title) || empty($company) || empty($description)) {
        $error = "Title, company and description are required";
    } else {
        $stmt = $conn->prepare("INSERT INTO jobs (title, company, description, requirements, location, deadline, salary, job_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $title, $company, $description, $requirements, $location, $deadline, $salary, $job_type);
        
        if ($stmt->execute()) {
            $success = "Job posted successfully!";
        } else {
            $error = "Error posting job: " . $conn->error;
        }
        $stmt->close();
    }
}


if (isset($_GET['toggle_job'])) {
    $job_id = intval($_GET['toggle_job']);
    $stmt = $conn->prepare("UPDATE jobs SET is_active = NOT is_active WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}

$jobs = array();
$result = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC");
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <div class="admin-dashboard">
        
        <aside class="admin-sidebar">
            <div class="admin-profile">
                <h3>Admin Panel</h3>
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <!--<li><a href="manage_jobs.php"><i class="fas fa-briefcase"></i> Manage Jobs</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>!-->
                    <li><a href="apply.php"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <!--<li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>!-->
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h2>Job Management</h2>
                <div class="admin-search">
                  <!--  <input type="text"  placeholder="Search jobs..."></a>
                    <button><i class="fas fa-search"></i></button>!-->
                </div>
            </header>

            <!-- Job Posting Form -->
            <section class="job-form-section">
                <h3><i class="fas fa-plus-circle"></i> Post New Job</h3>
                
                <?php if ($success): ?>
                    <div class="admin-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="admin-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Job Title*</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="company">Company*</label>
                            <input type="text" id="company" name="company" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location">
                        </div>
                        <div class="form-group">
                            <label for="job_type">Job Type</label>
                            <select id="job_type" name="job_type">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="salary">Salary</label>
                            <input type="text" id="salary" name="salary" placeholder="e.g., 5k - 7k">
                        </div>
                        <div class="form-group">
                            <label for="deadline">Application Deadline</label>
                            <input type="date" id="deadline" name="deadline">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Job Description*</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="requirements">Requirements</label>
                        <textarea id="requirements" name="requirements"></textarea>
                    </div>
                    
                    <button type="submit" name="post_job" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Post Job
                    </button>
                </form>
            </section>

            <!-- Job Listings Table -->
            <section class="job-table-section">
                <h3><i class="fas fa-list"></i> Current Job Listings</h3>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['company']); ?></td>
                                    <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                                    <td><?php echo htmlspecialchars($job['location']); ?></td>
                                    <td><?php echo $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : 'None'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $job['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $job['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="actions">

                                        <a href="delete.php?toggle_job=<?php echo $job['id']; ?>" class="status-btn" title="<?php echo $job['is_active'] ? 'Delete' : 'Activate'; ?>">
                                            <i class="fas fa-power-off"></i>
                                        </a>
                                        <a href="view_applicants.php?job_id=<?php echo $job['id']; ?>" class="view-btn" title="View Applicants">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Initialize CKEditor for textareas
        CKEDITOR.replace('description');
        CKEDITOR.replace('requirements');
        
        // Confirm before deactivating a job
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to change this job\'s status?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <script src="script.js"></script>
</body>
</html>