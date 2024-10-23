<?php
// Session Start
session_start();

// Load users to an array
$usersFile = file_get_contents('files/users.json');
$usersArray = json_decode($usersFile, true);

// If the request is post (data submit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check user and password
    if(isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        // control login var
        $validLogin = false;

        // check if user in the usersfile
        foreach ($usersArray['users'] as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                // Store username in session
                $_SESSION['username'] = $username;
                // control login var
                $validLogin = true;
                break;
            }
        }

        // if valid login redirect to dashboard
        if ($validLogin) {
            header("Location: dashboard.php");
            exit(); 
        } else {
            // if invalid set error
            $error_message = "Credenciais inválidas. Por favor, tente novamente.";
        }
    } else {
        // if empty set error
        $error_message = "Por favor, preencha os campos de username e password.";
    }
}
?>





<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Import bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>TechnoGreen - Login</title>
</head>
<body class="bg-light">

<!-- login container -->
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-100">
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-success text-white p-4 rounded-start">
            <div>
                <h1>TechnoGreen</h1>
                <p>A tecnologia que planta o futuro!</p>
            </div>
        </div>

        <div class="col-md-6 bg-white p-5 rounded-end shadow">
            <h1 class="text-center mb-4">Login</h1>
            <form method="post" accept-charset="utf-8">
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username" id="username" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <!-- Submit button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>

                <!-- Show error msg if set -->
                <?php
                if (isset($error_message)) {
                    echo '<div class="alert alert-danger mt-3">' . $error_message . '</div>';
                }
                ?>
            </form>
        </div>
    </div>
</div>
