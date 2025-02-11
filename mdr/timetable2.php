<?php
// Include the database connection file
include 'db_connection.php';

// Fetch timetable events
$sql = "SELECT id, teacher_name, subject, lecture_date, start_time, end_time, classroom FROM timetable2";
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
    <title>時間割カレンダー</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/ja.js"></script>
    <style>
        #calendar { max-width: 1000px; margin: auto; }
        .event-content { white-space: normal; }

        .btn-iica{
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
        }

        /* Edit Button Styling */
        .btn-warning.edit-btn {
            background-color:rgb(17, 29, 196); /* Warning color */
            color: #fff; /* White text */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners */
            padding: 5px 10px; /* Add some padding */
            font-size: 12px; /* Adjust font size */
            cursor: pointer; /* Pointer cursor on hover */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            transition: all 0.3s ease; /* Smooth hover effect */
        }

        .btn-warning.edit-btn:hover {
            background-color: #ec971f; /* Darker warning color on hover */
            transform: scale(1.05); /* Slightly enlarge the button */
        }

        /* Delete Button Styling */
        .btn-danger.delete-btn {
            background-color:rgb(203, 25, 12); /* Danger color */
            color: #fff; /* White text */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners */
            padding: 5px 10px; /* Add some padding */
            font-size: 12px; /* Adjust font size */
            cursor: pointer; /* Pointer cursor on hover */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            transition: all 0.3s ease; /* Smooth hover effect */
        }

        .btn-danger.delete-btn:hover {
            background-color: #c9302c; /* Darker danger color on hover */
            transform: scale(1.05); /* Slightly enlarge the button */
        }

        /* Additional Styling for Buttons in Calendar */
        button.btn {
            margin: 2px; /* Add some spacing between buttons */
            font-weight: bold; /* Make text bold */
        }

    </style>
</head>
<body>
    
<div class="text-center">
    <h2 class="text-white mt-4" style="display: inline-block; background-color: rgb(57, 111, 227); padding: 2px 5px;">
    グローバルビジネス－２年
    </h2>
</div>
<div class="container">
    <div class="d-flex justify-content-end">
        <a href="csv2.php" class="btn btn-primary me-3 btn-iica">CSVを導入</a>
    </div>
    <div id="calendar" class="mt-3 me-3"></div>
</div>



<!-- Add Lecture Modal -->
<div class="modal fade" id="addLectureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addLectureForm">
                <div class="modal-header">
                    <h5 class="modal-title">新しい講義を追加</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="lectureDate">
                    <div class="mb-3">
                        <label for="subject" class="form-label">科目名</label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="startTime" class="form-label">開催時</label>
                        <input type="text" class="form-control" id="startTime" required>
                    </div>
                    <div class="mb-3">
                        <label for="endTime" class="form-label">終了時</label>
                        <input type="text" class="form-control" id="endTime" required>
                    </div>
                    <div class="mb-3">
                        <label for="teacherName" class="form-label">講師名</label>
                        <input type="text" class="form-control" id="teacherName" required>
                    </div>
                    <div class="mb-3">
                        <label for="classRoom" class="form-label">教室</label>
                        <input type="text" class="form-control" id="classRoom" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lecture Modal -->
<div class="modal fade" id="editLectureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editLectureForm">
                <div class="modal-header">
                    <h5 class="modal-title">講義を編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editLectureId">
                    <div class="mb-3">
                        <label for="editSubject" class="form-label">科目名</label>
                        <input type="text" class="form-control" id="editSubject" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStartTime" class="form-label">開催時</label>
                        <input type="text" class="form-control" id="editStartTime" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEndTime" class="form-label">終了時</label>
                        <input type="text" class="form-control" id="editEndTime" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTeacherName" class="form-label">講師名</label>
                        <input type="text" class="form-control" id="editTeacherName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editClassRoom" class="form-label">教室</label>
                        <input type="text" class="form-control" id="editClassRoom" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var events = <?php echo json_encode($events); ?>;

    // Subject color mapping
    var subjectColors = {
        "開発演習": "#FFD700",
        "ビジネス実務": "#87CEEB",
        "セキュリティ診断": "#FFA07A",
        "キャリア": "#98FB98",
        "グローバルスタデ": "#DDA0DD"
    };

    events.forEach(event => {
        if (!subjectColors[event.title]) {
            subjectColors[event.title] = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
        }
    });

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ja',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        events: events.map(event => ({
            ...event,
            backgroundColor: subjectColors[event.title] || "#D3D3D3",
            borderColor: subjectColors[event.title] || "#D3D3D3"
        })),
        eventContent: function (info) {
            let eventHtml = `
                <div class="event-content">
                    <strong>${info.event.title}</strong><br>
                    <span>${info.event.extendedProps.startTime} - ${info.event.extendedProps.endTime}</span><br>
                    <span>${info.event.extendedProps.teacherName}</span><br>
                    <span>${info.event.extendedProps.classRoom}</span><br>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${info.event.id}">編集</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${info.event.id}">削除</button>
                </div>`;
            return { html: eventHtml };
        },
        dateClick: function (info) {
            document.getElementById('lectureDate').value = info.dateStr;
            new bootstrap.Modal(document.getElementById('addLectureModal')).show();
        }
    });

    calendar.render();

    // Add Lecture - Handle form submission
    document.getElementById('addLectureForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const lectureDate = document.getElementById('lectureDate').value;
        const subject = document.getElementById('subject').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;
        const teacherName = document.getElementById('teacherName').value;
        const classRoom = document.getElementById('classRoom').value;

        // Validate inputs
        if (!lectureDate || !subject || !startTime || !endTime || !teacherName || !classRoom) {
            alert('すべてのフィールドを記入してください。');
            return;
        }


        // Send data to the server
        fetch('add_event2.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                date: lectureDate,
                subject,
                startTime,
                endTime,
                teacherName,
                classRoom
            }),
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('講義が正常に追加されました！');

                // Add the new event to the calendar dynamically
                calendar.addEvent({
                    id: data.id, // Ensure the server returns the new event ID
                    title: subject,
                    start: `${lectureDate}T${startTime}`,
                    end: `${lectureDate}T${endTime}`,
                    backgroundColor: subjectColors[subject] || "#D3D3D3",
                    borderColor: subjectColors[subject] || "#D3D3D3",
                    extendedProps: {
                        startTime: startTime,
                        endTime: endTime,
                        teacherName: teacherName,
                        classRoom: classRoom,
                    }
                });

                // Close the modal and reset the form
                const modal = bootstrap.Modal.getInstance(document.getElementById('addLectureModal'));
                modal.hide();
                document.getElementById('addLectureForm').reset();
            } else {
                alert(`エラー: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。もう一度お試しください。');
        });
    });

    // Edit Lecture - Handle form submission
    document.getElementById('editLectureForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('editLectureId').value;
        const subject = document.getElementById('editSubject').value;
        const startTime = document.getElementById('editStartTime').value;
        const endTime = document.getElementById('editEndTime').value;
        const teacherName = document.getElementById('editTeacherName').value;
        const classRoom = document.getElementById('editClassRoom').value;

        // Validate inputs
        if (!subject || !startTime || !endTime || !teacherName || !classRoom) {
            alert('すべてのフィールドを記入してください。');
            return;
        }

        fetch('edit_event2.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, subject, startTime, endTime, teacherName, classRoom }),
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('講義が正常に編集されました！');

                // Close modal and reset the form
                const modal = bootstrap.Modal.getInstance(document.getElementById('editLectureModal'));
                modal.hide();
                document.getElementById('editLectureForm').reset();

                // Reload the page to reflect changes
                window.location.reload();
            } else {
                alert(`エラー: ${data.error || '予期しないエラーが発生しました。'}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。もう一度お試しください。');
        });
    });

    // Add click handler for Edit buttons
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-btn')) {
            const eventId = e.target.dataset.id;
            const event = calendar.getEventById(eventId);

            document.getElementById('editLectureId').value = event.id;
            document.getElementById('editSubject').value = event.title;
            document.getElementById('editStartTime').value = event.extendedProps.startTime;
            document.getElementById('editEndTime').value = event.extendedProps.endTime;
            document.getElementById('editTeacherName').value = event.extendedProps.teacherName;
            document.getElementById('editClassRoom').value = event.extendedProps.classRoom;

            new bootstrap.Modal(document.getElementById('editLectureModal')).show();
        }
    });

    // Add click handler for Delete buttons
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-btn')) {
            const eventId = e.target.dataset.id;
            const event = calendar.getEventById(eventId);

            if (confirm('この講義を削除しますか？')) {
                fetch('delete_event2.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: eventId })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        event.remove(); // Remove event from the calendar
                        alert('講義が削除されました！');
                    } else {
                        alert(`エラー: ${data.error}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('エラーが発生しました。もう一度お試しください。');
                });
            }
        }
    });
});

</script>

</body>
</html>
