<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/funciones.php';

$simulacion_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuario_id = $_SESSION['usuario_id'];

// Obtener datos de la simulación
$simulacion = obtenerDatosSimulacion($simulacion_id, $usuario_id);

if (!$simulacion) {
    die('Simulación no encontrada o no autorizada');
}

// Obtener historial de interacciones
$historial = obtenerHistorialInteracciones($simulacion_id);

// Calcular métricas básicas
$total_preguntas = 0;
$total_respuestas = 0;
$tiempo_total = time() - strtotime($simulacion['fecha_inicio']);

foreach ($historial as $interaccion) {
    if ($interaccion['tipo'] == 'pregunta') {
        $total_preguntas++;
    } elseif ($interaccion['tipo'] == 'respuesta') {
        $total_respuestas++;
    }
}

// Calcular puntuación aproximada
$puntuacion = min(100, max(0, 
    ($total_preguntas * 10) + 
    (rand(70, 90)) // Simulación de precisión diagnóstica
));

// Finalizar simulación
finalizarSimulacion($simulacion_id, $puntuacion, $tiempo_total);

// Guardar reporte
$datos_reporte = [
    'precision_diagnostica' => rand(75, 95),
    'habilidades_comunicacion' => rand(80, 90),
    'tiempo_respuesta_promedio' => rand(30, 120),
    'areas_mejora' => 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares',
    'recomendaciones' => 'Practicar más casos de ' . $simulacion['especialidad'],
    'analisis_completo' => [
        'total_interacciones' => count($historial),
        'preguntas_relevantes' => $total_preguntas,
        'tiempo_simulacion' => $tiempo_total,
        'dificultad' => $simulacion['dificultad']
    ]
];

guardarReporte($usuario_id, $simulacion_id, $datos_reporte);

// Obtener ID del reporte recién creado
$pdo = getDB();
$stmt = $pdo->prepare("SELECT id FROM reportes WHERE simulacion_id = ? ORDER BY fecha_generacion DESC LIMIT 1");
$stmt->execute([$simulacion_id]);
$reporte = $stmt->fetch();
$reporte_id = $reporte ? $reporte['id'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Simulación - EduMedIA</title>
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
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="reporte-header">
                <div>
                    <h1 class="card-title">Reporte de Simulación</h1>
                    <p style="font-size: 1.2rem; color: #666;">
                        <i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($simulacion['titulo']); ?>
                    </p>
                </div>
                <div>
                    <a href="dashboard.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>

            <div class="reporte-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $puntuacion; ?>%</div>
                    <div class="stat-name">Puntuación Final</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $total_preguntas; ?></div>
                    <div class="stat-name">Preguntas Realizadas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo round($tiempo_total / 60, 1); ?> min</div>
                    <div class="stat-name">Tiempo de Simulación</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo ucfirst($simulacion['dificultad']); ?></div>
                    <div class="stat-name">Dificultad</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div class="reporte-card">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <i class="fas fa-chart-line"></i> Métricas de Desempeño
                    </h3>
                    <div style="space-y: 1rem;">
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Precisión Diagnóstica</span>
                                <span><?php echo $datos_reporte['precision_diagnostica']; ?>%</span>
                            </div>
                            <div style="height: 10px; background: #e1e5eb; border-radius: 5px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $datos_reporte['precision_diagnostica']; ?>%; background: var(--success-color); border-radius: 5px;"></div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Habilidades Comunicativas</span>
                                <span><?php echo $datos_reporte['habilidades_comunicacion']; ?>%</span>
                            </div>
                            <div style="height: 10px; background: #e1e5eb; border-radius: 5px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $datos_reporte['habilidades_comunicacion']; ?>%; background: var(--info-color); border-radius: 5px;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Tiempo de Respuesta Promedio</span>
                                <span><?php echo $datos_reporte['tiempo_respuesta_promedio']; ?>s</span>
                            </div>
                            <div style="height: 10px; background: #e1e5eb; border-radius: 5px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo min(100, $datos_reporte['tiempo_respuesta_promedio']); ?>%; background: var(--warning-color); border-radius: 5px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reporte-card">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <i class="fas fa-lightbulb"></i> Áreas de Mejora
                    </h3>
                    <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ffc107;">
                        <p style="margin: 0; line-height: 1.6;">
                            <strong>Recomendaciones:</strong><br>
                            <?php echo htmlspecialchars($datos_reporte['areas_mejora']); ?>
                        </p>
                    </div>
                    
                    <h3 style="color: var(--primary-color); margin: 2rem 0 1rem 0;">
                        <i class="fas fa-graduation-cap"></i> Sugerencias de Estudio
                    </h3>
                    <div style="background: #d1ecf1; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #0dcaf0;">
                        <p style="margin: 0; line-height: 1.6;">
                            <?php echo htmlspecialchars($datos_reporte['recomendaciones']); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="reporte-card" style="margin-top: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-history"></i> Historial de la Simulación
                </h3>
                <div style="max-height: 300px; overflow-y: auto; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <?php foreach($historial as $mensaje): ?>
                        <?php if($mensaje['tipo'] == 'pregunta'): ?>
                            <div style="margin-bottom: 1rem; padding: 0.8rem; background: #e8f5e9; border-radius: 8px; border-left: 3px solid #4caf50;">
                                <strong style="color: #2e7d32;">Tú:</strong> <?php echo htmlspecialchars($mensaje['contenido']); ?>
                                <div style="font-size: 0.8rem; color: #666; margin-top: 0.3rem;">
                                    <?php echo date('H:i:s', strtotime($mensaje['timestamp'])); ?>
                                </div>
                            </div>
                        <?php elseif($mensaje['tipo'] == 'respuesta'): ?>
                            <div style="margin-bottom: 1rem; padding: 0.8rem; background: #e3f2fd; border-radius: 8px; border-left: 3px solid #2196f3;">
                                <strong style="color: #1565c0;">Paciente:</strong> <?php echo htmlspecialchars($mensaje['contenido']); ?>
                                <div style="font-size: 0.8rem; color: #666; margin-top: 0.3rem;">
                                    <?php echo date('H:i:s', strtotime($mensaje['timestamp'])); ?>
                                </div>
                            </div>
                        <?php elseif($mensaje['tipo'] == 'feedback'): ?>
                            <div style="margin-bottom: 1rem; padding: 0.8rem; background: #fff3cd; border-radius: 8px; border-left: 3px solid #ff9800;">
                                <strong style="color: #e65100;">Mentor IA:</strong> <?php echo htmlspecialchars($mensaje['contenido']); ?>
                                <div style="font-size: 0.8rem; color: #666; margin-top: 0.3rem;">
                                    <?php echo date('H:i:s', strtotime($mensaje['timestamp'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="simulacion.php" class="btn btn-primary" style="margin: 0 1rem;">
                    <i class="fas fa-plus"></i> Nueva Simulación
                </a>
                <a href="reportes.php" class="btn btn-outline" style="margin: 0 1rem;">
                    <i class="fas fa-chart-bar"></i> Ver Todos los Reportes
                </a>
            </div>
        </div>
    </div>
</body>
</html>