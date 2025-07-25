<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/funciones.php';

$usuario_id = $_SESSION['usuario_id'];
$pdo = getDB();

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $nivel = $_POST['nivel'];
    
    if (!empty($nombre)) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, nivel = ? WHERE id = ?");
        $stmt->execute([$nombre, $nivel, $usuario_id]);
        
        $_SESSION['nombre'] = $nombre;
        $_SESSION['nivel'] = $nivel;
        
        $mensaje = "Perfil actualizado correctamente";
        $usuario['nombre'] = $nombre;
        $usuario['nivel'] = $nivel;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - EduMedIA</title>
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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="simulacion.php"><i class="fas fa-stethoscope"></i> Simulaciones</a></li>
                <li><a href="perfil.php" class="active"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h1 class="card-title">Mi Perfil</h1>
            
            <?php if(isset($mensaje)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem; margin-top: 2rem;">
                <div style="text-align: center;">
                    <img src="assets/avatares/<?php echo $usuario['avatar']; ?>" 
                         alt="Avatar" 
                         style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid var(--secondary-color); margin-bottom: 1rem;">
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($usuario['nombre']); ?></h3>
                    <p style="color: #666;">
                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario['email']); ?>
                    </p>
                    <p style="color: #666; margin-top: 0.5rem;">
                        <i class="fas fa-calendar-alt"></i> Miembro desde <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                    </p>
                </div>
                
                <div>
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre Completo:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                            <small style="color: #666;">El email no puede modificarse</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="nivel" class="form-label">Nivel de Conocimiento:</label>
                            <select id="nivel" name="nivel" class="form-control">
                                <option value="basico" <?php echo $usuario['nivel'] == 'basico' ? 'selected' : ''; ?>>Básico</option>
                                <option value="intermedio" <?php echo $usuario['nivel'] == 'intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                                <option value="avanzado" <?php echo $usuario['nivel'] == 'avanzado' ? 'selected' : ''; ?>>Avanzado</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                    
                    <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">
                            <i class="fas fa-shield-alt"></i> Seguridad
                        </h4>
                        <p>Para cambiar tu contraseña, contacta al administrador del sistema.</p>
                        <a href="#" class="btn btn-outline" style="margin-top: 1rem;">
                            <i class="fas fa-key"></i> Solicitar Cambio de Contraseña
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2 class="card-title">Configuración de Preferencias</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                <div>
                    <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                        <i class="fas fa-bell"></i> Notificaciones
                    </h4>
                    <label style="display: block; margin: 0.5rem 0;">
                        <input type="checkbox" checked> Recordatorios de simulación
                    </label>
                    <label style="display: block; margin: 0.5rem 0;">
                        <input type="checkbox"> Nuevos casos clínicos
                    </label>
                    <label style="display: block; margin: 0.5rem 0;">
                        <input type="checkbox" checked> Reportes semanales
                    </label>
                </div>
                
                <div>
                    <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                        <i class="fas fa-paint-brush"></i> Apariencia
                    </h4>
                    <label style="display: block; margin: 0.5rem 0;">
                        <input type="radio" name="tema" checked> Tema claro
                    </label>
                    <label style="display: block; margin: 0.5rem 0;">
                        <input type="radio" name="tema"> Tema oscuro
                    </label>
                </div>
                
                <div>
                    <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                        <i class="fas fa-language"></i> Idioma
                    </h4>
                    <select class="form-control">
                        <option>Español</option>
                        <option>English</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</body>
</html>