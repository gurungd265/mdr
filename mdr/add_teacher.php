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
    <title>Add Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .btn-iica {
            background-color: rgb(57, 167, 152) !important;
            border-color: rgb(57, 167, 152) !important;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">講師を追加</h2>
        <form id="addTeacherForm" method="POST" action="save_teacher.php">
            <div class="mb-3">
                <label for="teacher-name" class="form-label">氏名</label>
                <input type="text" id="teacher-name" name="teacher-name" class="form-control" placeholder="" required>
            </div>
            <div class="mb-3">
                <label for="sensei_id" class="form-label">講師ID</label>
                <input type="text" id="sensei_id" name="sensei_id" class="form-control" placeholder="" required>
            </div>
            <div class="mb-3">
                <label for="teacher-email" class="form-label">メールアドレス</label>
                <input type="email" id="teacher-email" name="teacher-email" class="form-control" placeholder="" required>
            </div>
            <div class="mb-3">
                <label for="teacher-rate" class="form-label">単価</label>
                <input type="number" id="teacher-rate" name="teacher-rate" class="form-control" placeholder="" required>
            </div>
            <div class="mb-3">
                <label for="kyori" class="form-label">自宅からIICAまでの距離</label>
                <input type="number" id="kyori" name="kyori" class="form-control" placeholder="" required>
            </div>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-secondary w-100" onclick="window.location.href='teacher.php'">キャンセル</button>
                </div>
                <div class="col text-end">
                    <button type="submit" class="btn btn-primary btn-iica w-100" name="add_teacher">登録</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>