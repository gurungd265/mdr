<?php
// Include authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit();
}

// Include database connection
include 'db_connection.php';

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: subject.php?message=" . urlencode("Invalid subject ID."));
    exit();
}

$subject_id = $_GET['id'];

// Fetch the subject details
$sql = "SELECT id, subject_name, teacher_id, subject_code FROM subject WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: subject.php?message=" . urlencode("Subject not found."));
    exit();
}

$subject = $result->fetch_assoc();

// Fetch all teachers for dropdown
$teacher_sql = "SELECT teacher_id, name FROM teacher ORDER BY name ASC";
$teacher_result = $conn->query($teacher_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = trim($_POST['subject_name']);
    $teacher_id = $_POST['teacher_id'];
    $subject_code = trim($_POST['subject_code']);

    if (!empty($subject_name) && !empty($teacher_id) && !empty($subject_code)) {
        $update_sql = "UPDATE subject SET subject_name = ?, teacher_id = ?, subject_code = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sisi", $subject_name, $teacher_id, $subject_code, $subject_id);

        if ($update_stmt->execute()) {
            header("Location: subjects_list.php?message=" . urlencode("Subject updated successfully!"));
            exit();
        } else {
            $error_message = "Error updating subject. Please try again.";
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>科目の内容更新</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="subject_name" class="form-label">科目名科目名</label>
                <input type="text" name="subject_name" id="subject_name" class="form-control" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="teacher_id" class="form-label">講師名</label>
                <select name="teacher_id" id="teacher_id" class="form-control" required>
                    <option value="">Select Teacher</option>
                    <?php while ($teacher = $teacher_result->fetch_assoc()): ?>
                        <option value="<?php echo $teacher['teacher_id']; ?>" 
                            <?php echo ($teacher['teacher_id'] == $subject['teacher_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teacher['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="subject_code" class="form-label">科目コード</label>
                <input type="text" name="subject_code" id="subject_code" class="form-control" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">更新</button>
            <a href="subject.php" class="btn btn-secondary">キャンセル</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
