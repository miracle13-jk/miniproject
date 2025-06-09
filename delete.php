<?php
session_start();

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "job_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['toggle_job'])) {
    $job_id = intval($_GET['toggle_job']);
    
    if ($job_id <= 0) {
        $_SESSION['error'] = "Invalid job ID";
        header("Location: employer.php");
        exit();
    }
    
    // First check if job exists
    $check = $conn->prepare("SELECT id FROM jobs WHERE id = ?");
    $check->bind_param("i", $job_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows === 0) {
        $_SESSION['error'] = "Job not found";
        $check->close();
        header("Location: admin_dashboard.php");
        exit();
    }
    $check->close();
    
    // Start transaction for atomic operation
    $conn->begin_transaction();
    
    try {
        // Delete related applications first
        $delete_apps = $conn->prepare("DELETE FROM applications WHERE job_id = ?");
        $delete_apps->bind_param("i", $job_id);
        
        if (!$delete_apps->execute()) {
            throw new Exception("Error deleting applications: " . $conn->error);
        }
        $delete_apps->close();
        
        // Then delete the job
        $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->bind_param("i", $job_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Job and related applications deleted successfully";
        } else {
            throw new Exception("Error deleting job: " . $conn->error);
        }
        
        $stmt->close();
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }
} else {
    $_SESSION['error'] = "No job ID specified";
}

$conn->close();
header("Location:employer.php");
exit();
?>