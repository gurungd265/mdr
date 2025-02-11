<?php
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
    <title>ダッシュボード</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 200px;
            background-color: rgb(57, 167, 152);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 10px;
            transition: transform 0.3s ease;
            padding-top: 70px; /* Space for the toggle button */
            position: fixed;
            height: 100%;
            z-index: 1050;
        }

        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 2000;
            font-size: 20px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .toggle-btn span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: #333;
            margin: 5px 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .toggle-btn.closed span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .toggle-btn.closed span:nth-child(2) {
            opacity: 0;
        }

        .toggle-btn.closed span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        .navbar-brand {
            margin-bottom: 0;
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center; /* Align text and icons vertically */
            padding: 10px;
            margin-left: 30px;
            font-size: 14px;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .nav-link i {
            font-size: 16px; /* Icon size */
            margin-right: 10px; /* Spacing between the icon and text */
        }
        .navbar-custom {
            margin-top: 10px;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

.content {
    flex: 1;
    overflow: hidden;
    margin-left: 200px; /* Add space for the sidebar */
    margin-top: 60px; /* Adjust to avoid overlapping with navbar */
}

iframe {
    width: 100%;
    height: calc(100vh - 60px); /* Adjust height to account for navbar */
    border: none;
}


        .navbar-custom .navbar-nav .nav-link {
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .navbar-toggler {
            position: absolute; /* Position the button absolutely within the navbar */
            top: 10px; /* Adjust the top position as needed */
            right: 10px; /* Adjust the right position as needed */
            z-index: 1000; /* Ensure it's above other elements */
            border: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }


        /* Mobile and small screen adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-220px); /* Initially hide sidebar */
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0); /* Show sidebar when opened */
            }

            .nav-link {
                display: block; /* Make nav-links display as block when the sidebar is open */
            }

            .content {
                margin-left: 0; /* Remove space for sidebar on small screens */
            }
        }

        /* Ensure the sidebar is always open on large screens */
        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0); /* Always show the sidebar on large screens */
            }

            .toggle-btn {
                display: none; /* Hide toggle button on large screens */
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a class="navbar-brand d-flex justify-content-center align-items-center" href="#">
            <img src="logo1.png" alt="Logo" style="width: 60px;">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="timetable.php" target="contentFrame" id="timetableLink">
                    <i class="fas fa-calendar-alt"></i> 時間割
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="attendance.php" target="contentFrame">
                    <i class="fas fa-clipboard-check"></i> 実績
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="teacher.php" target="contentFrame">
                    <i class="fas fa-user-tie"></i> 講師
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="subject.php" target="contentFrame">
                    <i class="fas fa-book"></i> 科目
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="invoice.php" target="contentFrame">
                    <i class="fas fa-file-invoice"></i> 請求書
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user.php" target="contentFrame">
                    <i class="fas fa-users"></i> ユーザー
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn btn-danger text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> ログアウト
                </a>
            </li>
        </ul>
    </nav>

 <!-- Top Navigation Bar (Always Visible) -->
<nav class="navbar navbar-custom" id="navbarCustom">
    <div class="container-fluid">
        <!-- Nav Links -->
        <div class="d-flex justify-content-center w-100  id="navbarNav">
            <ul class="navbar-nav flex-row">
                <li class="nav-item" style="background-color: rgb(57, 167, 152);">
                    <a class="nav-link" href="timetable1.php" target="contentFrame">
                        ITソリューション１年
                    </a>
                </li>
                <li class="nav-item" style="background-color: rgb(176, 15, 179);">
                    <a class="nav-link" href="timetable.php" target="contentFrame">
                        ITソリューション２年
                    </a>
                </li>
                <li class="nav-item" style="background-color: rgb(80, 25, 219);">
                    <a class="nav-link" href="timetable2.php" target="contentFrame">
                        グローバルビジネスー２年
                    </a>
                </li>
                <li class="nav-item" style="background-color: rgb(29, 167, 31);">
                    <a class="nav-link" href="timetable3.php" target="contentFrame">
                        地域観光デザイン－１年
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Content Area -->
    <div class="content">
        <button class="toggle-btn" id="toggleSidebar">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <iframe name="contentFrame" src="timetable.php"></iframe>
    </div>

    <!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle the sidebar visibility on small screens
        document.querySelector('#toggleSidebar').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('open');
            this.classList.toggle('closed');
        });

        // Show/hide the navbar when "時間割" is clicked
        document.querySelector('#timetableLink').addEventListener('click', function () {
            document.querySelector('#navbarCustom').style.display = 'block';
        });

        // Hide the navbar when other sidebar links are clicked
        const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
        sidebarLinks.forEach(link => {
            if (link.id !== 'timetableLink') {
                link.addEventListener('click', function () {
                    document.querySelector('#navbarCustom').style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>