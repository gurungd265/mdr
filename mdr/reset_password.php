<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
require 'db_connection.php'; // Include database connection

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires >= ?");
    $stmt->bind_param("si", $token, time()); // Use time() instead of date("U") for better consistency
    $stmt->execute();
    $result = $stmt->get_result();
    $resetRequest = $result->fetch_assoc();

    if ($resetRequest) {
        // Token is valid; show the reset form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            // Basic password validation
            if (empty($newPassword) || empty($confirmPassword)) {
                $error = "Both password fields are required.";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "Passwords do not match.";
            } elseif (strlen($newPassword) < 8) {
                $error = "Password must be at least 8 characters long.";
            } else {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the user's password in the database
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashedPassword, $resetRequest['email']);
                
                if ($stmt->execute()) {
                    // Delete the token from the database
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    $stmt->execute();

                    // Redirect to login page with a success message
                    header("Location: login.php?message=success");
                    exit();
                } else {
                    $error = "An error occurred while resetting the password.";
                }
            }
        }
    } else {
        $error = "This token is invalid or has expired.";
    }
} else {
    $error = "No token provided.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .btn-iica{
        background-color: rgb(57, 167, 152)!important;
        border-color: rgb(57, 167, 152) !important;
    }
</style>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>パスワード変更</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">新しいパスワード</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="新しいパスワードを入力" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">パスワードの確認</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="新しいパスワードを再一度入力" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-iica">パスワードを変更する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
