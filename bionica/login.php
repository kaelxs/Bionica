<?php
session_start();
require_once 'includes/funciones.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (iniciarSesion($email, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Email o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - EduMedIA</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-brain logo-icon"></i>
                EduMedIA
            </a>
            <ul class="nav-links">
                <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card" style="max-width: 500px; margin: 2rem auto;">
            <h2 class="card-title">Iniciar Sesión</h2>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
            
            <p style="margin-top: 1.5rem; text-align: center;">
                ¿No tienes cuenta? <a href="registro.php" style="color: var(--secondary-color); font-weight: 500;">Regístrate aquí</a>
            </p>
        </div>
    </div>
</body>
</html>