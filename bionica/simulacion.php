<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/funciones.php';

$dificultad = isset($_GET['dificultad']) ? $_GET['dificultad'] : null;
$especialidad = isset($_GET['especialidad']) ? $_GET['especialidad'] : null;
$casos = obtenerCasosClinicos($dificultad, $especialidad);

// Obtener especialidades únicas
$pdo = getDB();
$stmt = $pdo->query("SELECT DISTINCT especialidad FROM casos_clinicos WHERE activo = 1 ORDER BY especialidad");
$especialidades = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulaciones - EduMedIA</title>
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
                <li><a href="simulacion.php" class="active"><i class="fas fa-stethoscope"></i> Simulaciones</a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h1 class="card-title">Casos Clínicos Disponibles</h1>
            <p>Selecciona un caso para comenzar una simulación clínica realista.</p>
            
            <div style="margin: 1.5rem 0; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <strong>Filtros:</strong>
                
                <a href="simulacion.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </a>
                
                <div style="position: relative;">
                    <select onchange="location.href=this.value" class="form-control" style="padding: 0.5rem 1rem; min-width: 150px;">
                        <option value="simulacion.php">Dificultad: Todas</option>
                        <option value="simulacion.php?dificultad=basico" <?php echo $dificultad == 'basico' ? 'selected' : ''; ?>>Básico</option>
                        <option value="simulacion.php?dificultad=intermedio" <?php echo $dificultad == 'intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                        <option value="simulacion.php?dificultad=avanzado" <?php echo $dificultad == 'avanzado' ? 'selected' : ''; ?>>Avanzado</option>
                    </select>
                </div>
                
                <div style="position: relative;">
                    <select onchange="location.href=this.value" class="form-control" style="padding: 0.5rem 1rem; min-width: 180px;">
                        <option value="simulacion.php">Especialidad: Todas</option>
                        <?php foreach($especialidades as $esp): ?>
                        <option value="simulacion.php?especialidad=<?php echo urlencode($esp); ?>" <?php echo $especialidad == $esp ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($esp); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="casos-grid">
            <?php if(empty($casos)): ?>
                <div class="card">
                    <p>No se encontraron casos clínicos con los filtros seleccionados.</p>
                    <a href="simulacion.php" class="btn btn-outline">Limpiar filtros</a>
                </div>
            <?php else: ?>
                <?php foreach($casos as $caso): ?>
                <div class="caso-card">
                    <h3 class="caso-titulo"><?php echo htmlspecialchars($caso['titulo']); ?></h3>
                    <p class="caso-descripcion"><?php echo htmlspecialchars(substr($caso['descripcion'], 0, 120)) . '...'; ?></p>
                    
                    <div class="caso-detalles">
                        <span><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($caso['especialidad']); ?></span>
                        <span><i class="fas fa-user"></i> <?php echo $caso['edad_paciente']; ?> años</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <span class="badge badge-<?php echo $caso['dificultad']; ?>">
                            <?php echo ucfirst($caso['dificultad']); ?>
                        </span>
                        <a href="iniciar_simulacion.php?id=<?php echo $caso['id']; ?>" class="btn btn-success">
                            <i class="fas fa-play"></i> Iniciar
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>