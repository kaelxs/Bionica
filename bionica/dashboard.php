<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/funciones.php';

$usuario_id = $_SESSION['usuario_id'];
$estadisticas = obtenerEstadisticasUsuario($usuario_id);
$historial = obtenerHistorialSimulaciones($usuario_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EduMedIA</title>
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="simulacion.php"><i class="fas fa-stethoscope"></i> Simulaciones</a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h1 class="card-title">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
            <p style="font-size: 1.1rem;">Aquí puedes ver tu progreso y comenzar nuevas simulaciones.</p>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_simulaciones']; ?></div>
                <div class="stat-label">Simulaciones Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['simulaciones_completadas']; ?></div>
                <div class="stat-label">Completadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['promedio_puntuacion']; ?>%</div>
                <div class="stat-label">Promedio de Puntuación</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $estadisticas['ultima_actividad'] ? 
                        date('d/m/Y', strtotime($estadisticas['ultima_actividad'])) : 'N/A'; ?>
                </div>
                <div class="stat-label">Última Actividad</div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Últimas Simulaciones</h2>
            <?php if(empty($historial)): ?>
                <p>No tienes simulaciones aún. <a href="simulacion.php">¡Comienza una nueva!</a></p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; text-align: left;">
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Caso</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Especialidad</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Dificultad</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Fecha</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Estado</th>
                                <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Puntuación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($historial, 0, 5) as $sim): ?>
                            <tr style="transition: background-color 0.3s;">
                                <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;"><?php echo htmlspecialchars($sim['caso_titulo']); ?></td>
                                <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                    <span style="background-color: #e3f2fd; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                        <?php echo htmlspecialchars($sim['especialidad']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                    <span class="badge badge-<?php echo $sim['dificultad']; ?>">
                                        <?php echo ucfirst($sim['dificultad']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                    <?php echo date('d/m/Y H:i', strtotime($sim['fecha_inicio'])); ?>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                    <span class="badge <?php echo $sim['estado'] == 'completada' ? 'badge-success' : ($sim['estado'] == 'en_progreso' ? 'badge-warning' : 'badge-danger'); ?>">
                                        <?php echo ucfirst($sim['estado']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                    <?php echo $sim['puntuacion'] ? $sim['puntuacion'].'%' : 'N/A'; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1.5rem; text-align: center;">
                    <a href="simulacion.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Simulación
                    </a>
                    <a href="reportes.php" class="btn btn-outline" style="margin-left: 1rem;">
                        <i class="fas fa-chart-bar"></i> Ver Todos los Reportes
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 class="card-title">Próximos Pasos</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                <div style="border: 1px solid #e1e5eb; border-radius: 8px; padding: 1.5rem; text-align: center;">
                    <div style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-book-medical"></i>
                    </div>
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Explora Casos</h3>
                    <p>Descubre nuevos casos clínicos para practicar diferentes especialidades.</p>
                    <a href="simulacion.php" class="btn btn-outline" style="margin-top: 1rem;">Ver Casos</a>
                </div>
                <div style="border: 1px solid #e1e5eb; border-radius: 8px; padding: 1.5rem; text-align: center;">
                    <div style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Analiza tu Progreso</h3>
                    <p>Revisa tus reportes y estadísticas para identificar áreas de mejora.</p>
                    <a href="reportes.php" class="btn btn-outline" style="margin-top: 1rem;">Ver Reportes</a>
                </div>
                <div style="border: 1px solid #e1e5eb; border-radius: 8px; padding: 1.5rem; text-align: center;">
                    <div style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Configura tu Perfil</h3>
                    <p>Personaliza tu experiencia y ajusta tus preferencias de aprendizaje.</p>
                    <a href="perfil.php" class="btn btn-outline" style="margin-top: 1rem;">Ir al Perfil</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>