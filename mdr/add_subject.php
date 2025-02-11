<?php

// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}

require 'db_connection.php';

// Fetch teachers' names and IDs from the subject table
$query = "SELECT DISTINCT teacher_id, teacher_name FROM subject";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .btn-iica{
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">新しい科目を登録</h2>
        <form action="save_subject.php" method="POST">
            <!-- Subject Name -->
            <div class="mb-3">
                <label for="subject_name" class="form-label">科目名</label>
                <input type="text" id="subject_name" name="subject_name" class="form-control" required>
            </div>

            <!-- Teacher Dropdown -->
            <div class="mb-3">
                <label for="teacher_id" class="form-label">講師ID</label>
                <select id="teacher-select" name="teacher_id" class="form-select" required>
                    <option value="" disabled selected>講師ID選択</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['teacher_id']) ?>">
                            <?= htmlspecialchars($row['teacher_id'] . " - " . $row['teacher_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Subject Code -->
            <div class="mb-3">
                <label for="subject_code" class="form-label">科目コード</label>
                <input type="text" id="subject_code" name="subject_code" class="form-control">
            </div>

            <!-- Teacher Name (Optional) -->
            <div class="mb-3">
                <label for="teacher_name" class="form-label">講師名</label>
                <input type="text" id="teacher_name" name="teacher_name" class="form-control">
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-iica w-25">登録</button>
                <a href="subject.php" class="btn btn-dark w-25">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>
