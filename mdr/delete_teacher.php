<?php
// Include authentication check
include 'auth_check.php';

// Ensure only admins can delete teachers
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect if not an admin
    exit();
}

// Include database connection
include('db_connection.php');

// Check if 'id' is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $teacher_id = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: teacher.php?message=講師の情報が削除されました。");
        exit();
    } else {
        // Redirect with error message
        header("Location: teacher.php?message=削除に失敗しました。");
        exit();
    }

    // Close statement
    $stmt->close();
} else {
    // Redirect if 'id' is missing or invalid
    header("Location: teacher_list.php?message=無効なリクエストです。");
    exit();
}

// Close database connection
$conn->close();
?>
