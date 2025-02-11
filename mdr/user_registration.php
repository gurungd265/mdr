<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
@include 'db_connection.php';

$message = []; // Initialize message array

if (isset($_POST['submit'])) {

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
    $cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    } else {
        // Check if email already exists
        $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select->bind_param("s", $email);
        $select->execute();
        $result = $select->get_result();

        if ($result->num_rows > 0) {
            $message[] = 'User email already exists!';
        } else {
            // Check if passwords match
            if ($pass !== $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                // Insert user into the database
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT); // Hashing password
                $insert = $conn->prepare("INSERT INTO `users` (name, email, password) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $name, $email, $hashed_pass);

                if ($insert->execute()) {
                    header('Location: login.php?success=Registered successfully!');
                    exit;
                } else {
                    $message[] = 'Registration failed, try again.';
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            background-color: #f7f8fa;
        }
        .btn-iica{
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
            margin-bottom: 20px;
        }
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .btn {
            width: 100%;
            padding: 10px;
        }

        .btn-primary {
            background-color: green !important;
            border-color: green !important;
        }

        .btn-primary:hover {
            background-color: darkgreen !important;
            border-color: darkgreen !important;
        }
    </style>
</head>

<body>

<?php
// Display success message
if (isset($_GET['success'])) {
    echo '
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successMessage">
        ' . htmlspecialchars($_GET['success']) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    ';
}

// Display error messages
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . $msg . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        ';
    }
}
?>

<section class="form-container">
    <form action="" method="POST" class="needs-validation" novalidate>

        <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input type="text" name="name" id="name" class="form-control" required>
            <div class="invalid-feedback">
                Please enter your name.
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">メール</label>
            <input type="email" name="email" id="email" class="form-control" required>
            <div class="invalid-feedback">
                Please provide a valid email.
            </div>
        </div>

        <div class="mb-3">
            <label for="pass" class="form-label">パスワード</label>
            <div class="input-group mb-3">
                <input type="password" name="pass" id="pass" class="form-control" required>
                <span class="input-group-text">
                    <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                </span>
                <div class="invalid-feedback">
                    Please enter your password.
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="cpass" class="form-label">パスワードの確認</label>
            <div class="input-group mb-3">
                <input type="password" name="cpass" id="cpass" class="form-control" required>
                <span class="input-group-text">
                    <i class="fa fa-eye" id="togglePasswordConfirm" style="cursor: pointer;"></i>
                </span>
                <div class="invalid-feedback">
                    Please confirm your password.
                </div>
            </div>
        </div>

        <input type="submit" value="登録" class="btn  mt-3 btn-iica" name="submit">
        <p class="form-text text-center">既に登録されている方? <a href="login.php">ログイン</a></p>
    </form>
</section>

<!-- Bootstrap 5 Validation Script -->
<script>
    (function () {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
    })();
</script>

<script>
    // Toggle password visibility
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#pass');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    // Automatically hide the success message after 5 seconds
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(() => {
            successMessage.classList.add('fade'); // Add Bootstrap fade class
            setTimeout(() => successMessage.remove(), 5000); // Remove from DOM after fade
        }, 5000); // 5 seconds
    }
</script>

</body>

</html>