<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
require 'db_connection.php';

if (isset($_GET['teacher_id'])) {
    $teacher_id = $_GET['teacher_id'];

    $query = "SELECT subject_name FROM subject WHERE teacher_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="form-check">
                    <input type="checkbox" class="form-check-input" name="subjects[]" value="' . htmlspecialchars($row['subject_name']) . '">
                    <label class="form-check-label">' . htmlspecialchars($row['subject_name']) . '</label>
                  </div>';
        }
    } else {
        echo '<p class="text-muted">No subjects found for the selected teacher.</p>';
    }

    $stmt->close();
}
?>
