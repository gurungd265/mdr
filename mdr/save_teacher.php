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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    // Get form data
    $teacher_name = trim($_POST['teacher-name']);
    $sensei_id = trim($_POST['sensei_id']);
    $teacher_email = trim($_POST['teacher-email']);
    $teacher_rate = trim($_POST['teacher-rate']);
    $kyori = trim($_POST['kyori']);

    // Validate required fields
    if (empty($teacher_name) || empty($sensei_id) || empty($teacher_email) || empty($teacher_rate) || empty($kyori)) {
        echo "<script>alert('すべてのフィールドを入力してください。'); window.history.back();</script>";
        exit();
    }

    // Prevent SQL injection
    $teacher_name = mysqli_real_escape_string($conn, $teacher_name);
    $sensei_id = mysqli_real_escape_string($conn, $sensei_id);
    $teacher_email = mysqli_real_escape_string($conn, $teacher_email);
    $teacher_rate = mysqli_real_escape_string($conn, $teacher_rate);
    $kyori = mysqli_real_escape_string($conn, $kyori);

    // Check if the teacher ID already exists
    $check_query = "SELECT * FROM teacher WHERE sensei_id = '$sensei_id'";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('この講師IDは既に登録されています。'); window.history.back();</script>";
        exit();
    }

    // Insert data into the database
    $query = "INSERT INTO teacher (name, sensei_id, email, hourly_rate, kyori) VALUES ('$teacher_name', '$sensei_id', '$teacher_email', '$teacher_rate', '$kyori')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('講師が正常に登録されました。'); window.location.href = 'teacher.php';</script>";
    } else {
        echo "<script>alert('エラー: 登録に失敗しました。'); window.history.back();</script>";
    }
}
?>
