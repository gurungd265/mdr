<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centers items horizontally */
            justify-content: center; /* Centers items vertically */
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }
        .btn {
            margin: 5px;
            width: auto;
            padding: 8px 20px;
            font-size: 14px;
        }
        h2, p {
            text-align: center; /* Centers text */
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['message'])): ?>
            <h2 class="text-success mb-3">完了!</h2>
            <p class="mb-3"><?= htmlspecialchars($_GET['message']) ?></p>
        <?php else: ?>
            <h2 class="text-danger mb-3">Error!</h2>
            <p class="mb-3">Something went wrong. Please try again.</p>
        <?php endif; ?>

        <div>
            <a href="addteacher.php" class="btn btn-primary">続けて登録するする</a>
            <a href="teacher.php" class="btn btn-secondary">講師一致</a>
        </div>
    </div>
</body>
</html>
