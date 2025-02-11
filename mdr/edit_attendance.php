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
    header('Location: attendance_list.php'); // Redirect if no ID is provided
    exit();
}

$id = $_GET['id'];

// Fetch the attendance record to edit
$sql = "
    SELECT 
        kosiki.id,
        kosiki.teacher_id,
        kosiki.subject,
        kosiki.lecture_date,
        kosiki.time_range,
        kosiki.bikou
    FROM 
        kosiki
    WHERE 
        kosiki.id = $id
";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: attendance.php'); // Redirect if no record is found
    exit();
}

$row = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $subject = $_POST['subject'];
    $lecture_date = $_POST['lecture_date'];
    $time_range = $_POST['time_range'];
    $bikou = $_POST['bikou'];

    // Update the record
    $update_sql = "
        UPDATE kosiki
        SET 
            teacher_id = '$teacher_id',
            subject = '$subject',
            lecture_date = '$lecture_date',
            time_range = '$time_range',
            bikou = '$bikou'
        WHERE 
            id = $id
    ";

    if ($conn->query($update_sql) === TRUE) {
        header('Location: attendance.php?message=Record updated successfully');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch all teachers for the dropdown
$teachers_sql = "SELECT teacher_id, name FROM teacher";
$teachers_result = $conn->query($teachers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>実績登録更新</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="teacher_id" class="form-label">講師</label>
                <select class="form-select" id="teacher_id" name="teacher_id" required>
                    <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                        <option value="<?php echo $teacher['teacher_id']; ?>" <?php echo ($teacher['teacher_id'] == $row['teacher_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teacher['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">科目</label>
                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($row['subject']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lecture_date" class="form-label">日付</label>
                <input type="date" class="form-control" id="lecture_date" name="lecture_date" value="<?php echo htmlspecialchars($row['lecture_date']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="time_range" class="form-label">時間帯</label>
                <input type="text" class="form-control" id="time_range" name="time_range" value="<?php echo htmlspecialchars($row['time_range']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="time_range" class="form-label">ティーチングモード</label>
                <input type="text" class="form-control" id="bikou" name="bikou" value="<?php echo htmlspecialchars($row['bikou']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="attendance.php" class="btn btn-secondary">キャンセル</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>