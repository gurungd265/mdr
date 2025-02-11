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

// Fetch teachers from database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-iica{
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
        }
        .custom-thead {
            background-color: rgb(57, 167, 152); /* Blue background */
            color: white; /* White text */
            text-align: center;
    }
    </style>
    <script>
        // Function to confirm deletion
        function confirmDelete() {
            return confirm("Are you sure you want to delete this teacher?");
        }

        // Function to hide the success message after 1 second
        window.onload = function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 1000); // 1000 milliseconds = 1 second
            }
        };
    </script>
</head>
<body>
    <div class="container my-5">
        <!-- Check for success message -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success" role="alert" id="successMessage">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>ユーザー一覧</h2>
            <!-- Add New button -->
            <a href="user_registration.php" class="btn btn-primary btn-iica">ユーザーを追加</a>
        </div>

        <table class="table table-bordered">
            <thead class="custom-thead">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">氏名</th>
                    <th scope="col">メール</th>
                    <th scope="col">ユーザー権限</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['user_type']; ?></td>
                            <td>
                                <!-- Edit button -->
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">変更</a>
                                 
                                <!-- Delete button with confirmation -->
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirmDelete()">削除</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No teachers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
