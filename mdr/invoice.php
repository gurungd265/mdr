<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
session_start();

// Include the database connection file
include 'db_connection.php';

// Fetch unique teacher IDs for dropdown
$sql = "SELECT DISTINCT teacher_id, name FROM teacher";
$result = $conn->query($sql);

// Handle filter form submission
$sel1 = $_POST['sellist1'] ?? null;
$selectedMonth = $_POST['month'] ?? null;

// Fetch teacher name for selected teacher_id
if ($sel1) {
    $teacher_query = "SELECT name FROM teacher WHERE teacher_id = ?";
    $teacher_stmt = $conn->prepare($teacher_query);
    $teacher_stmt->bind_param("s", $sel1);
    $teacher_stmt->execute();
    $teacher_result = $teacher_stmt->get_result();
    $teacher_name = $teacher_result->fetch_assoc()['name'];
    $teacher_stmt->close();
}

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Query for filtered results
$query = "SELECT kosiki.*, CONCAT(teacher.name, ' (', teacher.teacher_id, ')') AS teacher_name_with_id 
          FROM kosiki 
          INNER JOIN teacher ON kosiki.teacher_id = teacher.teacher_id 
          WHERE 1=1";

if ($sel1) {
    $query .= " AND kosiki.teacher_id = ?";
}
if ($selectedMonth) {
    $year = date("Y", strtotime($selectedMonth));
    $month = date("m", strtotime($selectedMonth));
    $query .= " AND YEAR(lecture_date) = ? AND MONTH(lecture_date) = ?";
}
$query .= " ORDER BY kosiki.created_at DESC LIMIT ?, ?";

// Prepare statement and bind parameters
$stmt = $conn->prepare($query);
if ($sel1 && $selectedMonth) {
    $stmt->bind_param("ssiii", $sel1, $year, $month, $start_from, $results_per_page);
} elseif ($sel1) {
    $stmt->bind_param("sii", $sel1, $start_from, $results_per_page);
} elseif ($selectedMonth) {
    $stmt->bind_param("ssii", $year, $month, $start_from, $results_per_page);
} else {
    $stmt->bind_param("ii", $start_from, $results_per_page);
}
$stmt->execute();
$result_teacher = $stmt->get_result();

// Count total rows for pagination
$total_rows_query = "SELECT COUNT(*) AS total FROM kosiki WHERE 1=1";
if ($sel1) {
    $total_rows_query .= " AND teacher_id = '$sel1'";
}
if ($selectedMonth) {
    $total_rows_query .= " AND YEAR(lecture_date) = '$year' AND MONTH(lecture_date) = '$month'";
}
$total_rows_result = $conn->query($total_rows_query);
$total_rows = $total_rows_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>実績一覧</title>
    <style>
        .btn-iica{
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
        }
        /* Styling the buttons for hover effect and spacing */
        table.table thead th {
            background-color: rgb(57, 167, 152) !important;
            color: white !important;
            text-align: center;
}
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <h2>講師実績一覧</h2>
    </div>

    <!-- Filter form for selecting teacher and month -->
    <form action="" method="post" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-4 mb-3">
                <select class="form-select" name="sellist1" id="teacherSelect">
                    <option value="">講師IDを選んでください</option>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['teacher_id']) . "'>" . htmlspecialchars($row['teacher_id']) . " - " . htmlspecialchars($row['name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <input type="month" class="form-control" name="month" id="monthInput">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100 btn-iica " name="submit">検索</button>
            </div>
        </div>
    </form>

    <?php if ($sel1 || $selectedMonth): ?>
    <div id="selectionHeader" class=" text-center">
        <h6>講師IDと氏名: <?php echo htmlspecialchars($sel1) . " - " . htmlspecialchars($teacher_name); ?></h6>
        <h6>年月: <?php echo htmlspecialchars($selectedMonth); ?></h6>
    </div>
    <?php endif; ?>

    <div class="mt-4">
        <?php
        if ($result_teacher->num_rows > 0) {
            echo "<table class='table table-bordered table-hover'>";
            echo "<thead style='background-color: rgb(57, 167, 152); color: white;'>
                    <tr>
                        <th>講師名とID</th>
                        <th>科目</th>
                        <th>日付</th>
                        <th>コーマ数</th>
                        <th>備考</th>
                        <th>記録時間</th>
                        <th>編集</th>
                    </tr>
                  </thead>";
            echo "<tbody>";
            while ($row = $result_teacher->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['teacher_name_with_id']) . "</td>
                        <td>" . htmlspecialchars($row['subject']) . "</td>
                        <td>" . date('Y-m-d', strtotime($row['lecture_date'])) . "</td>
                        <td>" . htmlspecialchars($row['comasuu']) . "</td>
                        <td>" . htmlspecialchars($row['bikou']) . "</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td>
                            <a href='edit_lecture.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>変更</a>
                            <a href='delete_lecture.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>削除</a>
                        </td>
                      </tr>";
            }
            echo "</tbody></table>";
            
            
            // Salary Calculation Button
            echo "<div class='text-center mt-4'>
                    <form action='salary_calculation.php' method='post'>
                        <input type='hidden' name='teacher_id' value='" . htmlspecialchars($sel1) . "'>
                        <input type='hidden' name='month' value='" . htmlspecialchars($selectedMonth) . "'>
                        <button type='submit' class='btn btn-primary mb-4 w-100 btn-iica'>給与計算</button>
                    </form>
                  </div>";

            // Pagination links
            echo "<nav aria-label='Page navigation'>";
            echo "<ul class='pagination justify-content-center'>";
            
            // Show only pages 1 to 10 initially
            $limit = 10;
            for ($i = 1; $i <= min($total_pages, $limit); $i++) {
                echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='?page=$i" . ($sel1 ? "&sellist1=$sel1" : "") . ($selectedMonth ? "&month=$selectedMonth" : "") . "'>$i</a></li>";
            }
            
            // Show "Next" button if there are more pages beyond 10
            if ($total_pages > $limit) {
                echo "<li class='page-item'><a class='page-link' href='?page=11" . ($sel1 ? "&sellist1=$sel1" : "") . ($selectedMonth ? "&month=$selectedMonth" : "") . "'>Next</a></li>";
            }
            
            echo "</ul>";
            echo "</nav>";
            
        } else {
            echo "<p class='text-center'>データが見つかりません。</p>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
