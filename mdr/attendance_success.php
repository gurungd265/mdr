<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
// Check if the status is successful
$status = isset($_GET['status']) ? $_GET['status'] : '';
$inserted = isset($_GET['inserted']) ? $_GET['inserted'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Saved Successfully</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .success-message {
            background-color: #e7f7e7;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .btn-register, .btn-home {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
        }
        .btn-register:hover, .btn-home:hover {
            background-color: #218838;
        }
        .btn-home {
            background-color: #007bff;
        }
        .btn-home:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <?php if ($status == 'success'): ?>
        <div class="success-message">
            <h2 class="text-success">送信完了しました!</h2>
            <p class="text-muted">実績記録<strong><?= htmlspecialchars($inserted) ?></strong> コマ数録されました.</p>
            <a href="attendance_form.php" class="btn-register">もう一度登録する</a>
            <a href="attendance.php" class="btn-home">実績一覧</a> <!-- New Go Back Home Button -->
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> Something went wrong while saving the attendance. Please try again.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
