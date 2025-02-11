<?php
// Include the database connection file
include 'db_connection.php';

// Fetch timetable events
$sql = "SELECT id, teacher_name, subject, lecture_date, start_time, end_time, classroom FROM timetable";
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Combine date and time for FullCalendar
        $events[] = [
            'id' => $row['id'],
            'title' => $row['subject'],
            'teacherName' => $row['teacher_name'],
            'start' => $row['lecture_date'] . 'T' . substr($row['start_time'], 0, 5), // Remove seconds
            'end' => $row['lecture_date'] . 'T' . substr($row['end_time'], 0, 5), // Remove seconds
            'classRoom' => $row['classroom'],
            'extendedProps' => [
                'startTime' => substr($row['start_time'], 0, 5), // Remove seconds
                'endTime' => substr($row['end_time'], 0, 5), // Remove seconds
                'teacherName' => $row['teacher_name'],
                'classRoom' => $row['classroom'],
            ],
        ];
        
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>時間割カレンダー（観覧専用）</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN (最新バージョン) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/ja.js"></script>
    <style>
         body {
            background-color: #f1f3f5;
        }
        #calendar { max-width: 1000px; margin: auto; }
        .event-content { white-space: normal; }
/* Style for event content */
.fc-event .event-content {
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-size: 14px; /* Default font size for event content */
    color: #333; /* Default text color */
}

.event-content span {
            font-size: 16px; /* Adjust the font size as needed */
            color: #fff;
        }

/* Event title style */
.fc-event .event-content strong {
    font-size: 16px; /* Larger font size for the title */
    color: #fff; /* Dark color for the title */
}

/* Event time and additional details style */
.fc-event .event-content span {
    font-size: 12px; /* Smaller font size for time and details */
    color: #fff; /* Lighter color for time and details */
}

/* Button styles inside the event */
.fc-event .event-content .btn {
    margin-top: 5px;
    font-size: 12px; /* Font size for buttons */
}

/* Specific button styles */
.fc-event .event-content .btn-warning {
    background-color: #ffcc00;
    border-color: #ffcc00;
    color: #fff; /* White text on warning button */
}

.fc-event .event-content .btn-danger {
    background-color: #ff6666;
    border-color: #ff6666;
    color: #fff; /* White text on delete button */
}


    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container">
        <!-- ロゴを左側に配置 -->
        <a class="navbar-brand" href="#">
            <img src="logo.jpg" alt="Logo" style="width: 300px;">
        </a>

        <!-- トグルボタン (スマホサイズでナビバーを展開するため) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ナビゲーションアイテム -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <!-- ユーザーアイコンを右側に配置 -->
                <li class="nav-item">
                    <a class="nav-link" href="admin.php" style="color:rgb(101, 170, 167); font-size: 18px; font-weight: bold; display: flex; align-items: center;">
                         管理者ログイン
                          <i class="fa-solid fa-user fa-lg" style="margin-left: 8px; color:rgb(101, 170, 167);"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center">時間割（観覧専用）</h2>
    <div id="calendar"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var events = <?php echo json_encode($events); ?>; // Assuming you have a PHP variable $events

        // Map subjects to colors
        var subjectColors = {
            "開発演習": "#FFD700",
            "ビジネス実務": "#87CEEB",
            "セキュリティ診断": "#FFA07A",
            "キャリア": "#98FB98",
            "グローバルスタデ": "#DDA0DD"
        };

        // Dynamically add colors for new subjects
        events.forEach(event => {
            if (!subjectColors[event.title]) {
                subjectColors[event.title] = '#' + Math.floor(Math.random() * 16777215).toString(16);
            }
        });

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ja',
            initialView: 'dayGridMonth',
            events: events.map(event => ({
                ...event,
                backgroundColor: subjectColors[event.title] || "#D3D3D3",
                borderColor: subjectColors[event.title] || "#D3D3D3"
            })),
            eventContent: function (info) {
                let eventHtml = `
                    <div class="event-content p-2 rounded shadow-sm" style="background-color: ${subjectColors[info.event.title] || '#D3D3D3'};">
                        <strong class="d-block mb-1">${info.event.title}</strong>
                        <span class="d-block text-muted" style="font-size: 16px;">${info.event.extendedProps.startTime} - ${info.event.extendedProps.endTime}</span>
                        <span class="d-block text-muted" style="font-size: 16px;">${info.event.extendedProps.teacherName}</span>
                        <span class="d-block text-muted" style="font-size: 16px;">${info.event.extendedProps.classRoom}</span>
                    </div>`;
                return { html: eventHtml };
            },
            dateClick: function (info) {
                document.getElementById('lectureDate').value = info.dateStr;
                new bootstrap.Modal(document.getElementById('addLectureModal')).show();
            }
        });

        calendar.render();
    });
    </script>

<!-- 必要なJavaScriptを追加 -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-qGCRHjIV9Q3D6KVGDmj4GpTKZFu7elG5hL44hw94W1y5suGd9j9LY3o5EzXDdAyM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoY9NU1hfTgiUR/3VRr9ZA+oEmNsE6cqSbFYFf38L5ej5x0" crossorigin="anonymous"></script>

</body>
</html>