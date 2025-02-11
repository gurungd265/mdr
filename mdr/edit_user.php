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

// Check if ID is provided and validate it
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch teacher data by ID
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    } else {
        header("Location: user.php?error=User not found");
        exit;
    }
} else {
    header("Location: user.php?error=No ID provided");
    exit;
}

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $user_type = $conn->real_escape_string($_POST['user_type']);

    $update_sql = "UPDATE users SET name='$name', email='$email', user_type='$user_type' WHERE id=$id";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: user.php?message=Teacher Updated Successfully");
        exit;
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Teacher</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">User Type</label>
                <input type="text" class="form-control" id="user_type" name="user_type" value="<?php echo htmlspecialchars($teacher['user_type']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="user.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
