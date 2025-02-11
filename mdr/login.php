<?php

@include 'db_connection.php';

session_start();

if (isset($_POST['submit'])) {
    // Sanitize and validate email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    } else {
        $pass = $_POST['pass']; // Raw password entered by the user

        // SQL query to fetch the user's data using mysqli
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // If user exists and the password matches
        if ($row && password_verify($pass, $row['password'])) {
            // Check user type and redirect accordingly
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_id'] = $row['id'];
                session_regenerate_id(); // Prevent session hijacking
                echo '<div class="alert alert-success" id="successMessage">
                        Login successfully!
                      </div>';
                echo '<script>
                        setTimeout(function() {
                            document.getElementById("successMessage").style.display = "none";
                            window.location.href = "/graduation/admin.php";
                        }, 1000);
                      </script>';
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_id'] = $row['id'];
                session_regenerate_id(); // Prevent session hijacking
                echo '<div class="alert alert-success" id="successMessage">
                        Login successfully!
                      </div>';
                echo '<script>
                        setTimeout(function() {
                            document.getElementById("successMessage").style.display = "none";
                            window.location.href = "/graduation/home.php";
                        }, 1000);
                      </script>';
            } else {
                $message[] = 'No user found!';
            }
        } else {
            $message[] = 'Incorrect email or password!';
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
    <title>Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color: #f1f3f5;
        }
        h1{
            text-align: center;
            color:rgb(57, 167, 152);
        }
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #842029;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
        }
        .form-text {
            margin-top: 10px;
        }

        .btn-primary {
    background-color: rgb(57, 167, 152)!important;
    border-color: rgb(57, 167, 152) !important;
}

.btn-primary:hover {
    background-color:teal !important;
    border-color: teal !important;
}
    </style>
</head>
<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container">
        <!-- ロゴを左側に配置 -->
        <a class="navbar-brand" href="#">
            <img src="logo.jpg" alt="Logo" style="width: 300px;">
        </a>
    </div>
</nav>

<h1>管理者ログイン</h1>
<body>
<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> ' . $msg . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        ';
    }
}
?>

<section class="form-container">

    <form action="" method="POST" class="needs-validation" novalidate>


        <div class="mb-3">
            <label for="email" class="form-label">メール</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="メール" required>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>

        <div class="mb-3">
            <label for="pass" class="form-label">パスワード</label>
            <div class="input-group mb-3">
            <input type="password" name="pass" id="pass" class="form-control" placeholder="パスワードを入力" required>
            <span class="input-group-text">
                <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </span>
            <div class="invalid-feedback">
            Please enter your password.
            </div>
        </div>

        <input type="submit" value="ログイン" class="btn btn-primary" name="submit">

        <!-- <p class="form-text">ログイン登録<a href="register.php">こちら</a></p> -->
        <p class="form-text">パスワード忘れての方 <a href="forgot_password.php">こちら</a></p>
    </form>

</section>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Form validation using Bootstrap's built-in validation classes
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })();
</script>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#pass');

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the eye icon
        this.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>
