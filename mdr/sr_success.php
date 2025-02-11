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
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f8f9fa;
            padding-top: 50px; /* Adjust this value as needed */
        }
        .success-container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 50%; /* Adjust the width as needed */
        }
        .success-container h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>科目登録完了しました!</h1>
        <div class="btn-container">
            <a href="add_subject.php" class="btn btn-success">続いて科目登録するする</a>
            <a href="subject.php" class="btn btn-primary">科目一覧</a>
        </div>
    </div>
</body>
</html>
