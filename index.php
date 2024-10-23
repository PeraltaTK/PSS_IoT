<?php
// Session Start
session_start();

// Carregar utilizadores para uma matriz
$usersFile = file_get_contents('data/users.json');
$usersArray = json_decode($usersFile, true);

// Se o pedido for post (envio de dados)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verificar o utilizador e a palavra-passe
    if(isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Definir login válido
        $validLogin = false;

        // verificar se o utilizador está no ficheiro usersfile
        foreach ($usersArray['users'] as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                // Armazenar o nome de utilizador na sessão
                $_SESSION['username'] = $username;
                // Definir login válido
                $validLogin = true;
                break;
            }
        }

        // se o login for válido, redirecionar para o painel de controlo
        if ($validLogin) {
            header("Location: dashboard.php");
            exit(); 
        } else {
           // se inválido, definir erro
            $error_message = "Credenciais inválidas. Por favor, tente novamente.";
        }
    } else {
        // se erro de conjunto vazio
        $error_message = "Por favor, preencha os campos de username e password.";
    }
}
?>



<!DOCTYPE html>
<html lang="pt">
<head>
<?php include 'style/head.php'; ?>
</head>
<body class="bg-light">

<!-- contentor de início de sessão -->
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
                <!-- Nome de utilizador -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username" id="username" required>
                    </div>
                </div>

                <!-- Palavra-passe -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <!-- Botão Enviar -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>

                <!-- Mostrar mensagem de erro se definido -->
                <?php
                if (isset($error_message)) {
                    echo '<div class="alert alert-danger mt-3">' . $error_message . '</div>';
                }
                ?>
            </form>
        </div>
    </div>
</div>
