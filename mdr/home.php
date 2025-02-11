<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styling */
        .navbar-custom {
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 10px 20px;
        }

        /* Logo Styling */
        .navbar-brand img {
            width: 250px;
            margin-left: 20px;
        }

        /* Center Dropdown */
        .navbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .navbar select {
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            background: white;
            cursor: pointer;
        }

        /* Admin Login Link */
        .admin-link {
            color: rgb(101, 170, 167);
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: 0.3s;
        }

        .admin-link:hover {
            opacity: 0.8;
        }

        .admin-link i {
            margin-left: 8px;
            color: rgb(101, 170, 167);
        }

        /* Content Area */
        .content {
            flex: 1;
            margin-top: 70px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-left: 20px;
            margin-right: 20px;
        }

        iframe {
            width: 100%;
            height: calc(100vh - 100px);
            border: none;
            border-radius: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .navbar-custom {
                padding: 5px 10px;
            }

            .navbar-brand img {
                width: 150px;
            }

            .content {
                margin-left: 10px;
                margin-right: 10px;
                margin-top: 120px;
            }

            iframe {
                height: calc(100vh - 90px);
            }

            .admin-link {
                font-size: 16px;
            }

            .navbar-center {
                position: relative;
                left: auto;
                transform: none;
                text-align: center;
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid d-flex align-items-center">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <img src="logo.jpg" alt="Logo">
            </a>

            <!-- Dropdown for Timetable Selection (Centered) -->
            <div class="navbar-center">
                <select id="timetableSelect" class="form-select w-auto" onchange="updateTimetable()">
                    <option value="timetable100.php" selected>ITソリューション２年</option>
                    <option value="timetable101.php">ITソリューション１年</option>
                    <option value="timetable102.php">グローバルビジネスー２年</option>
                    <option value="timetable103.php">地域観光デザイン－１年</option>
                </select>
            </div>

            <!-- Admin Login Link (Right Side) -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link admin-link" href="admin.php">
                        管理者ログイン
                        <i class="fa-solid fa-user fa-lg"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Content Area -->
    <div class="content">
        <iframe name="contentFrame" id="contentFrame" src="timetable100.php"></iframe>
    </div>

    <!-- JavaScript to Update Timetable -->
    <script>
        function updateTimetable() {
            var selectedValue = document.getElementById("timetableSelect").value;
            document.getElementById("contentFrame").src = selectedValue;
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
