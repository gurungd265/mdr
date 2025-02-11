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

// Read JSON input
$data = json_decode(file_get_contents("php://input"));

// Check if all required fields are present
if (isset($data->id, $data->subject, $data->startTime, $data->endTime, $data->teacherName, $data->classRoom)) {
    $id = $data->id;
    $subject = $data->subject;
    $startTime = $data->startTime;
    $endTime = $data->endTime;
    $teacherName = $data->teacherName;
    $classRoom = $data->classRoom;

    // Ensure that the start time is before the end time
    if ($startTime >= $endTime) {
        echo json_encode(['success' => false, 'error' => '開始時間は終了時間より前でなければなりません。']);
        exit;
    }

    // Prepare and execute the update query
    $sql = "UPDATE timetable3 SET subject = ?, start_time = ?, end_time = ?, teacher_name = ?, classroom = ? WHERE id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("sssssi", $subject, $startTime, $endTime, $teacherName, $classRoom, $id);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'データベースの更新に失敗しました。']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'クエリの準備中にエラーが発生しました。']);
    }
} else {
    echo json_encode(['success' => false, 'error' => '必要なデータが不足しています。']);
}

// Close the database connection
$conn->close();
?>
