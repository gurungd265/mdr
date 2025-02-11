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

// Check if the ID parameter is set
if (!isset($_GET['id'])) {
    header('Location: attendance.php'); // Redirect if no ID is provided
    exit();
}

$id = $_GET['id'];

// Delete the record
$delete_sql = "DELETE FROM kosiki WHERE id = $id";

if ($conn->query($delete_sql) === TRUE) {
    header('Location: attendance.php?message=実績を削除されました！');
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>