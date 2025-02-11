<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
// Include database connection
include('db_connection.php');

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $subject_id = intval($_GET['id']); // Convert ID to an integer for security

    // Delete subject query
    $delete_sql = "DELETE FROM subject WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $subject_id);

    if ($delete_stmt->execute()) {
        // Redirect with success message
        header("Location: subject.php?message=Subject Deleted Successfully");
        exit;
    } else {
        // Display error message if deletion fails
        echo "Error deleting subject: " . $conn->error;
    }
} else {
    // Redirect back if no ID is provided
    header("Location: subject_list.php?error=Invalid Subject ID");
    exit;
}

// Close the database connection
$conn->close();
?>
