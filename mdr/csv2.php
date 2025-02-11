<?php
// Include authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users
    exit();
}

// Include the database connection file
include 'db_connection.php';

$normalizedData = []; // Array to hold normalized data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $fileType = mime_content_type($_FILES['file']['tmp_name']);
        $fileExt = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if (!in_array($fileType, ['text/plain', 'text/csv']) || strtolower($fileExt) !== 'csv') {
            die("<div class='alert alert-danger text-center'>Please upload a valid CSV file.</div>");
        }

        // Temporary file path
        $filePath = $_FILES['file']['tmp_name'];

        // Open the file and read its content
        if (($handle = fopen($filePath, "r")) !== false) {
            $headers = fgetcsv($handle); // Get the first row as headers

            // Read and normalize CSV data
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                $normalizedData[] = [
                    'teacher_name' => trim($row[0]), // Teacher name
                    'subject' => trim($row[1]), // Subject
                    'lecture_date' => trim($row[2]), // Lecture date (YYYY-MM-DD)
                    'start_time' => trim($row[3]), // Start time
                    'end_time' => trim($row[4]), // End time
                    'classroom' => isset($row[5]) ? trim($row[5]) : '' // Classroom (optional)
                ];
            }
            fclose($handle);
        } else {
            echo "<div class='alert alert-danger text-center'>Error opening the file.</div>";
        }
    } elseif (isset($_POST['insert_data'])) {
        // Handle data insertion
        if (!empty($_POST['normalized_data'])) {
            $normalizedData = json_decode($_POST['normalized_data'], true);

            // Prepare the SQL statement
            $sql = "INSERT INTO timetable2 (teacher_name, subject, lecture_date, start_time, end_time, classroom) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                foreach ($normalizedData as $row) {
                    $stmt->bind_param("ssssss", $row['teacher_name'], $row['subject'], $row['lecture_date'], 
                                      $row['start_time'], $row['end_time'], $row['classroom']);
                    if (!$stmt->execute()) {
                        echo "<div class='alert alert-danger text-center'>Error inserting row: " . $stmt->error . "</div>";
                    }
                }
                $stmt->close();

                // Clear preview data after successful insertion
                $normalizedData = [];

                // Redirect to timetable.php after saving
                echo "<script>
                        alert('Data inserted successfully!');
                        window.location.href = 'timetable.php';
                      </script>";
                exit();
            } else {
                echo "<div class='alert alert-danger text-center'>Error preparing the statement: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning text-center'>No data to insert.</div>";
        }
    } else {
        echo "<div class='alert alert-warning text-center'>No file uploaded or there was an upload error.</div>";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .btn-iica {
        background-color: rgb(57, 167, 152) !important;
        border-color: rgb(57, 167, 152) !important;
    }
    .custom-table {
        background-color: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
    .custom-table th {
        text-align: center;
        background-color: #343a40 !important;
        color: #ffffff !important;
    }
    .custom-table td {
        text-align: center;
        vertical-align: middle;
    }
    .custom-table tbody tr:hover {
        background-color: #dff0d8;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    </style>
</head>
<body>
    <h2 class="text-center mb-4 mt-5">Upload CSV File</h2>

    <!-- Upload Form -->
    <div class="col-sm-6 col-md-4 d-flex justify-content-center mx-auto">
        <form method="post" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light w-100">
            <div class="mb-3">
                <label for="file" class="form-label">Select CSV file:</label>
                <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary btn-iica">CSVファイルアップロード</button>
        </form>
    </div>

    <!-- Data Preview Section (Only show if data is available) -->
    <?php if (!empty($normalizedData)): ?>
    <div class="container mt-5">
        <h3 class="text-center mb-4">Data Preview</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped custom-table">
                <thead class="table-dark">
                    <tr>
                        <th>講師名</th>
                        <th>科目</th>
                        <th>日付</th>
                        <th>開始時</th>
                        <th>終了時</th>
                        <th>教室</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($normalizedData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo htmlspecialchars($row['lecture_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['classroom']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Insert Data Button -->
            <form method="post" class="mt-3">
                <input type="hidden" name="normalized_data" value='<?php echo htmlspecialchars(json_encode($normalizedData)); ?>'>
                <button type="submit" name="insert_data" class="btn btn-success btn-iica">データベースに導入</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
