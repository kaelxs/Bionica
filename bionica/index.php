<?php
session_start();
require_once 'includes/funciones.php';

$logueado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema BIONICA - Plataforma Educativa Médica con IA</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-brain logo-icon"></i>
                BIONICA
            </a>
            <ul class="nav-links">
                <?php if($logueado): ?>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="simulacion.php"><i class="fas fa-stethoscope"></i> Simulaciones</a></li>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                    <li><a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h1 class="card-title">Bienvenido a BIONICA</h1>
            <p style="font-size: 1.2rem; line-height: 1.8;">
                La plataforma de simulación clínica con inteligencia artificial para estudiantes de salud. 
                Practica diagnósticos, mejora tus habilidades comunicativas y recibe retroalimentación 
                personalizada de nuestros agentes inteligentes.
            </p>
            
            <?php if(!$logueado): ?>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="registro.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                        <i class="fas fa-rocket"></i> ¡Regístrate Gratis!
                    </a>
                    <p style="margin-top: 1rem; font-size: 1.1rem;">
                        ¿Ya tienes cuenta? <a href="login.php" style="color: var(--secondary-color); font-weight: 500;">Inicia sesión</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-number">150+</div>
                <div class="stat-label">Casos Clínicos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">10,000+</div>
                <div class="stat-label">Simulaciones Realizadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">98%</div>
                <div class="stat-label">Satisfacción de Usuarios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Disponibilidad</div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">¿Cómo Funciona?</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Pacientes Virtuales</h3>
                    <p>Interactúa con pacientes simulados que responden de forma realista gracias a nuestra IA avanzada.</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Agentes Inteligentes</h3>
                    <p>Nuestros agentes IA analizan tu desempeño y te proporcionan retroalimentación inmediata.</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Seguimiento de Progreso</h3>
                    <p>Monitorea tu evolución con reportes detallados y estadísticas personalizadas.</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Especialidades Disponibles</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 1rem;">
                <span class="badge badge-basico">Cardiología</span>
                <span class="badge badge-basico">Pediatría</span>
                <span class="badge badge-basico">Neumología</span>
                <span class="badge badge-basico">Gastroenterología</span>
                <span class="badge badge-basico">Neurología</span>
                <span class="badge badge-basico">Dermatología</span>
                <span class="badge badge-basico">Traumatología</span>
                <span class="badge badge-basico">Psiquiatría</span>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>