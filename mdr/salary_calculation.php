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
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>給与明細</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .btn-iica{
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
        }
        .salary-sheet {
            margin-top: 30px;
            padding: 30px;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .salary-sheet h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .salary-sheet table {
            width: 100%;
            border-collapse: collapse;
        }

        .salary-sheet th,
        .salary-sheet td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .salary-sheet th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .salary-sheet td {
            text-align: right;
        }

        .salary-sheet td.left-align {
            text-align: left;
        }

        .salary-sheet .total-row td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .salary-sheet .btn-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .salary-sheet .btn-container .btn {
            padding: 10px 20px;
        }

        .salary-sheet .print-btn {
            background-color: #007bff;
            color: white;
        }

        .salary-sheet .csv-btn {
            background-color: #28a745;
            color: white;
        }
    </style>
    <script>
        // Function to print the salary sheet
        function printSalarySheet() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <?php
        // Include the database connection file
        include('db_connection.php');

        // Get the teacher_id and month from the previous form submission
        $teacher_id = $_POST['teacher_id'] ?? null;
        $month = $_POST['month'] ?? null;

        // Initialize variables
        $total_salary = null;
        $total_hours = 0;
        $hourly_rate = null;
        $transportation_fee = 0;
        $total_bikou_value = 0;  // This will store the sum of the bikou values (1 for 学校 and 0 for オンライン)
        $kyori = 0;  // Variable to store the distance (kyori) from the teacher's data

        // Check if teacher_id or month is missing
        if (!$teacher_id || !$month) {
            echo "<h5 class='text-danger'>Teacher ID or month not provided.</h5>";
            exit();
        }

        // Extract year and month from the selected date
        $year = date("Y", strtotime($month));
        $month_number = date("m", strtotime($month));

        // Query to get hourly rate, per kilometer rate, and kyori from Teacher table
        $stmt = $conn->prepare("SELECT hourly_rate, per_kilometer_rate, kyori FROM teacher WHERE teacher_id = ?");
        $stmt->bind_param("s", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $teacher_data = $result->fetch_assoc();
            $hourly_rate = $teacher_data['hourly_rate'];
            $transportation_rate = $teacher_data['per_kilometer_rate'];
            $kyori = $teacher_data['kyori'];  // Get the distance (kyori) from the teacher's record
            
            // Query to sum the total working hours (comasuu) from Lecture table for the selected teacher and month
            $stmt = $conn->prepare("SELECT * FROM kosiki WHERE teacher_id = ? AND YEAR(lecture_date) = ? AND MONTH(lecture_date) = ?");
            $stmt->bind_param("sss", $teacher_id, $year, $month_number);
            $stmt->execute();
            $result = $stmt->get_result();

            // Loop through the results and calculate total salary, transportation fee, and sum of bikou
            while ($row = $result->fetch_assoc()) {
                $comasuu = $row['comasuu'];  // Hours worked (or units worked)
                $bikou = $row['bikou'];      // Remarks (学校 or オンライン)

                // If bikou is "学校", treat it as 1, otherwise 0 (for "オンライン")
                if ($bikou === '学校') {
                    $total_bikou_value += 1;  // Add 1 for "学校"
                }

                // Accumulate total hours worked
                $total_hours += $comasuu;
            }

            // Calculate transportation fee based on the sum of bikou values and kyori (distance)
            $transportation_fee = $total_bikou_value * $transportation_rate * $kyori;

            // Calculate total salary
            $total_salary = $total_hours * $hourly_rate;
        } else {
            echo "<h5 class='text-danger'>Teacher data not found.</h5>";
        }

        // Show the salary sheet if total salary is calculated
        if (isset($total_salary)) {
        ?>
            <div class="salary-sheet" id="salary-content">
                <h2>給与明細書</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="left-align">項目</th>
                            <th>金額 (円)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="left-align">講師ID</td>
                            <td><?php echo htmlspecialchars($teacher_id); ?></td>
                        </tr>
                        <tr>
                            <td class="left-align">勤務時間</td>
                            <td><?php echo htmlspecialchars($total_hours); ?> 時間</td>
                        </tr>
                        <tr>
                            <td class="left-align">時給</td>
                            <td><?php echo htmlspecialchars(number_format($hourly_rate)); ?> 円</td>
                        </tr>
                        <tr>
                            <td class="left-align">総支給額</td>
                            <td><?php echo htmlspecialchars(number_format($total_salary)); ?> 円</td>
                        </tr>
                        <tr>
                            <td class="left-align">交通費</td>
                            <td><?php echo htmlspecialchars(number_format($transportation_fee)); ?> 円</td>
                        </tr>
                        <tr>
                            <td class="left-align">合計通勤数</td>
                            <td><?php echo htmlspecialchars($total_bikou_value); ?> 回</td>  <!-- Display sum of bikou (学校 count) -->
                        </tr>
                        <tr class="total-row">
                            <td class="left-align">総支給額 (交通費含む)</td>
                            <td><?php echo htmlspecialchars(number_format($total_salary + $transportation_fee)); ?> 円</td>
                        </tr>
                    </tbody>
                </table>

                <div class="btn-container">
                    <button class="btn print-btn btn-iica" onclick="printSalarySheet()">印刷</button>
                    <form action="download_csv.php" method="post">
                        <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher_id); ?>">
                        <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
                        <button type="submit" name="download_csv" class="btn csv-btn btn-iica">CSVダウンロード</button>
                    </form>
                </div>
            </div>
        <?php
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
