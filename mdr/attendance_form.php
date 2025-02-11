<?php
require 'db_connection.php';

// Fetch teachers' names and IDs from the subject table
$query = "SELECT DISTINCT teacher_id, name FROM teacher";
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
    <title>Attendance Form</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
/* Responsive Styling for Attendance Form */
body {
    background-color: #f8f9fa;
}

.container {
    width: 50%;
    max-width: 600px;
    padding: 20px;
}

form {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h4 {
    text-align: center;
    background-color: rgb(57, 167, 152)!important
    font-weight: bold;
}

.form-control, .form-select {
    border-radius: 8px;
}
.btn-iica{
        background-color: rgb(57, 167, 152)!important;
        border-color: rgb(57, 167, 152) !important;
        }

/* Adjustments for smaller screens */
@media (max-width: 992px) {
    .container {
        width: 75%;
    }
}

@media (max-width: 768px) {
    .container {
        width: 90%;
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .container {
        width: 95%;
        padding: 10px;
    }
    .d-flex.gap-3 {
        flex-direction: column;
    }
    
    .form-check {
        display: flex;
        align-items: center;
    }
}


    </style>
</head>
<body>

<div class="container mt-5">
    <form id="attendanceForm" action="save_attendance.php" method="POST" class="border p-4 rounded shadow-sm bg-light">
        <h4 class="mb-4 text-primary title">実績登録</h4>

        <!-- Date Selection -->
        <div class="mb-3">
            <input type="date" id="date" name="date" class="form-control" lang="ja" required>
        </div>

        <!-- Teacher Selection -->
        <div class="mb-3">
            <select id="teacher-select" name="teacher_id" class="form-select" required>
                <option value="" disabled selected>講師名選択</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['teacher_id']) ?>">
                        <?= htmlspecialchars($row['teacher_id'] . " - " . $row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Dynamic Subject Selection -->
        <div class="mb-3" id="subject-selection">
        </div>

        <!-- Time Range Selection -->
<div class="mb-3">
    <?php 
    $time_ranges = [
        "09:30-11:00", "11:10-12:40", "13:30-15:00", "15:10-16:40"
    ];
    foreach ($time_ranges as $index => $time) {
        echo "<div class='form-check'>
                <input type='checkbox' id='time{$index}' name='time_range[]' value='{$time}' class='form-check-input time-range-checkbox'>
                <label for='time{$index}' class='form-check-label'>{$time}</label>
              </div>";
    }
    ?>
</div>

<!-- Lecture Hour Selection -->
<div class="mb-3">
    <label class="form-label fw-bold">コマ数</label>
    <div class="d-flex gap-3">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="form-check">
                <input type="radio" id="hour<?= $i ?>" name="lecture_hour" value="<?= $i ?>" class="form-check-input lecture-hour-radio" required>
                <label for="hour<?= $i ?>" class="form-check-label"><?= $i ?></label>
            </div>
        <?php endfor; ?>
    </div>
</div>

        <!-- Teaching Mode -->
        <div class="mb-3">
            <label for="teaching_mode" class="form-label fw-bold">ティーチングモード</label>
            <select id="teaching_mode" name="teaching_mode" class="form-select" required>
                <option value="" disabled selected>モードを選択</option>
                <option value="学校">学校</option>
                <option value="オンライン">オンライン</option>
            </select>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-iica w-25">登録</button>
            <a href="attendance.php" class="btn btn-dark w-25">キャンセル</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeRangeCheckboxes = document.querySelectorAll('.time-range-checkbox');
        const lectureHourRadios = document.querySelectorAll('.lecture-hour-radio');

        function updateLectureHour() {
            // Count the number of checked time range checkboxes
            const checkedCount = Array.from(timeRangeCheckboxes).filter(cb => cb.checked).length;

            // Update the lecture hour radio button selection
            lectureHourRadios.forEach(radio => {
                if (parseInt(radio.value) === checkedCount) {
                    radio.checked = true;
                }
            });
        }

        // Add event listeners to time range checkboxes
        timeRangeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateLectureHour);
        });
    });

    $(document).ready(function () {
        $('#teacher-select').on('change', function () {
            const teacherId = $(this).val();

            if (teacherId) {
                $.ajax({
                    url: 'get_subjects.php',
                    method: 'GET',
                    data: {teacher_id: teacherId},
                    beforeSend: function () {
                        $('#subject-selection').html('<p>Loading...</p>');
                    },
                    success: function (response) {
                        $('#subject-selection').html(response);
                    },
                    error: function () {
                        $('#subject-selection').html('<p class="text-danger">Failed to load subjects. Please try again.</p>');
                    }
                });
            } else {
                $('#subject-selection').html('<p class="text-muted">Subjects will be dynamically loaded.</p>');
            }
        });
    });
</script>

<?php
$conn->close();
?>
</body>
</html>
