<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
// Include the database connection file
include 'db_connection.php';

// Set the response header to return JSON
header('Content-Type: application/json');

try {
    // Get the JSON data from the request body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validate input data
    if (
        empty($data['date']) || empty($data['subject']) || 
        empty($data['startTime']) || empty($data['endTime']) || 
        empty($data['teacherName']) || empty($data['classRoom'])
    ) {
        echo json_encode(['success' => false, 'error' => 'すべてのフィールドを記入してください。']);
        exit;
    }

    // Prepare the SQL query to insert the new lecture
    $stmt = $conn->prepare("INSERT INTO timetable (lecture_date, subject, start_time, end_time, teacher_name, classroom) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssss", 
        $data['date'], 
        $data['subject'], 
        $data['startTime'], 
        $data['endTime'], 
        $data['teacherName'], 
        $data['classRoom']
    );

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'データベースエラーが発生しました。']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Handle any exceptions
    echo json_encode(['success' => false, 'error' => 'サーバーエラーが発生しました。']);
    error_log($e->getMessage());
}
?>
