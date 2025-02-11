<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
require 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $teacher_id = $_POST['teacher_id'];
    $lecture_hour = $_POST['lecture_hour'];
    $teaching_mode = $_POST['teaching_mode'];
    $time_ranges = isset($_POST['time_range']) ? $_POST['time_range'] : [];
    $subjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];

    // Validate required fields
    if (empty($date) || empty($teacher_id) || empty($lecture_hour) || empty($teaching_mode) || empty($subjects)) {
        header("Location: attendance_form.php?status=error&message=All fields are required.");
        exit;
    }

    $recordsUpdated = 0;
    $recordsInserted = 0;

    // Loop through subjects and time ranges
    foreach ($subjects as $subject) {
        foreach ($time_ranges as $time_range) {
            // Check if a record already exists
            $checkQuery = "SELECT * FROM kosiki WHERE lecture_date = ? AND teacher_id = ? AND subject = ? AND time_range = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param('siss', $date, $teacher_id, $subject, $time_range);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing record
                $updateQuery = "UPDATE kosiki SET comasuu = ?, bikou = ? WHERE lecture_date = ? AND teacher_id = ? AND subject = ? AND time_range = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param('ssssss', $lecture_hour, $teaching_mode, $date, $teacher_id, $subject, $time_range);
                if ($updateStmt->execute()) {
                    $recordsUpdated++;
                }
                $updateStmt->close();
            } else {
                // Insert new record
                $insertQuery = "INSERT INTO kosiki (lecture_date, teacher_id, subject, comasuu, bikou, time_range) VALUES (?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param('sissss', $date, $teacher_id, $subject, $lecture_hour, $teaching_mode, $time_range);
                if ($insertStmt->execute()) {
                    $recordsInserted++;
                }
                $insertStmt->close();
            }

            $checkStmt->close();
        }
    }

    $conn->close();

    // Redirect with success message
    header("Location: attendance_success.php?status=success&inserted=$recordsInserted&updated=$recordsUpdated");
    exit;
}
?>
