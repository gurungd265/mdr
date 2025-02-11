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

// Set the number of records per page
$records_per_page = 10;

// Get the current page number from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// SQL query to count the total number of records
$sql_count = "
    SELECT COUNT(*) AS total_records 
    FROM kosiki
    INNER JOIN teacher ON kosiki.teacher_id = teacher.teacher_id
";
$result_count = $conn->query($sql_count);
$total_records = $result_count->fetch_assoc()['total_records'];

// SQL query to fetch data with LIMIT and OFFSET for pagination
$sql = "
    SELECT 
        kosiki.id,
        CONCAT(teacher.sensei_id, ' - ', teacher.name) AS teacher_info,
        kosiki.subject,
        kosiki.lecture_date,
        kosiki.time_range,
        kosiki.bikou,
        kosiki.created_at
    FROM 
        kosiki
    INNER JOIN 
        teacher 
    ON 
        kosiki.teacher_id = teacher.teacher_id
    ORDER BY 
        kosiki.id DESC
    LIMIT $offset, $records_per_page
";
$result = $conn->query($sql);

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>実績一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-iica{
            background-color: rgb(57, 167, 152);
            border-color: rgb(57, 167, 152);
        }
        .custom-thead {
        background-color: rgb(57, 167, 152); /* Blue background */
        color: white; /* White text */
    }
    </style>
    <script>
        // Function to confirm deletion
        function confirmDelete() {
            return confirm("この実績記録削除されますか?");
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
            <h2>実績一覧</h2>
            <!-- Add New button -->
            <a href="attendance_form.php" class="btn btn-primary btn-iica">実績を登録</a>
        </div>

<table class="table table-bordered">
    <thead class="custom-thead">
        <tr>
            <th scope="col">講師</th>
            <th scope="col">科目</th>
            <th scope="col">日付</th>
            <th scope="col">授業時間</th>
            <th scope="col">ティーチングモード</th>
            <th scope="col">登録時間</th>
            <th scope="col">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['teacher_info']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><?php echo htmlspecialchars($row['lecture_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_range']); ?></td>
                    <td><?php echo htmlspecialchars($row['bikou']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="edit_attendance.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm">変更</a>
                        <a href="delete_attendance.php?id=<?php echo htmlspecialchars($row['id']); ?>" 
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

        <!-- Pagination Links -->
<nav>
    <ul class="pagination justify-content-center">
        <?php if ($current_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php
        if ($total_pages <= 10) {
            // Show all pages if total is 10 or less
            for ($page = 1; $page <= $total_pages; $page++) {
                echo '<li class="page-item ' . ($page == $current_page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $page . '">' . $page . '</a>
                      </li>';
            }
        } else {
            // Show first 10 pages always
            for ($page = 1; $page <= 10; $page++) {
                echo '<li class="page-item ' . ($page == $current_page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $page . '">' . $page . '</a>
                      </li>';
            }

            // Show ellipsis if needed
            if ($current_page > 10) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            // Show the current page if it's beyond 10
            if ($current_page > 10) {
                echo '<li class="page-item active">
                        <a class="page-link" href="?page=' . $current_page . '">' . $current_page . '</a>
                      </li>';
            }
        }
        ?>

        <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

    </div>
</body>
</html>

<?php
$conn->close();
?>
