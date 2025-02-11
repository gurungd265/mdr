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

// Check if ID is provided
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    
    // Fetch teacher data by ID
    $sql = "SELECT * FROM teacher WHERE teacher_id = $teacher_id";
    $result = $conn->query($sql);
    $teacher = $result->fetch_assoc();
}

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $sensei_id = $_POST['sensei_id'];
    $email = $_POST['email'];
    $hourly_rate = $_POST['hourly_rate'];
    $kyori = $_POST['kyori'];

    $update_sql = "UPDATE teacher SET name='$name',sensei_id='$sensei_id', email='$email', hourly_rate='$hourly_rate', kyori='$kyori' WHERE teacher_id=$teacher_id";
    
    if ($conn->query($update_sql) === TRUE) {
        header("Location: teacher.php?message=講師の情報を更新されました！！");
        exit;
    } else {
        echo "Error updating teacher: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>講師情報更新</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>講師情報更新更新</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">氏名</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $teacher['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="sensei_id" class="form-label">講師ID</label>
                <input type="text" class="form-control" id="sensei_id" name="sensei_id" value="<?php echo $teacher['sensei_id']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">メール</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="hourly_rate" class="form-label">単価</label>
                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo $teacher['hourly_rate']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="kyori" class="form-label">距離</label>
                <input type="number" class="form-control" id="kyori" name="kyori" value="<?php echo $teacher['kyori']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="teacher.php" class="btn btn-secondary">キャンセル</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
