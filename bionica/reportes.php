<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/funciones.php';

$usuario_id = $_SESSION['usuario_id'];
$reporte_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($reporte_id > 0) {
    // Mostrar reporte específico
    $reporte = obtenerReporte($reporte_id);
    if (!$reporte || $reporte['usuario_id'] != $usuario_id) {
        die('Reporte no encontrado o no autorizado');
    }
    
    $analisis = json_decode($reporte['datos_analisis'], true);
} else {
    // Mostrar lista de reportes
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT r.*, c.titulo as caso_titulo, c.especialidad, c.dificultad, s.fecha_inicio
        FROM reportes r
        JOIN simulaciones s ON r.simulacion_id = s.id
        JOIN casos_clinicos c ON s.caso_id = c.id
        WHERE r.usuario_id = ?
        ORDER BY r.fecha_generacion DESC
    ");
    $stmt->execute([$usuario_id]);
    $reportes = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - EduMedIA</title>
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
                <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="reportes.php" class="active"><i class="fas fa-chart-bar"></i> Reportes</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <?php if ($reporte_id > 0 && isset($reporte)): ?>
            <!-- Vista de reporte individual -->
            <div class="card">
                <div class="reporte-header">
                    <div>
                        <h1 class="card-title">Reporte Detallado</h1>
                        <p style="font-size: 1.2rem; color: #666;">
                            <i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($reporte['caso_titulo']); ?>
                        </p>
                    </div>
                    <div>
                        <a href="reportes.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Volver a Reportes
                        </a>
                    </div>
                </div>

                <div class="reporte-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $reporte['precision_diagnostica']; ?>%</div>
                        <div class="stat-name">Precisión Diagnóstica</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $reporte['habilidades_comunicacion']; ?>%</div>
                        <div class="stat-name">Comunicación</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo round($reporte['tiempo_respuesta_promedio']); ?>s</div>
                        <div class="stat-name">Tiempo Promedio</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo date('d/m/Y', strtotime($reporte['fecha_generacion'])); ?></div>
                        <div class="stat-name">Fecha</div>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <i class="fas fa-lightbulb"></i> Áreas de Mejora
                    </h3>
                    <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ffc107;">
                        <p style="margin: 0; line-height: 1.6;">
                            <?php echo htmlspecialchars($reporte['areas_mejora']); ?>
                        </p>
                    </div>
                    
                    <h3 style="color: var(--primary-color); margin: 2rem 0 1rem 0;">
                        <i class="fas fa-graduation-cap"></i> Recomendaciones
                    </h3>
                    <div style="background: #d1ecf1; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #0dcaf0;">
                        <p style="margin: 0; line-height: 1.6;">
                            <?php echo htmlspecialchars($reporte['recomendaciones']); ?>
                        </p>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Lista de reportes -->
            <div class="card">
                <h1 class="card-title">Mis Reportes</h1>
                <p>Historial completo de tus simulaciones y evaluaciones.</p>
            </div>

            <?php if(empty($reportes)): ?>
                <div class="card">
                    <p>No tienes reportes aún. <a href="simulacion.php">¡Comienza una simulación!</a></p>
                </div>
            <?php else: ?>
                <div class="card">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f8f9fa; text-align: left;">
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Caso</th>
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Especialidad</th>
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Dificultad</th>
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Precisión</th>
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Comunicación</th>
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Fecha</th>
                                    <th style="padding: 1rem; border-bottom: 2px solid #dee2e6;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reportes as $rep): ?>
                                <tr style="transition: background-color 0.3s;">
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <?php echo htmlspecialchars($rep['caso_titulo']); ?>
                                    </td>
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <span style="background-color: #e3f2fd; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                            <?php echo htmlspecialchars($rep['especialidad']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <span class="badge badge-<?php echo $rep['dificultad']; ?>">
                                            <?php echo ucfirst($rep['dificultad']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <div style="display: flex; align-items: center;">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: conic-gradient(#27ae60 <?php echo $rep['precision_diagnostica']; ?>%, #e1e5eb 0%); display: flex; align-items: center; justify-content: center; margin-right: 0.5rem;">
                                                <span style="font-size: 0.7rem; font-weight: bold;"><?php echo round($rep['precision_diagnostica']); ?>%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <div style="display: flex; align-items: center;">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: conic-gradient(#3498db <?php echo $rep['habilidades_comunicacion']; ?>%, #e1e5eb 0%); display: flex; align-items: center; justify-content: center; margin-right: 0.5rem;">
                                                <span style="font-size: 0.7rem; font-weight: bold;"><?php echo round($rep['habilidades_comunicacion']); ?>%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <?php echo date('d/m/Y H:i', strtotime($rep['fecha_generacion'])); ?>
                                    </td>
                                    <td style="padding: 1rem; border-bottom: 1px solid #e9ecef;">
                                        <a href="reportes.php?id=<?php echo $rep['id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>