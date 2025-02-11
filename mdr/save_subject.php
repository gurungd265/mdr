<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
// Database connection file
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $subject_name = $_POST['subject_name'];
    $teacher_id = !empty($_POST['teacher_id']) ? $_POST['teacher_id'] : null;
    $subject_code = !empty($_POST['subject_code']) ? $_POST['subject_code'] : null;
    $teacher_name = !empty($_POST['teacher_name']) ? $_POST['teacher_name'] : null;

    // SQL query to insert data
    $sql = "INSERT INTO subject (subject_name, teacher_id, subject_code, teacher_name) 
            VALUES (?, ?, ?, ?)";

    // Prepare and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("siss", $subject_name, $teacher_id, $subject_code, $teacher_name);

        if ($stmt->execute()) {
            echo "<script>alert('Subject added successfully!'); window.location.href = 'sr_success.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
