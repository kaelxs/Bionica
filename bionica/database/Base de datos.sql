-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-07-2025 a las 00:32:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `edumedia_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_ultimo_acceso` (IN `usuario_id` INT)   BEGIN
    UPDATE usuarios 
    SET ultimo_acceso = NOW() 
    WHERE id = usuario_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_casos_por_especialidad` (IN `especialidad_nombre` VARCHAR(100))   BEGIN
    SELECT 
        id,
        titulo,
        dificultad,
        descripcion,
        edad_paciente,
        genero_paciente
    FROM casos_clinicos 
    WHERE especialidad = especialidad_nombre AND activo = 1
    ORDER BY dificultad, titulo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_progreso_usuario` (IN `usuario_id` INT)   BEGIN
    SELECT 
        COUNT(*) as total_simulaciones,
        COUNT(CASE WHEN estado = 'completada' THEN 1 END) as completadas,
        AVG(puntuacion) as promedio_puntuacion,
        MAX(fecha_inicio) as ultima_actividad
    FROM simulaciones 
    WHERE usuario_id = usuario_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casos_clinicos`
--

CREATE TABLE `casos_clinicos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `dificultad` enum('basico','intermedio','avanzado') DEFAULT NULL,
  `edad_paciente` int(11) DEFAULT NULL,
  `genero_paciente` enum('masculino','femenino','otro') DEFAULT NULL,
  `sintomas_principales` text DEFAULT NULL,
  `historia_clinica` text DEFAULT NULL,
  `datos_json` longtext DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `creador_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `casos_clinicos`
--

INSERT INTO `casos_clinicos` (`id`, `titulo`, `descripcion`, `especialidad`, `dificultad`, `edad_paciente`, `genero_paciente`, `sintomas_principales`, `historia_clinica`, `datos_json`, `activo`, `fecha_creacion`, `fecha_actualizacion`, `creador_id`) VALUES
(1, 'Dolor Torácico Agudo', 'Paciente masculino de 45 años con dolor torácico intenso que comenzó repentinamente durante ejercicio físico. Presenta sudoración profusa y náuseas.', 'Cardiología', 'intermedio', 45, 'masculino', 'Dolor torácico, sudoración, náuseas', 'Hipertenso, fumador, antecedentes familiares de infarto', '{\"sintomas\":[\"dolor toracico\",\"sudoracion\",\"nauseas\",\"dificultad respiratoria\"],\"diagnosticos_posibles\":[\"IAM\",\"angina\",\"disecion aortica\"],\"respuestas\":{\"ansiedad\":\"Moderada\",\"dolor\":\"8/10\"},\"historia_clinica\":\"Hipertenso, fumador, antecedentes familiares de infarto\",\"medicamentos\":[\"Enalapril\",\"Aspirina\"],\"factores_riesgo\":[\"edad\",\"sexo\",\"tabaquismo\",\"hipertension\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(2, 'Cefalea Intensa en Niño', 'Niño de 8 años que presenta dolor de cabeza severo que comenzó hace 12 horas. Acompañado de vómitos y fiebre. No tiene antecedentes médicos relevantes.', 'Pediatría', 'basico', 8, 'masculino', 'Dolor de cabeza, vómitos, fiebre', 'Sin antecedentes relevantes', '{\"sintomas\":[\"cefalea\",\"vomitos\",\"fiebre\",\"rigidez nuca\"],\"diagnosticos_posibles\":[\"migraña\",\"meningitis\",\"tumor cerebral\"],\"respuestas\":{\"ansiedad\":\"Baja\",\"dolor\":\"7/10\"},\"historia_clinica\":\"Sin antecedentes relevantes\",\"medicamentos\":[],\"factores_riesgo\":[\"edad\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(3, 'Dificultad Respiratoria en Anciano', 'Anciano de 75 años con disnea progresiva durante las últimas 48 horas. Tos seca persistente y fatiga importante. Historia de EPOC y diabetes tipo 2.', 'Neumología', 'avanzado', 75, 'masculino', 'Disnea, tos seca, fatiga', 'EPOC diagnosticada hace 10 años, diabetes tipo 2', '{\"sintomas\":[\"disnea\",\"tos seca\",\"fatiga\",\"sibilancias\"],\"diagnosticos_posibles\":[\"EPOC exacerbado\",\"insuficiencia cardiaca\",\"neumonia\"],\"respuestas\":{\"ansiedad\":\"Alta\",\"dolor\":\"Leve\"},\"historia_clinica\":\"EPOC diagnosticada hace 10 años, diabetes tipo 2\",\"medicamentos\":[\"Salbutamol\",\"Metformina\",\"Furosemida\"],\"factores_riesgo\":[\"edad\",\"EPOC\",\"diabetes\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(4, 'Dolor Abdominal en Mujer Joven', 'Mujer de 25 años con dolor abdominal intenso en cuadrante inferior derecho. Dolor iniciado hace 8 horas, empeora con el movimiento. Acompañado de náuseas.', 'Gastroenterología', 'intermedio', 25, 'femenino', 'Dolor abdominal, náuseas, fiebre', 'Sin antecedentes gastrointestinales, menstruación regular', '{\"sintomas\":[\"dolor abdominal\",\"nauseas\",\"fiebre\",\"anorexia\"],\"diagnosticos_posibles\":[\"apendicitis\",\"ovario torcido\",\"gastroenteritis\"],\"respuestas\":{\"ansiedad\":\"Moderada\",\"dolor\":\"9/10\"},\"historia_clinica\":\"Sin antecedentes gastrointestinales, menstruación regular\",\"medicamentos\":[],\"factores_riesgo\":[\"edad\",\"sexo\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(5, 'Convulsiones en Niño', 'Niño de 6 años con episodio de convulsiones generalizadas hace 30 minutos. Primer episodio, sin fiebre. Antecedente de caída previa del día de hoy.', 'Neurología', 'intermedio', 6, 'masculino', 'Convulsiones, pérdida de conciencia', 'Sin antecedentes neurológicos', '{\"sintomas\":[\"convulsiones\",\"perdida conciencia\",\"cefalea postictal\"],\"diagnosticos_posibles\":[\"crisis epiletica\",\"trauma craneoencefalico\",\"infeccion cns\"],\"respuestas\":{\"ansiedad\":\"Alta\",\"dolor\":\"Moderado\"},\"historia_clinica\":\"Sin antecedentes neurológicos\",\"medicamentos\":[],\"factores_riesgo\":[\"edad\",\"trauma\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(6, 'Erupción Cutánea Generalizada', 'Mujer de 35 años con erupción cutánea que apareció hace 2 días. Prurito intenso, distribución simétrica. Inicio después de tomar antibiótico recetado hace 5 días.', 'Dermatología', 'basico', 35, 'femenino', 'Erupción cutánea, prurito', 'Sin antecedentes alérgicos conocidos', '{\"sintomas\":[\"erupcion cutanea\",\"prurito\",\"eritema\"],\"diagnosticos_posibles\":[\"reaccion alergica\",\"urticaria\",\"dermatitis de contacto\"],\"respuestas\":{\"ansiedad\":\"Baja\",\"dolor\":\"Leve\"},\"historia_clinica\":\"Sin antecedentes alérgicos conocidos\",\"medicamentos\":[\"Amoxicilina\"],\"factores_riesgo\":[\"medicacion reciente\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(7, 'Dolor Lumbar Traumático', 'Hombre de 40 años con dolor lumbar intenso tras caída de altura de 3 metros. Dolor localizado en región lumbar, limitación para moverse.', 'Traumatología', 'intermedio', 40, 'masculino', 'Dolor lumbar, limitación movilidad', 'Sin antecedentes traumáticos', '{\"sintomas\":[\"dolor lumbar\",\"limitacion movilidad\",\"contractura muscular\"],\"diagnosticos_posibles\":[\"fractura vertebral\",\"esguince lumbar\",\"hernia discal\"],\"respuestas\":{\"ansiedad\":\"Moderada\",\"dolor\":\"9/10\"},\"historia_clinica\":\"Sin antecedentes traumáticos\",\"medicamentos\":[],\"factores_riesgo\":[\"trauma\",\"altura caida\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2),
(8, 'Crisis de Ansiedad en Joven', 'Joven de 22 años con episodio de angustia intensa, sensación de ahogo, palpitaciones y miedo a morir. Primer episodio, sin desencadenante claro.', 'Psiquiatría', 'basico', 22, 'femenino', 'Ansiedad, palpitaciones, disnea', 'Sin antecedentes psiquiátricos', '{\"sintomas\":[\"ansiedad\",\"palpitaciones\",\"disnea\",\"miedo morir\"],\"diagnosticos_posibles\":[\"crisis ansiedad\",\"ataque panico\",\"hipertiroidismo\"],\"respuestas\":{\"ansiedad\":\"Muy alta\",\"dolor\":\"Leve\"},\"historia_clinica\":\"Sin antecedentes psiquiátricos\",\"medicamentos\":[],\"factores_riesgo\":[\"edad\",\"sexo\",\"estres\"]}', 1, '2025-07-25 00:28:59', '2025-07-25 00:28:59', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json') DEFAULT 'texto',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `fecha_actualizacion`) VALUES
(1, 'titulo_sitio', 'Bionica', 'Nombre del sitio web', 'texto', '2025-07-25 18:03:28'),
(2, 'descripcion_sitio', 'Plataforma de Simulación Clínica con IA', 'Descripción del sitio', 'texto', '2025-07-25 00:28:59'),
(3, 'email_contacto', 'contacto@edumedia.com', 'Email de contacto', 'texto', '2025-07-25 00:28:59'),
(4, 'max_simulaciones_diarias', '10', 'Máximo de simulaciones por usuario al día', 'numero', '2025-07-25 00:28:59'),
(5, 'tiempo_sesion_minutos', '60', 'Tiempo máximo de sesión en minutos', 'numero', '2025-07-25 00:28:59'),
(6, 'registro_habilitado', '1', 'Permitir registro de nuevos usuarios', 'booleano', '2025-07-25 00:28:59'),
(7, 'modo_mantenimiento', '0', 'Sitio en modo mantenimiento', 'booleano', '2025-07-25 00:28:59'),
(8, 'version_sistema', '1.0.0', 'Versión actual del sistema', 'texto', '2025-07-25 00:28:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT 'fa-stethoscope',
  `color` varchar(7) DEFAULT '#3498db',
  `activa` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id`, `nombre`, `descripcion`, `icono`, `color`, `activa`, `fecha_creacion`) VALUES
(1, 'Cardiología', 'Especialidad médica que se enfoca en el corazón y el sistema cardiovascular', 'fa-heart', '#e74c3c', 1, '2025-07-25 00:28:59'),
(2, 'Pediatría', 'Especialidad médica dedicada a la atención de niños y adolescentes', 'fa-child', '#3498db', 1, '2025-07-25 00:28:59'),
(3, 'Neumología', 'Especialidad médica que trata las enfermedades del aparato respiratorio', 'fa-lungs', '#27ae60', 1, '2025-07-25 00:28:59'),
(4, 'Gastroenterología', 'Especialidad médica que estudia el aparato digestivo y órganos anexos', 'fa-stomach', '#f39c12', 1, '2025-07-25 00:28:59'),
(5, 'Neurología', 'Especialidad médica que trata trastornos del sistema nervioso', 'fa-brain', '#9b59b6', 1, '2025-07-25 00:28:59'),
(6, 'Dermatología', 'Especialidad médica que se ocupa del estudio de la piel', 'fa-allergies', '#1abc9c', 1, '2025-07-25 00:28:59'),
(7, 'Traumatología', 'Especialidad médica que trata lesiones del aparato locomotor', 'fa-bone', '#d35400', 1, '2025-07-25 00:28:59'),
(8, 'Psiquiatría', 'Especialidad médica que trata trastornos mentales', 'fa-head-side-brain', '#8e44ad', 1, '2025-07-25 00:28:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interacciones`
--

CREATE TABLE `interacciones` (
  `id` int(11) NOT NULL,
  `simulacion_id` int(11) DEFAULT NULL,
  `tipo` enum('pregunta','respuesta','diagnostico','feedback','sistema','observacion') DEFAULT NULL,
  `contenido` longtext DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `interacciones`
--

INSERT INTO `interacciones` (`id`, `simulacion_id`, `tipo`, `contenido`, `timestamp`) VALUES
(1, 1, 'pregunta', '¿Cuándo comenzaron los síntomas?', '2025-07-25 00:28:59'),
(2, 1, 'respuesta', 'Los síntomas comenzaron hace 2 horas, de forma repentina.', '2025-07-25 00:28:59'),
(3, 1, 'pregunta', '¿Tiene antecedentes de problemas cardíacos?', '2025-07-25 00:28:59'),
(4, 1, 'respuesta', 'Sí, soy hipertenso y fumo desde hace 20 años.', '2025-07-25 00:28:59'),
(5, 1, 'feedback', '🎓 **Evaluación de tu pregunta:**\n🎯 **Pertinencia:** 90%\n🔍 **Profundidad:** 85%\n📋 **Orden lógico:** 92%\n\n✅ Excelente pregunta relevante para el caso\n✅ Buena profundidad en la pregunta\n\n💡 **Sugerencias:**\n- Considera preguntar sobre factores desencadenantes\n- Evalúa síntomas asociados como sudoración', '2025-07-25 00:28:59'),
(6, 1, 'diagnostico', 'Infarto agudo de miocardio', '2025-07-25 00:28:59'),
(7, 1, 'feedback', '📋 **Evaluación de tu diagnóstico:**\n🎯 **Precisión:** 95%\n📝 **Justificación:** 88%\n✅ **Completitud:** 90%\n\n✅ Diagnóstico correcto y relevante', '2025-07-25 00:28:59'),
(8, 5, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 00:29:46'),
(9, 5, 'pregunta', 'en que', '2025-07-25 00:30:18'),
(10, 6, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 8 años. Necesito ayuda médica.', '2025-07-25 00:33:35'),
(11, 6, 'pregunta', '¿Qué tan intenso es el dolor?', '2025-07-25 00:33:51'),
(12, 6, 'respuesta', 'El dolor es intenso, 7/10. Me duele especialmente cuando hago ejercicio', '2025-07-25 00:33:51'),
(13, 7, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 00:35:44'),
(14, 7, 'pregunta', 'TE DUELE ALGO', '2025-07-25 00:36:01'),
(15, 7, 'respuesta', 'Esa es una buena pregunta. Déjeme pensar...', '2025-07-25 00:36:02'),
(16, 8, 'respuesta', 'Hola doctor, soy la paciente virtual. Me llamo Juan y tengo 25 años. Necesito ayuda médica.', '2025-07-25 00:37:52'),
(17, 8, 'pregunta', '\"¿Cuándo comenzaron los síntomas?', '2025-07-25 00:38:06'),
(18, 8, 'respuesta', 'Entiendo su preocupación, doctor. ¿Puede explicarme más sobre eso?', '2025-07-25 00:38:07'),
(19, 8, 'pregunta', 'que te duele', '2025-07-25 00:38:19'),
(20, 8, 'respuesta', 'He notado que los síntomas empeoran por la noche.', '2025-07-25 00:38:20'),
(21, 8, 'pregunta', 'cuales sintomas', '2025-07-25 00:38:29'),
(22, 8, 'respuesta', 'He notado que los síntomas empeoran por la noche.', '2025-07-25 00:38:30'),
(23, 9, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 8 años. Necesito ayuda médica.', '2025-07-25 00:44:28'),
(24, 9, 'pregunta', 'Qué tan intenso es el dolor', '2025-07-25 00:44:45'),
(25, 9, 'respuesta', 'El dolor es intenso, 7/10. Me duele especialmente cuando hago ejercicio', '2025-07-25 00:44:46'),
(26, 10, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 00:55:59'),
(27, 10, 'pregunta', 'Cuándo comenzaron los síntomas', '2025-07-25 00:56:24'),
(28, 10, 'respuesta', 'Los síntomas comenzaron hace aproximadamente dos días. Empecé a sentir un dolor intenso en el pecho que se irradia hacia el brazo izquierdo, además de sentirme muy ansioso y con dificultad para respirar. Mi nivel de ansiedad ha aumentado debido a la intensidad del dolor y a mis antecedentes médicos.', '2025-07-25 00:56:27'),
(29, 10, 'feedback', '### Evaluación de la pregunta:\n\n1. **Pertinencia:** La pregunta sobre cuándo comenzaron los síntomas es relevante para el caso de cardiología, ya que el momento de inicio de los síntomas puede ser crucial para diferenciar entre diferentes diagnósticos como IAM, angina o disección aórtica.\n\n2. **Profundidad y claridad:** La pregunta es clara en su formulación, pero sería beneficioso para el estudiante especificar qué síntomas está considerando y por qué es importante conocer el momento de inicio en este contexto.\n\n3. **Sugerencias de mejora:** Se podría mejorar la pregunta incluyendo ejemplos de síntomas específicos que podrían ayudar a diferenciar entre los diagnósticos mencionados. Además, sería útil explicar por qué el momento de inicio de los síntomas es relevante para cada uno de los posibles diagnósticos.\n\n### Retroalimentación constructiva:\n\nLa pregunta sobre cuándo comenzaron los síntomas es fundamental en cardiología, ya que puede proporcionar pistas importantes para llegar a un diagnóstico preciso. Para mejorar, te sugiero que seas más específico al mencionar los síntomas clave a considerar en este caso (por ejemplo, dolor torácico, disnea, sudoración, etc.) y expliques cómo el momento de inicio de estos síntomas puede influir en la sospecha diagnóstica. Continúa trabajando en la formulación de preguntas claras y detalladas que aborden aspectos relevantes para el caso clínico. ¡Sigue así!', '2025-07-25 00:56:31'),
(30, 11, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 8 años. Necesito ayuda médica.', '2025-07-25 01:05:49'),
(31, 11, 'pregunta', 'que has tomado este dia', '2025-07-25 01:06:22'),
(32, 11, 'respuesta', 'Hoy tomé un desayuno normal, jugo de naranja y cereales con leche. Para el almuerzo comí pollo con arroz y ensalada. De merienda comí una manzana. No he tomado ningún medicamento o pastilla para el dolor. El dolor que siento es bastante intenso, es como un 7 sobre 10.', '2025-07-25 01:06:25'),
(33, 11, 'feedback', '### Evaluación de la pregunta:\n\n1. **Pertinencia:** La pregunta no es pertinente al caso presentado en Pediatría, ya que se centra en conocer qué ha tomado el paciente en el día, sin relación directa con los síntomas o diagnósticos mencionados.\n\n2. **Profundidad y claridad:** La pregunta es poco profunda y carece de claridad en su relación con la especialidad de Pediatría y los posibles diagnósticos. No aporta información relevante para el abordaje clínico de un paciente pediátrico con migraña, meningitis o tumor cerebral.\n\n3. **Sugerencias de mejora:** Se recomienda al estudiante formular preguntas más enfocadas en los síntomas presentados por el paciente, antecedentes médicos relevantes, exploración física o pruebas diagnósticas necesarias para abordar los posibles diagnósticos diferenciales mencionados.\n\n4. **Retroalimentación constructiva:** Es fundamental que las preguntas formuladas estén directamente relacionadas con la información clínica proporcionada en el caso, de manera que ayuden a obtener datos relevantes para el diagnóstico y manejo del paciente. En este caso, se sugiere enfocarse en preguntas que permitan explorar los síntomas principales, signos de alarma, antecedentes médicos y factores de riesgo que orienten hacia uno de los diagnósticos mencionados.\n\nRecuerda que en la práctica clínica, la formulación de preguntas pertinentes y claras es crucial para obtener la información necesaria y tomar decisiones clínicas acertadas. ¡Sigue practicando y mejorando tus habilidades de interrogatorio clínico!', '2025-07-25 01:06:30'),
(34, 11, 'pregunta', 'que sientas mas', '2025-07-25 01:07:19'),
(35, 11, 'respuesta', 'Siento un dolor fuerte en mi estómago, me duele mucho. Estoy incómodo y no puedo dejar de llorar por el dolor. Quisiera que se me quite.', '2025-07-25 01:07:21'),
(36, 11, 'feedback', '### Evaluación de la pregunta:\n\n1. **Pertinencia:** La pregunta no es pertinente al caso presentado, ya que no proporciona información suficiente sobre los síntomas principales del paciente ni sobre el contexto clínico que permita diferenciar entre las opciones de diagnóstico mencionadas.\n\n2. **Profundidad y claridad:** La pregunta carece de profundidad y claridad. No está claro a qué se refiere con \"que sientas mas\", lo que puede llevar a interpretaciones erróneas por parte del evaluador.\n\n3. **Sugerencias de mejora:** \n   - Proporciona información más detallada sobre los síntomas principales del paciente.\n   - Sé específico en tu pregunta, evita ambigüedades que puedan llevar a malentendidos.\n\n### Retroalimentación constructiva:\n\nEs importante que en tus preguntas incluyas información relevante y específica que permita al evaluador entender claramente el escenario clínico que estás presentando. En este caso, te recomendaría que describas de manera más detallada los síntomas del paciente y el contexto clínico para que la pregunta sea más precisa y útil en la diferenciación de diagnósticos. Recuerda ser claro y conciso en tus planteamientos para facilitar la evaluación. ¡Sigue practicando y mejorando en la formulación de preguntas clínicas!', '2025-07-25 01:07:26'),
(37, 12, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:17:16'),
(38, 13, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:23:15'),
(39, 14, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:23:35'),
(40, 14, 'pregunta', 'cuales son tus sintomas', '2025-07-25 01:24:43'),
(41, 14, 'respuesta', 'Doctor, tengo un fuerte dolor en el pecho, siento como si me apretaran con fuerza y se extiende hacia mi brazo izquierdo. Además, tengo dificultad para respirar y me siento mareado. También tengo náuseas y sudoración fría. Estoy muy asustado por lo que me está sucediendo.', '2025-07-25 01:24:45'),
(42, 14, 'feedback', '### Evaluación de la pregunta:\n\n1. La pregunta es pertinente al contexto del caso de cardiología, ya que los síntomas son fundamentales para el diagnóstico diferencial entre IAM, angina y disección aórtica.\n   \n2. La pregunta carece de profundidad y claridad, ya que no especifica cuáles son los síntomas que se deben considerar para diferenciar entre los diagnósticos posibles.\n   \n### Sugerencias de mejora:\n\n- Especificar qué tipo de síntomas se deben considerar para cada uno de los diagnósticos mencionados.\n- Podría ser útil incluir ejemplos de síntomas característicos de IAM, angina y disección aórtica para guiar al estudiante en la identificación de los mismos.\n\n### Retroalimentación constructiva:\n\nEs importante ser específico al preguntar sobre los síntomas en un caso clínico, ya que esto puede orientar hacia el diagnóstico correcto. Te recomendaría mejorar la claridad de tu pregunta incluyendo ejemplos de síntomas característicos para cada diagnóstico mencionado. Esto ayudará a los estudiantes a comprender mejor la importancia de la semiología en la práctica clínica. ¡Sigue practicando y mejorando tus habilidades de interrogatorio clínico!', '2025-07-25 01:24:49'),
(43, 15, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:27:15'),
(44, 16, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 01:27:45'),
(45, 17, 'respuesta', 'Hola doctor, soy la paciente virtual. Me llamo Juan y tengo 35 años. Necesito ayuda médica.', '2025-07-25 01:27:53'),
(46, 17, 'pregunta', 'cuales son tus sintomas', '2025-07-25 01:29:30'),
(47, 17, 'respuesta', 'He estado sintiendo un malestar general en mi cuerpo, un poco de cansancio y dolores musculares leves. También he tenido algo de dolor de cabeza y sensación de náuseas. No tengo fiebre ni tos, pero me siento un poco congestionada. Estos síntomas han estado presentes durante los últimos días.', '2025-07-25 01:29:34'),
(48, 17, 'feedback', '### Evaluación de la pregunta:\n\n1. La pregunta es pertinente para el caso, ya que en Dermatología es crucial conocer los síntomas que presenta el paciente para poder realizar un diagnóstico adecuado.\n   \n2. La pregunta es bastante básica y genérica. Sería útil especificar más sobre los síntomas que se están experimentando, como tipo de lesiones cutáneas, ubicación, duración, factores desencadenantes, entre otros.\n   \n3. Para mejorar la pregunta, se podría reformular de la siguiente manera: \"¿Podrías describir con más detalle los síntomas que estás experimentando en tu piel? Por ejemplo, ¿hay enrojecimiento, picazón, ampollas, o alguna otra característica que hayas notado?\"\n   \n4. **Retroalimentación constructiva:** Es importante que como médico, seas lo más específico posible al recopilar información sobre los síntomas de un paciente en Dermatología. Esto te ayudará a diferenciar entre diferentes diagnósticos posibles y a brindar un tratamiento más preciso. Recuerda siempre indagar sobre la calidad de las lesiones, factores desencadenantes, evolución temporal y otros síntomas asociados que puedan ser relevantes para el diagnóstico diferencial. ¡Sigue practicando para mejorar tus habilidades de interrogatorio clínico!', '2025-07-25 01:29:39'),
(49, 17, 'pregunta', 'erupcion cutanea prurito', '2025-07-25 01:31:02'),
(50, 17, 'respuesta', 'He notado que tengo una erupción cutánea en mi piel que me provoca picazón. Aunque el dolor es leve, la comezón me resulta bastante molesta. No tengo antecedentes de alergias conocidas, por lo que me preocupa que pueda ser una reacción a algo nuevo. ¿Qué podría estar causando esta erupción y cómo puedo aliviar la picazón?', '2025-07-25 01:31:04'),
(51, 17, 'feedback', '### Evaluación de la pregunta:\n\n1. La pregunta es pertinente al contexto de dermatología, ya que aborda un síntoma común en esta especialidad.\n   \n2. La pregunta es clara y concisa al mencionar \"erupción cutánea prurito\", lo cual ayuda a enfocar la discusión en síntomas específicos.\n   \n3. Para mejorar la pregunta, sería útil proporcionar más detalles sobre el tipo de erupción (aspecto, distribución, duración) y el tiempo de inicio del prurito.\n   \n### Retroalimentación constructiva:\n\nLa pregunta es un buen punto de partida al abordar un caso de erupción cutánea con prurito en dermatología. Para enriquecer la discusión y facilitar un diagnóstico diferencial más preciso, te recomendaría incluir más detalles sobre la erupción y el prurito, así como considerar preguntas relacionadas con antecedentes médicos, exposiciones recientes y tratamientos previos. Recuerda que en dermatología, la historia clínica detallada es fundamental para llegar a un diagnóstico preciso. ¡Sigue practicando y mejorando en la formulación de preguntas clínicas!', '2025-07-25 01:31:08'),
(52, 18, 'respuesta', 'Hola doctor, soy la paciente virtual. Me llamo Juan y tengo 35 años. Necesito ayuda médica.', '2025-07-25 01:35:55'),
(53, 18, 'pregunta', 'desde cuando inicio Erupción', '2025-07-25 01:36:20'),
(54, 18, 'respuesta', 'La erupción comenzó hace aproximadamente una semana. Al principio solo eran pequeñas manchas rojas que picaban un poco, pero ahora se ha extendido por mi espalda y brazos. No he usado ningún producto nuevo en mi piel, así que no sé qué puede estar causando esta erupción. Me preocupa que pueda ser algo más serio, por eso decidí venir a consulta médica para que me ayuden a averiguar qué está pasando.', '2025-07-25 01:36:23'),
(55, 18, 'feedback', '### Evaluación de la pregunta:\n\n1. La pregunta es pertinente al caso presentado, ya que se relaciona directamente con la especialidad de Dermatología y los posibles diagnósticos mencionados.\n   \n2. La pregunta carece de profundidad y claridad, ya que no especifica cuál es la erupción a la que se refiere ni proporciona detalles adicionales que podrían ayudar a identificar el problema dermatológico.\n\n### Sugerencias de mejora:\n\n1. Ser más específico al mencionar el tipo de erupción del que se está hablando (por ejemplo, tipo de lesiones, localización, características).\n   \n2. Proporcionar más detalles sobre los síntomas asociados con la erupción (picazón, dolor, inflamación, etc.).\n   \n3. Incluir información sobre factores desencadenantes que podrían estar relacionados con la erupción (exposición a sustancias, cambios en la piel, etc.).\n\n### Retroalimentación constructiva:\n\nPara mejorar la calidad de tus preguntas en casos dermatológicos, es fundamental ser específico y detallado en la descripción de los síntomas y características de las lesiones cutáneas. Esto ayudará a orientar de manera más precisa hacia un diagnóstico adecuado. Recuerda siempre considerar factores desencadenantes y síntomas asociados para una evaluación más completa. ¡Sigue practicando y mejorando en la formulación de preguntas clínicas!', '2025-07-25 01:36:28'),
(56, 18, 'pregunta', 'síntomas y características de las lesiones cutáneas', '2025-07-25 01:37:38'),
(57, 18, 'respuesta', 'No he notado ninguna lesión cutánea en mi piel. No tengo enrojecimiento, protuberancias, ampollas ni ningún tipo de erupción. Mi piel se ve normal y no tengo ninguna molestia en ese aspecto. ¿Debo preocuparme por algo en específico?', '2025-07-25 01:37:41'),
(58, 18, 'feedback', '### Evaluación de la pregunta:\n\n1. La pregunta es pertinente al caso presentado, ya que se encuentra dentro del contexto de la dermatología y los diagnósticos posibles mencionados.\n   \n2. La pregunta es clara y directa al pedir información sobre los síntomas y características de las lesiones cutáneas en general. Sin embargo, sería útil especificar si se refiere a síntomas comunes a todas las lesiones cutáneas o a síntomas específicos de los diagnósticos posibles mencionados.\n   \n3. Para mejorar la pregunta, se podría añadir más detalles sobre los síntomas asociados a cada uno de los diagnósticos posibles (reacción alérgica, urticaria, dermatitis de contacto) para hacerla más específica y desafiante.\n   \n### Retroalimentación constructiva:\n\nLa pregunta es un buen punto de partida, pero se beneficiaría de ser más específica para abordar mejor los diagnósticos posibles mencionados. Te sugiero que incluyas detalles sobre los síntomas característicos de cada diagnóstico para enriquecer la pregunta y hacerla más desafiante. ¡Sigue adelante con tu práctica y sigue mejorando en la formulación de preguntas clínicas!', '2025-07-25 01:37:44'),
(59, 18, 'pregunta', 'La pregunta es pertinente al caso presentado, ya que se encuentra dentro del contexto de la dermatología y los diagnósticos posibles mencionados', '2025-07-25 01:38:04'),
(60, 18, 'respuesta', 'Sí, doctor, entiendo la importancia de la pregunta en relación con la dermatología y los posibles diagnósticos mencionados. Estoy dispuesta a proporcionar más detalles sobre mis síntomas para ayudar en el proceso de diagnóstico. Aunque actualmente tengo un nivel bajo de ansiedad y experimento un dolor leve, me preocupa la aparición de cualquier problema en mi piel. Estoy abierta a realizar cualquier prueba o seguimiento necesario para identificar la causa de mis síntomas dermatológicos.', '2025-07-25 01:38:06'),
(61, 18, 'feedback', '### Evaluación de la pregunta médica:\n\n1. **Pertinencia:** La pregunta es pertinente al caso presentado, ya que se relaciona con la especialidad de dermatología y los posibles diagnósticos mencionados.\n\n2. **Profundidad y claridad:** La pregunta es clara y directa, lo cual es positivo. Sin embargo, podría beneficiarse de una mayor profundidad al incluir más detalles sobre los síntomas o características específicas que ayudarían a diferenciar entre los diagnósticos posibles.\n\n3. **Sugerencias de mejora:** Sería útil ampliar la pregunta incluyendo información adicional sobre los síntomas principales que presenta el paciente, como la apariencia de las lesiones cutáneas, la duración de los síntomas, factores desencadenantes, entre otros. Esto permitiría una evaluación más detallada y precisa de los diagnósticos diferenciales.\n\n4. **Retroalimentación constructiva:** ¡Excelente trabajo en la pertinencia de la pregunta! Para mejorar, te recomendaría profundizar un poco más en los detalles clínicos del caso para enriquecer la pregunta y facilitar la diferenciación entre los diagnósticos posibles. Recuerda que los detalles específicos son clave en el proceso de diagnóstico en dermatología.\n\n¡Sigue así, estás en el camino correcto! Con práctica y atención a los detalles, mejorarás tu habilidad para formular preguntas clínicas más completas y precisas. ¡Adelante!', '2025-07-25 01:38:11'),
(62, 18, 'pregunta', 'Profundidad y claridad:** La pregunta es clara y directa, lo cual es positivo. Sin embargo, podría beneficiarse de una mayor profundidad al incluir más detalles sobre los síntomas o características específicas que ayudarían a diferenciar entre los diagnósticos posibles.', '2025-07-25 01:38:58'),
(63, 18, 'respuesta', 'Soy una paciente de 35 años de edad, género femenino. No tengo antecedentes alérgicos conocidos. En este momento, experimento un dolor leve y mi nivel de ansiedad es bajo. Me gustaría obtener más información sobre los síntomas que presento para poder comunicarlos con claridad al médico.', '2025-07-25 01:39:00'),
(64, 18, 'feedback', '### Evaluación de la pregunta:\n\n1. **Pertinencia:** La pregunta es pertinente al caso presentado, ya que se relaciona directamente con los diagnósticos posibles y la especialidad de dermatología.\n   \n2. **Profundidad y claridad:** La pregunta es clara al solicitar al estudiante que diferencie entre reacción alérgica, urticaria y dermatitis de contacto. Sin embargo, sería beneficioso incluir más detalles sobre los síntomas o características distintivas de cada condición para hacer la pregunta más desafiante y educativa.\n\n3. **Sugerencias de mejora:** Para mejorar la pregunta, se podría pedir al estudiante que describa los síntomas específicos que esperarías encontrar en cada uno de los diagnósticos posibles, como tipo de lesiones cutáneas, distribución, prurito, duración, factores desencadenantes, entre otros.\n\n4. **Retroalimentación constructiva:** Al incluir más detalles en la pregunta, se fomenta una comprensión más profunda de las diferencias entre las condiciones dermatológicas y se promueve el pensamiento crítico y analítico en el estudiante. Recuerda que en la práctica clínica, los detalles y la capacidad de diferenciar entre diagnósticos similares son fundamentales para brindar un cuidado de calidad a los pacientes. ¡Sigue practicando y mejorando en la formulación de preguntas más detalladas y desafiantes!', '2025-07-25 01:39:04'),
(65, 19, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:41:49'),
(66, 20, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:42:10'),
(67, 21, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 8 años. Necesito ayuda médica.', '2025-07-25 01:42:15'),
(68, 22, 'respuesta', 'Hola doctor, soy la paciente virtual. Me llamo Juan y tengo 25 años. Necesito ayuda médica.', '2025-07-25 01:44:27'),
(69, 22, 'pregunta', 'desde cuado presentaste  Dolor abdominal', '2025-07-25 01:44:51'),
(70, 22, 'respuesta', 'Comencé a sentir el dolor abdominal intenso hace aproximadamente 4 horas. Empezó como un dolor leve, pero ha ido aumentando en intensidad constantemente. Es una sensación punzante y constante en la parte baja del abdomen. Además, siento náuseas y algo de mareo. No he tenido este tipo de dolor abdominal tan intenso antes, por lo que me preocupa.', '2025-07-25 01:44:54'),
(71, 22, 'feedback', 'Mentor IA: Pregunta poco clara y relevante. Especifica síntomas y contexto para mejorar la pregunta.', '2025-07-25 01:44:56'),
(72, 23, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 01:50:51'),
(73, 23, 'pregunta', 'DESDE CUANDO SIENTE Dolor torácico', '2025-07-25 01:51:29'),
(74, 23, 'respuesta', 'He estado sintiendo dolor torácico desde hace aproximadamente 1 hora. Empezó como una sensación de opresión en el pecho y ha ido aumentando en intensidad. Me preocupa debido a mis antecedentes familiares de infarto y mi historial de hipertensión. Además, mi nivel de ansiedad está un poco elevado por la situación.', '2025-07-25 01:51:32'),
(75, 23, 'feedback', 'Mentor IA: Pregunta demasiado general. Sugerencia: ¿Puede describir el dolor con más detalle?', '2025-07-25 01:51:34'),
(76, 23, 'pregunta', '¿Puede describir el dolor con más detalle?', '2025-07-25 01:51:50'),
(77, 23, 'respuesta', 'El dolor que siento es intenso, se localiza en el pecho y se irradia hacia el brazo izquierdo. Es como una presión fuerte y constante, a veces siento como si me faltara el aire. También tengo sudoración fría y náuseas. Me preocupa mucho porque nunca había sentido un dolor así antes.', '2025-07-25 01:51:52'),
(78, 23, 'feedback', 'Mentor IA: Pregunta general. Sugerencia: ¿Puede describir si el dolor se irradia a algún lugar o está asociado con otros síntomas?', '2025-07-25 01:51:54'),
(79, 24, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 02:01:15'),
(80, 25, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 02:05:40'),
(81, 26, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 02:05:53'),
(82, 26, 'pregunta', 'desde cuando tienes estos sintomas Dolor torácico', '2025-07-25 02:06:35'),
(83, 26, 'respuesta', 'He estado sintiendo este dolor torácico desde hace aproximadamente 2 días. Al principio pensé que era solo un malestar pasajero, pero ha ido aumentando en intensidad y me preocupa especialmente por mis antecedentes familiares de infarto. La presión en el pecho es constante y a veces se irradia hacia mi brazo izquierdo. Me siento ansioso por no saber qué está causando esto y por mi historial de hipertensión y tabaquismo. ', '2025-07-25 02:06:39'),
(84, 26, 'feedback', 'Mentor IA: Pregunta directa. Sugerencia: ¿Cómo describirías la intensidad y características del dolor torácico?', '2025-07-25 02:06:41'),
(85, 26, 'pregunta', '¿Cómo describirías la intensidad y características del dolor torácico?', '2025-07-25 02:06:51'),
(86, 26, 'respuesta', 'El dolor torácico que estoy experimentando es muy intenso, lo calificaría como un 8/10 en cuanto a intensidad. Es una sensación de presión constante en el pecho que me resulta bastante alarmante, especialmente teniendo en cuenta mis antecedentes de hipertensión, tabaquismo y los problemas cardíacos en mi familia. Me preocupa que pueda ser un síntoma de algo grave, por lo que estoy buscando ayuda médica lo antes posible.', '2025-07-25 02:06:54'),
(87, 26, 'feedback', 'Mentor IA: Pregunta general. Sugerencia: ¿Se irradia el dolor a otras áreas del cuerpo?', '2025-07-25 02:06:56'),
(88, 27, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 02:12:23'),
(89, 27, 'pregunta', 'Dolor', '2025-07-25 02:12:36'),
(90, 27, 'respuesta', 'El dolor que estoy experimentando es muy intenso, lo calificaría como un 8 sobre 10. Es un dolor opresivo en el pecho que se irradia hacia el brazo izquierdo y se acompaña de sudoración fría. Me preocupa mucho porque tengo antecedentes familiares de infarto y soy hipertenso. Estoy realmente asustado por lo que pueda estar ocurriendo. Espero poder recibir ayuda médica lo antes posible.', '2025-07-25 02:12:38'),
(91, 27, 'pregunta', 'Dolor', '2025-07-25 02:12:52'),
(92, 27, 'feedback', 'Mentor IA: Pregunta muy general. Sugerencia: ¿Puede describir el dolor con más detalle?', '2025-07-25 02:13:14'),
(93, 27, 'respuesta', 'Mi dolor es bastante intenso, lo calificaría como un 8 sobre 10. Me preocupa bastante porque soy hipertenso, fumador y tengo antecedentes familiares de infarto. Me siento ansioso por saber qué puede estar causando este dolor y si está relacionado con alguno de mis factores de riesgo. Espero que puedan ayudarme a descubrir qué está pasando.', '2025-07-25 02:13:14'),
(94, 27, 'feedback', 'Mentor IA: Pregunta demasiado general. Sugerencia: ¿Puede describir con más detalle la ubicación y características del dolor?', '2025-07-25 02:13:18'),
(95, 28, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 02:35:42'),
(96, 29, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 02:35:59'),
(97, 29, 'pregunta', 'desde cuando sientes  Dolor torácico,', '2025-07-25 02:39:27'),
(98, 29, 'feedback', 'Mentor IA: Pregunta directa. Sugerencia: ¿Cómo describirías el dolor torácico que sientes?', '2025-07-25 02:39:58'),
(99, 29, 'respuesta', 'He estado sintiendo dolor torácico desde hace aproximadamente una hora. Empezó de repente y es bastante intenso, lo calificaría como un 8/10. Me preocupa mucho dado mis antecedentes familiares de infarto y mi historial de hipertensión. Además, el hecho de ser fumador me hace preocuparme aún más. Mi nivel de ansiedad es moderado, pero con este dolor en el pecho se ha incrementado un poco más. Estoy realmente preocupado y quiero saber qué está pasando.', '2025-07-25 02:39:58'),
(100, 29, 'pregunta', '¿Cómo describirías el dolor torácico que sientes?', '2025-07-25 02:40:12'),
(101, 29, 'feedback', 'Mentor IA: Pregunta genérica. Sugerencia: ¿Cómo es el dolor en relación con la actividad física o el reposo?', '2025-07-25 02:40:32'),
(102, 29, 'respuesta', 'Siento un dolor intenso en el pecho, como una presión fuerte que se irradia hacia el brazo izquierdo. Además, tengo dificultad para respirar y me siento mareado. El dolor es constante y no mejora con el reposo. Me preocupa mucho por mis antecedentes familiares de infarto y mi hipertensión.', '2025-07-25 02:40:32'),
(103, 29, 'pregunta', '¿Cómo es el dolor en relación con la actividad física o el reposo?', '2025-07-25 02:41:53'),
(104, 29, 'feedback', 'Mentor IA: Pregunta general. Sugerencia: ¿Cómo describirías el dolor en términos de intensidad y duración?', '2025-07-25 02:42:13'),
(105, 29, 'respuesta', 'El dolor que siento no parece cambiar mucho con la actividad física o el reposo. Si bien a veces parece intensificarse un poco con ciertas movimientos, en general se mantiene constante. Es bastante fuerte, diría que un 8 sobre 10, y me preocupa porque nunca había experimentado algo así antes.', '2025-07-25 02:42:13'),
(106, 29, 'pregunta', 'sientes nauseas en que momento', '2025-07-25 02:42:19'),
(107, 29, 'feedback', 'Mentor IA: Pregunta poco específica. Sugerencia: ¿Qué desencadena las náuseas que experimentas?', '2025-07-25 02:42:40'),
(108, 29, 'respuesta', 'Sí, he estado sintiendo náuseas principalmente por las mañanas, después de levantarme de la cama. También he notado que las náuseas se intensifican cuando tengo dolor en el pecho. Me preocupa que pueda ser un síntoma relacionado con mi historial de hipertensión y antecedentes familiares de infarto, por lo que decidí buscar ayuda médica.', '2025-07-25 02:42:40'),
(109, 29, 'pregunta', '¿Qué desencadena las náuseas que experimentas?', '2025-07-25 02:43:03'),
(110, 29, 'feedback', 'Mentor IA: Pregunta adecuada sobre las náuseas. Sugerencia: ¿Ha notado si las náuseas se presentan con el esfuerzo físico?', '2025-07-25 02:43:23'),
(111, 29, 'respuesta', 'Las náuseas que experimento suelen empeorar después de las comidas, especialmente si son copiosas o contienen alimentos muy grasos. También he notado que se intensifican cuando me siento muy ansioso o estresado. A veces, el dolor intenso que siento en el pecho también contribuye a que me den náuseas.', '2025-07-25 02:43:23'),
(112, 30, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 75 años. Necesito ayuda médica.', '2025-07-25 02:47:29'),
(113, 30, 'pregunta', 'desde cuando siente ese sintonma de manerada detallada dime  fatiga', '2025-07-25 02:48:00'),
(114, 30, 'feedback', 'Mentor IA: Falta claridad en la pregunta. Sugerencia: ¿Desde cuándo experimenta la fatiga y cómo ha afectado su vida diaria?', '2025-07-25 02:48:25'),
(115, 30, 'respuesta', 'He estado sintiendo fatiga desde hace aproximadamente unas semanas. Al principio pensé que era debido a mi EPOC y diabetes, pero últimamente me cuesta más realizar mis actividades diarias y me siento agotado con mayor facilidad. A veces me cuesta incluso levantarme de la cama por la sensación de cansancio constante. Aunque intento descansar lo suficiente, la fatiga persiste y me preocupa.', '2025-07-25 02:48:25'),
(116, 31, 'respuesta', 'Hola doctor, soy la paciente virtual. Me llamo Juan y tengo 25 años. Necesito ayuda médica.', '2025-07-25 03:15:42'),
(117, 31, 'pregunta', 'DESDE CUANDO TIENES FIEBRE', '2025-07-25 03:16:38'),
(118, 31, 'respuesta', 'No he mencionado tener fiebre como síntoma principal, por lo tanto no tengo fiebre en este momento. Mi principal preocupación son los dolores intensos que estoy experimentando, los cuales calificaría como 9/10 en intensidad.', '2025-07-25 03:16:39'),
(119, 31, 'feedback', 'Mentor IA: Pregunta poco específica. Sugerencia: ¿Qué otros síntomas acompañan a la fiebre?', '2025-07-25 03:16:42'),
(120, 31, 'pregunta', 'QUE SINTOMAAS TIENES', '2025-07-25 03:17:00'),
(121, 31, 'respuesta', 'Doctor, tengo un dolor abdominal muy intenso, es de 9/10 en intensidad. También siento náuseas y mareos. No he tenido vómitos ni diarrea, pero el dolor es constante y me preocupa. Mi nivel de ansiedad es moderado porque nunca he experimentado un dolor como este antes. Agradezco su ayuda para saber qué está causando estos síntomas.', '2025-07-25 03:17:02'),
(122, 31, 'pregunta', 'AAAA', '2025-07-25 03:17:16'),
(123, 31, 'respuesta', 'Doctor, el dolor que siento es muy intenso, está en un nivel 9/10 y me preocupa mucho. Mi ansiedad está un poco elevada por no saber qué está causando este malestar tan fuerte. No tengo antecedentes gastrointestinales y mi menstruación es regular. Estoy realmente preocupada por lo que me está sucediendo.', '2025-07-25 03:17:18'),
(124, 31, 'feedback', 'Mentor IA: Pregunta poco clara. Sugerencia: ¿Podrías describir los síntomas que te preocupan?', '2025-07-25 03:17:21'),
(125, 32, 'respuesta', 'Hola doctor, soy la paciente virtual. Me llamo Juan y tengo 25 años. Necesito ayuda médica.', '2025-07-25 03:17:34'),
(126, 32, 'pregunta', 'AAA', '2025-07-25 03:17:39'),
(127, 32, 'feedback', 'Mentor IA: Pregunta poco clara. Sugerencia: ¿Podrías describir los síntomas que te preocupan?', '2025-07-25 03:18:00'),
(128, 32, 'respuesta', 'Doctor, tengo un dolor abdominal muy intenso, es un 9 sobre 10. No tengo antecedentes gastrointestinales y mi menstruación es regular. Estoy sintiendo mucha ansiedad por esta situación. ¿Qué podría estar causando este dolor tan fuerte? Estoy preocupada y necesito ayuda.', '2025-07-25 03:18:00'),
(129, 33, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:20:17'),
(130, 33, 'pregunta', 'AAA', '2025-07-25 03:20:25'),
(131, 33, 'respuesta', 'Me duele mucho la cabeza y siento que estoy mareado. Me cuesta concentrarme y tengo miedo de que me pase algo grave. Quiero que me ayuden a sentirme mejor, por favor.', '2025-07-25 03:20:27'),
(132, 33, 'feedback', 'Mentor IA: Pregunta poco específica. Sugerencia: ¿Podrías describir con más detalle los síntomas que experimenta el paciente?', '2025-07-25 03:20:30'),
(133, 34, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:25:54'),
(134, 34, 'pregunta', 'AA', '2025-07-25 03:26:00'),
(135, 34, 'respuesta', 'Me siento asustado y confundido. Mi cabeza me duele mucho y no entiendo por qué. No me gusta estar aquí en el hospital, me da miedo que me hagan algún tratamiento. Quiero que la doctora me explique qué me está pasando y cómo me va a ayudar a sentirme mejor.', '2025-07-25 03:26:01'),
(136, 35, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:26:08'),
(137, 35, 'pregunta', 'SADASDAS', '2025-07-25 03:26:14'),
(138, 35, 'respuesta', 'No estoy seguro de qué es \"SADASDAS\", ¿puedes explicarme un poco más para poder responder adecuadamente? Gracias.', '2025-07-25 03:26:16'),
(139, 35, 'feedback', 'Mentor IA: Pregunta no clara. Sugerencia: ¿Puedes describir los síntomas que estás experimentando?', '2025-07-25 03:26:20'),
(140, 36, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:28:36'),
(141, 36, 'pregunta', 'ASDAS', '2025-07-25 03:28:40'),
(142, 37, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:38:23'),
(143, 37, 'pregunta', 'desde cuando tienes convulsiones', '2025-07-25 03:38:56'),
(144, 37, 'respuesta', 'No he tenido convulsiones. Mis síntomas principales no han sido especificados, pero me siento ansioso y con un dolor moderado. ¿Podrían decirme qué está pasando y cómo me pueden ayudar? Estoy preocupado por cómo me siento.', '2025-07-25 03:38:59'),
(145, 37, 'feedback', 'Mentor IA: Pregunta directa pero sería más adecuado preguntar \"¿Desde cuándo presenta episodios de convulsiones?\" Sugerencia: ¿Ha identificado algún desencadenante o patrón en sus convulsiones?', '2025-07-25 03:39:01'),
(146, 37, 'pregunta', 'quechua', '2025-07-25 03:39:32'),
(147, 37, 'respuesta', 'Lo siento, no entiendo la palabra \"quechua\". ¿Puedes reformular tu pregunta?', '2025-07-25 03:39:34'),
(148, 37, 'feedback', 'Mentor IA: Falta de claridad en la pregunta. Sugerencia: ¿Podrías describir los síntomas con más detalle?', '2025-07-25 03:39:36'),
(149, 38, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:39:39'),
(150, 39, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 6 años. Necesito ayuda médica.', '2025-07-25 03:39:52'),
(151, 40, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 03:40:43'),
(152, 41, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 03:54:35'),
(153, 41, 'pregunta', 'aaaaa', '2025-07-25 03:54:46'),
(154, 41, 'respuesta', 'Siento un dolor intenso en el pecho, es como una presión fuerte, y me cuesta respirar. También siento un dolor que se irradia hacia el brazo izquierdo. Estoy muy nervioso por lo que pueda estar pasando, especialmente con mis antecedentes familiares de infarto. Por favor, necesito ayuda lo más rápido posible.', '2025-07-25 03:54:50'),
(155, 41, 'pregunta', 'a', '2025-07-25 03:55:09'),
(156, 41, 'feedback', 'Mentor IA: Pregunta poco clara. Sugerencia: ¿Podrías describir los síntomas que te preocupan en detalle?', '2025-07-25 03:55:10'),
(157, 41, 'respuesta', 'Doctor, los síntomas que estoy experimentando son un dolor en el pecho muy intenso, como una presión constante, que se irradia hacia mi brazo izquierdo, junto con náuseas y sudoración. Estoy muy preocupado por lo que pueda estar sucediendo, especialmente con mis antecedentes de hipertensión y los problemas cardíacos en mi familia. Mi nivel de ansiedad es bastante alto en este momento y el dolor que siento es muy fuerte, diría que es un 8 sobre 10. ¿Qué debo hacer? Estoy asustado.', '2025-07-25 03:55:11'),
(158, 41, 'feedback', 'Mentor IA: Pregunta poco específica. Sugerencia: ¿Podrías describir los síntomas que estás experimentando en detalle?', '2025-07-25 03:55:14'),
(159, 41, 'pregunta', '¿Podrías describir los síntomas que estás experimentando en detalle?', '2025-07-25 03:55:20'),
(160, 41, 'respuesta', 'Siento un dolor muy intenso en el pecho, como si algo pesado me aplastara. También tengo dificultad para respirar y tengo sudores fríos. Me siento muy nervioso y ansioso, pensando que podría ser algo relacionado con mi corazón, sobre todo por mis antecedentes familiares de infarto. El dolor ha aumentado en intensidad en las últimas horas y no mejora con el reposo. Estoy preocupado y asustado por lo que pueda estar pasando.', '2025-07-25 03:55:21'),
(161, 41, 'feedback', 'Mentor IA: Pregunta directa. Sugerencia: ¿Cómo se relacionan los síntomas con la actividad física?', '2025-07-25 03:55:24'),
(162, 42, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 03:59:25'),
(163, 43, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 04:01:29'),
(164, 44, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 04:05:08'),
(165, 44, 'pregunta', 'sintomas', '2025-07-25 04:05:47'),
(166, 44, 'respuesta', 'Siento un dolor en el pecho muy intenso, como una presión fuerte, y se me irradia hacia el brazo izquierdo. También tengo dificultad para respirar y siento un malestar general. Además, tengo náuseas y estoy sudando mucho. Estoy preocupado por mis antecedentes familiares de infarto y mi hipertensión.', '2025-07-25 04:05:50'),
(167, 44, 'feedback', 'Mentor IA: Falta especificar los síntomas. Sugerencia: ¿Puede describir con detalle los síntomas que está experimentando el paciente?', '2025-07-25 04:06:09'),
(168, 44, 'pregunta', 'a', '2025-07-25 04:06:28'),
(169, 44, 'respuesta', 'Doctor, me siento muy preocupado por este dolor en el pecho que tengo. Es muy intenso, lo calificaría como un 8 sobre 10. Tengo antecedentes familiares de infarto y además soy hipertenso y fumador, por lo que me preocupa que pueda ser algo grave. Mi nivel de ansiedad es moderado, pero este dolor me está asustando mucho. Espero que pueda ayudarme a entender qué está pasando.', '2025-07-25 04:06:30'),
(170, 44, 'feedback', 'Mentor IA: Pregunta poco específica. Sugerencia: ¿Cuáles son los síntomas que te preocupan en relación con tu corazón?', '2025-07-25 04:06:32'),
(171, 45, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 04:08:30'),
(172, 46, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 18:05:32'),
(173, 47, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 18:06:01'),
(174, 48, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 18:08:28'),
(175, 48, 'pregunta', 'cuentame cuales son tus sintomas', '2025-07-25 18:08:49'),
(176, 48, 'respuesta', 'Doctor, he estado sintiendo un dolor intenso en el pecho, que se irradia hacia mi brazo izquierdo. También he notado dificultad para respirar y sudoración excesiva. Además, siento mucha ansiedad y preocupación por estos síntomas. Cabe mencionar que tengo antecedentes de hipertensión, fumo y mi familia tiene historia de infartos. El dolor es bastante fuerte, lo calificaría como un 8/10 en términos de intensidad.', '2025-07-25 18:08:53'),
(177, 48, 'pregunta', 'Tú: cuentame cuales son tus sintomas', '2025-07-25 18:09:05'),
(178, 48, 'feedback', 'Mentor IA: Pregunta demasi genérica. Sugerencia: ¿Puede describir con detalle lo que siente en su pecho?', '2025-07-25 18:09:37'),
(179, 48, 'respuesta', 'He estado sintiendo un dolor muy intenso en el pecho, como una presión constante, que se irradia hacia mi brazo izquierdo. También tengo dificultad para respirar y me siento mareado. Estoy preocupado porque mi familia tiene antecedentes de infartos y sé que soy hipertenso y fumador. Mi nivel de ansiedad es moderado, pero el dolor es muy intenso, diría que es un 8/10 en cuanto a intensidad.', '2025-07-25 18:09:37'),
(180, 48, 'feedback', 'Mentor IA: Pregunta directa. Sugerencia: ¿Cómo describirías la sensación en tu pecho?', '2025-07-25 18:09:41'),
(181, 48, 'pregunta', 'desde cuanndo empezo', '2025-07-25 18:09:43'),
(182, 48, 'pregunta', 'no me baja', '2025-07-25 18:10:06'),
(183, 48, 'feedback', 'Mentor IA: Falta claridad en la pregunta. Sugerencia: ¿Desde cuándo experimenta los síntomas cardíacos?', '2025-07-25 18:10:06'),
(184, 48, 'respuesta', 'Los síntomas empezaron hace aproximadamente una semana, he estado sintiendo un dolor en el pecho bastante intenso y constante, acompañado de falta de aire y sudoración. Me preocupa mucho por mi historial clínico y antecedentes familiares de infarto. Agradezco cualquier orientación que pueda proporcionarme para entender qué está sucediendo.', '2025-07-25 18:10:06'),
(185, 49, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 45 años. Necesito ayuda médica.', '2025-07-25 18:10:26'),
(186, 50, 'respuesta', 'Hola doctor, soy el paciente virtual. Me llamo Juan y tengo 8 años. Necesito ayuda médica.', '2025-07-25 21:36:23'),
(187, 50, 'pregunta', 'cuakes son tus sintomas', '2025-07-25 21:36:42'),
(188, 50, 'feedback', 'Mentor IA: Ortografía. Sugerencia: ¿Cuáles son los síntomas que has experimentado?', '2025-07-25 21:37:11'),
(189, 50, 'respuesta', 'He estado sintiendo un dolor muy fuerte en mi estómago, especialmente en la parte baja. También he tenido náuseas y sensación de malestar general. Me cuesta mucho moverme debido al dolor y me siento débil. Además, he notado que tengo fiebre desde hace unas horas. Estoy preocupado por cómo me siento y quiero que me ayuden a sentirme mejor.', '2025-07-25 21:37:11'),
(190, 50, 'pregunta', '¿Kuna sintomanakas jumax uñjta?', '2025-07-25 21:37:33'),
(191, 50, 'respuesta', 'No comprendo la pregunta del médico. ¿Podría explicar de otra manera para poder responder adecuadamente?', '2025-07-25 21:37:42'),
(192, 50, 'feedback', 'Mentor IA: Pregunta no clara. Sugerencia: ¿Podrías describir los síntomas que presenta tu hijo?', '2025-07-25 21:37:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logros`
--

CREATE TABLE `logros` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `logro` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `puntos` int(11) DEFAULT 0,
  `fecha_obtencion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logros`
--

INSERT INTO `logros` (`id`, `usuario_id`, `logro`, `descripcion`, `puntos`, `fecha_obtencion`) VALUES
(1, 3, 'Primer Simulación', 'Completar tu primera simulación clínica', 100, '2025-07-25 00:28:59'),
(2, 3, 'Diagnóstico Preciso', 'Obtener más del 90% de precisión en un diagnóstico', 200, '2025-07-25 00:28:59'),
(3, 4, 'Primer Simulación', 'Completar tu primera simulación clínica', 100, '2025-07-25 00:28:59'),
(4, 4, 'Comunicador Experto', 'Obtener más del 90% en habilidades comunicativas', 150, '2025-07-25 00:28:59'),
(5, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 00:29:46'),
(6, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 00:33:35'),
(7, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 00:35:44'),
(8, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 00:37:52'),
(9, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 00:44:28'),
(10, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 00:55:59'),
(11, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:05:49'),
(12, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:17:16'),
(13, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:23:15'),
(14, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:23:35'),
(15, NULL, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:27:15'),
(16, NULL, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:27:45'),
(17, NULL, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:27:53'),
(18, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:35:55'),
(19, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:41:49'),
(20, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:42:10'),
(21, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:42:15'),
(22, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:44:27'),
(23, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 01:50:51'),
(24, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:01:15'),
(25, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:05:40'),
(26, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:05:53'),
(27, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:12:23'),
(28, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:35:42'),
(29, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:35:59'),
(30, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 02:47:29'),
(31, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:15:42'),
(32, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:17:34'),
(33, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:20:17'),
(34, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:25:54'),
(35, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:26:08'),
(36, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:28:36'),
(37, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:38:23'),
(38, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:39:39'),
(39, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:39:52'),
(40, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:40:43'),
(41, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:54:35'),
(42, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 03:59:25'),
(43, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 04:01:29'),
(44, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 04:05:08'),
(45, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 04:08:30'),
(46, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 18:05:32'),
(47, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 18:06:01'),
(48, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 18:08:28'),
(49, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 18:10:26'),
(50, 5, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10, '2025-07-25 21:36:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `simulacion_id` int(11) DEFAULT NULL,
  `precision_diagnostica` decimal(5,2) DEFAULT NULL,
  `habilidades_comunicacion` decimal(5,2) DEFAULT NULL,
  `tiempo_respuesta_promedio` decimal(10,2) DEFAULT NULL,
  `areas_mejora` text DEFAULT NULL,
  `recomendaciones` text DEFAULT NULL,
  `datos_analisis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_analisis`)),
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id`, `usuario_id`, `simulacion_id`, `precision_diagnostica`, `habilidades_comunicacion`, `tiempo_respuesta_promedio`, `areas_mejora`, `recomendaciones`, `datos_analisis`, `fecha_generacion`) VALUES
(1, 3, 1, 85.50, 82.00, 45.50, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Cardiología', '{\"total_interacciones\": 8, \"preguntas_relevantes\": 6, \"tiempo_simulacion\": 1200, \"dificultad\": \"intermedio\"}', '2025-07-25 00:28:59'),
(2, 3, 2, 78.25, 85.50, 38.25, 'Profundizar más en los síntomas neurológicos', 'Revisar casos pediátricos de emergencia', '{\"total_interacciones\": 10, \"preguntas_relevantes\": 8, \"tiempo_simulacion\": 900, \"dificultad\": \"basico\"}', '2025-07-25 00:28:59'),
(3, 4, 1, 92.75, 88.50, 32.75, 'Excelente desempeño general', 'Continuar practicando casos de dificultad intermedia', '{\"total_interacciones\": 12, \"preguntas_relevantes\": 11, \"tiempo_simulacion\": 1500, \"dificultad\": \"intermedio\"}', '2025-07-25 00:28:59'),
(4, 5, 10, 91.00, 81.00, 83.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Cardiología', '{\"total_interacciones\":4,\"preguntas_relevantes\":1,\"tiempo_simulacion\":25302,\"dificultad\":\"intermedio\"}', '2025-07-25 00:57:41'),
(5, 5, 11, 88.00, 81.00, 41.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Pediatría', '{\"total_interacciones\":7,\"preguntas_relevantes\":2,\"tiempo_simulacion\":25431,\"dificultad\":\"basico\"}', '2025-07-25 01:09:40'),
(6, 5, 11, 91.00, 81.00, 30.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Pediatría', '{\"total_interacciones\":7,\"preguntas_relevantes\":2,\"tiempo_simulacion\":25709,\"dificultad\":\"basico\"}', '2025-07-25 01:14:18'),
(7, 5, 14, 75.00, 86.00, 71.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Cardiología', '{\"total_interacciones\":4,\"preguntas_relevantes\":1,\"tiempo_simulacion\":25293,\"dificultad\":\"intermedio\"}', '2025-07-25 01:25:08'),
(8, 5, 14, 92.00, 83.00, 41.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Cardiología', '{\"total_interacciones\":4,\"preguntas_relevantes\":1,\"tiempo_simulacion\":25417,\"dificultad\":\"intermedio\"}', '2025-07-25 01:27:12'),
(9, 5, 18, 87.00, 87.00, 54.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Dermatología', '{\"total_interacciones\":13,\"preguntas_relevantes\":4,\"tiempo_simulacion\":25503,\"dificultad\":\"basico\"}', '2025-07-25 01:40:58'),
(10, 5, 29, 75.00, 80.00, 60.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Cardiología', '{\"total_interacciones\":16,\"preguntas_relevantes\":5,\"tiempo_simulacion\":25650,\"dificultad\":\"intermedio\"}', '2025-07-25 02:43:29'),
(11, 5, 50, 85.00, 88.00, 102.00, 'Mejorar el orden de las preguntas y profundizar en antecedentes familiares', 'Practicar más casos de Pediatría', '{\"total_interacciones\":7,\"preguntas_relevantes\":2,\"tiempo_simulacion\":25288,\"dificultad\":\"basico\"}', '2025-07-25 21:37:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `simulaciones`
--

CREATE TABLE `simulaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `caso_id` int(11) DEFAULT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` timestamp NULL DEFAULT NULL,
  `estado` enum('en_progreso','completada','abandonada') DEFAULT 'en_progreso',
  `puntuacion` decimal(5,2) DEFAULT NULL,
  `tiempo_duracion` int(11) DEFAULT NULL,
  `datos_sesion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_sesion`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `simulaciones`
--

INSERT INTO `simulaciones` (`id`, `usuario_id`, `caso_id`, `fecha_inicio`, `fecha_fin`, `estado`, `puntuacion`, `tiempo_duracion`, `datos_sesion`) VALUES
(1, 3, 1, '2025-07-25 00:28:59', NULL, 'completada', 85.50, 1200, NULL),
(2, 3, 2, '2025-07-25 00:28:59', NULL, 'completada', 78.25, 900, NULL),
(3, 4, 1, '2025-07-25 00:28:59', NULL, 'completada', 92.75, 1500, NULL),
(4, 4, 3, '2025-07-25 00:28:59', NULL, 'en_progreso', NULL, NULL, NULL),
(5, 5, 1, '2025-07-25 00:29:46', NULL, 'en_progreso', NULL, NULL, NULL),
(6, 5, 2, '2025-07-25 00:33:35', NULL, 'en_progreso', NULL, NULL, NULL),
(7, 5, 1, '2025-07-25 00:35:44', NULL, 'en_progreso', NULL, NULL, NULL),
(8, 5, 4, '2025-07-25 00:37:52', NULL, 'en_progreso', NULL, NULL, NULL),
(9, 5, 2, '2025-07-25 00:44:28', NULL, 'en_progreso', NULL, NULL, NULL),
(10, 5, 1, '2025-07-25 00:55:59', '2025-07-25 00:57:41', 'completada', 87.00, 25302, NULL),
(11, 5, 2, '2025-07-25 01:05:49', '2025-07-25 01:14:18', 'completada', 100.00, 25709, NULL),
(12, 5, 1, '2025-07-25 01:17:16', NULL, 'en_progreso', NULL, NULL, NULL),
(13, 5, 1, '2025-07-25 01:23:15', NULL, 'en_progreso', NULL, NULL, NULL),
(14, 5, 1, '2025-07-25 01:23:35', '2025-07-25 01:27:12', 'completada', 88.00, 25417, NULL),
(15, NULL, 1, '2025-07-25 01:27:15', NULL, 'en_progreso', NULL, NULL, NULL),
(16, NULL, 5, '2025-07-25 01:27:45', NULL, 'en_progreso', NULL, NULL, NULL),
(17, NULL, 6, '2025-07-25 01:27:53', NULL, 'en_progreso', NULL, NULL, NULL),
(18, 5, 6, '2025-07-25 01:35:55', '2025-07-25 01:40:58', 'completada', 100.00, 25503, NULL),
(19, 5, 1, '2025-07-25 01:41:49', NULL, 'en_progreso', NULL, NULL, NULL),
(20, 5, 1, '2025-07-25 01:42:10', NULL, 'en_progreso', NULL, NULL, NULL),
(21, 5, 2, '2025-07-25 01:42:15', NULL, 'en_progreso', NULL, NULL, NULL),
(22, 5, 4, '2025-07-25 01:44:27', NULL, 'en_progreso', NULL, NULL, NULL),
(23, 5, 1, '2025-07-25 01:50:51', NULL, 'en_progreso', NULL, NULL, NULL),
(24, 5, 1, '2025-07-25 02:01:15', NULL, 'en_progreso', NULL, NULL, NULL),
(25, 5, 1, '2025-07-25 02:05:40', NULL, 'en_progreso', NULL, NULL, NULL),
(26, 5, 1, '2025-07-25 02:05:53', NULL, 'en_progreso', NULL, NULL, NULL),
(27, 5, 1, '2025-07-25 02:12:23', NULL, 'en_progreso', NULL, NULL, NULL),
(28, 5, 1, '2025-07-25 02:35:42', NULL, 'en_progreso', NULL, NULL, NULL),
(29, 5, 1, '2025-07-25 02:35:59', '2025-07-25 02:43:29', 'completada', 100.00, 25650, NULL),
(30, 5, 3, '2025-07-25 02:47:29', NULL, 'en_progreso', NULL, NULL, NULL),
(31, 5, 4, '2025-07-25 03:15:42', NULL, 'en_progreso', NULL, NULL, NULL),
(32, 5, 4, '2025-07-25 03:17:34', NULL, 'en_progreso', NULL, NULL, NULL),
(33, 5, 5, '2025-07-25 03:20:17', NULL, 'en_progreso', NULL, NULL, NULL),
(34, 5, 5, '2025-07-25 03:25:54', NULL, 'en_progreso', NULL, NULL, NULL),
(35, 5, 5, '2025-07-25 03:26:08', NULL, 'en_progreso', NULL, NULL, NULL),
(36, 5, 5, '2025-07-25 03:28:36', NULL, 'en_progreso', NULL, NULL, NULL),
(37, 5, 5, '2025-07-25 03:38:23', NULL, 'en_progreso', NULL, NULL, NULL),
(38, 5, 5, '2025-07-25 03:39:39', NULL, 'en_progreso', NULL, NULL, NULL),
(39, 5, 5, '2025-07-25 03:39:52', NULL, 'en_progreso', NULL, NULL, NULL),
(40, 5, 1, '2025-07-25 03:40:43', NULL, 'en_progreso', NULL, NULL, NULL),
(41, 5, 1, '2025-07-25 03:54:35', NULL, 'en_progreso', NULL, NULL, NULL),
(42, 5, 1, '2025-07-25 03:59:25', NULL, 'en_progreso', NULL, NULL, NULL),
(43, 5, 1, '2025-07-25 04:01:29', NULL, 'en_progreso', NULL, NULL, NULL),
(44, 5, 1, '2025-07-25 04:05:08', NULL, 'en_progreso', NULL, NULL, NULL),
(45, 5, 1, '2025-07-25 04:08:30', NULL, 'en_progreso', NULL, NULL, NULL),
(46, 5, 1, '2025-07-25 18:05:32', NULL, 'en_progreso', NULL, NULL, NULL),
(47, 5, 1, '2025-07-25 18:06:01', NULL, 'en_progreso', NULL, NULL, NULL),
(48, 5, 1, '2025-07-25 18:08:28', NULL, 'en_progreso', NULL, NULL, NULL),
(49, 5, 1, '2025-07-25 18:10:26', NULL, 'en_progreso', NULL, NULL, NULL),
(50, 5, 2, '2025-07-25 21:36:23', '2025-07-25 21:37:51', 'completada', 90.00, 25288, NULL);

--
-- Disparadores `simulaciones`
--
DELIMITER $$
CREATE TRIGGER `tr_contar_simulaciones` AFTER INSERT ON `simulaciones` FOR EACH ROW BEGIN
    INSERT INTO logros (usuario_id, logro, descripcion, puntos)
    VALUES (NEW.usuario_id, 'Simulación Iniciada', 'Iniciar una nueva simulación', 10)
    ON DUPLICATE KEY UPDATE puntos = puntos;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo` enum('estudiante','profesor','administrador') DEFAULT 'estudiante',
  `nivel` enum('basico','intermedio','avanzado') DEFAULT 'basico',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT 'default.png',
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `tipo`, `nivel`, `fecha_registro`, `avatar`, `ultimo_acceso`, `activo`) VALUES
(1, 'Administrador', 'admin@edumedia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 'avanzado', '2025-07-25 00:28:59', 'default.png', NULL, 1),
(2, 'Dr. Juan Pérez', 'juan@medico.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'profesor', 'avanzado', '2025-07-25 00:28:59', 'default.png', NULL, 1),
(3, 'María González', 'maria@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 'basico', '2025-07-25 00:28:59', 'default.png', NULL, 1),
(4, 'Carlos López', 'carlos@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 'intermedio', '2025-07-25 00:28:59', 'default.png', NULL, 1),
(5, 'Paul', 'admin@gmail.com', '$2y$10$9Tv3iaxIPVmTsgjFEMX/R.BSaYhTYK2M0HlJfeLQx.lAuE8Ob9rza', 'estudiante', 'basico', '2025-07-25 00:29:31', 'default.png', NULL, 1);

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_ultimo_acceso` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
    IF NEW.ultimo_acceso IS NULL THEN
        UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = NEW.id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_casos_populares`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_casos_populares` (
`id` int(11)
,`titulo` varchar(200)
,`especialidad` varchar(100)
,`dificultad` enum('basico','intermedio','avanzado')
,`veces_utilizado` bigint(21)
,`promedio_puntuacion` decimal(9,6)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_desempeno_usuario`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_desempeno_usuario` (
`usuario_id` int(11)
,`usuario_nombre` varchar(100)
,`especialidad` varchar(100)
,`simulaciones` bigint(21)
,`promedio_puntuacion` decimal(9,6)
,`promedio_comunicacion` decimal(9,6)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estadisticas_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_estadisticas_usuarios` (
`id` int(11)
,`nombre` varchar(100)
,`tipo` enum('estudiante','profesor','administrador')
,`nivel` enum('basico','intermedio','avanzado')
,`total_simulaciones` bigint(21)
,`simulaciones_completadas` bigint(21)
,`promedio_puntuacion` decimal(9,6)
,`ultima_actividad` timestamp
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_casos_populares`
--
DROP TABLE IF EXISTS `vista_casos_populares`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_casos_populares`  AS SELECT `c`.`id` AS `id`, `c`.`titulo` AS `titulo`, `c`.`especialidad` AS `especialidad`, `c`.`dificultad` AS `dificultad`, count(`s`.`id`) AS `veces_utilizado`, avg(`s`.`puntuacion`) AS `promedio_puntuacion` FROM (`casos_clinicos` `c` left join `simulaciones` `s` on(`c`.`id` = `s`.`caso_id`)) WHERE `c`.`activo` = 1 GROUP BY `c`.`id`, `c`.`titulo`, `c`.`especialidad`, `c`.`dificultad` ORDER BY count(`s`.`id`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_desempeno_usuario`
--
DROP TABLE IF EXISTS `vista_desempeno_usuario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_desempeno_usuario`  AS SELECT `u`.`id` AS `usuario_id`, `u`.`nombre` AS `usuario_nombre`, `c`.`especialidad` AS `especialidad`, count(`s`.`id`) AS `simulaciones`, avg(`s`.`puntuacion`) AS `promedio_puntuacion`, avg(`r`.`habilidades_comunicacion`) AS `promedio_comunicacion` FROM (((`usuarios` `u` join `simulaciones` `s` on(`u`.`id` = `s`.`usuario_id`)) join `casos_clinicos` `c` on(`s`.`caso_id` = `c`.`id`)) left join `reportes` `r` on(`s`.`id` = `r`.`simulacion_id`)) WHERE `s`.`estado` = 'completada' GROUP BY `u`.`id`, `u`.`nombre`, `c`.`especialidad` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estadisticas_usuarios`
--
DROP TABLE IF EXISTS `vista_estadisticas_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estadisticas_usuarios`  AS SELECT `u`.`id` AS `id`, `u`.`nombre` AS `nombre`, `u`.`tipo` AS `tipo`, `u`.`nivel` AS `nivel`, count(distinct `s`.`id`) AS `total_simulaciones`, count(distinct case when `s`.`estado` = 'completada' then `s`.`id` end) AS `simulaciones_completadas`, avg(`s`.`puntuacion`) AS `promedio_puntuacion`, max(`s`.`fecha_inicio`) AS `ultima_actividad` FROM (`usuarios` `u` left join `simulaciones` `s` on(`u`.`id` = `s`.`usuario_id`)) GROUP BY `u`.`id`, `u`.`nombre`, `u`.`tipo`, `u`.`nivel` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `casos_clinicos`
--
ALTER TABLE `casos_clinicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creador_id` (`creador_id`),
  ADD KEY `idx_especialidad` (`especialidad`),
  ADD KEY `idx_dificultad` (`dificultad`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_casos_especialidad_dificultad` (`especialidad`,`dificultad`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `interacciones`
--
ALTER TABLE `interacciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_simulacion_tipo` (`simulacion_id`,`tipo`),
  ADD KEY `idx_timestamp` (`timestamp`),
  ADD KEY `idx_interacciones_tipo_fecha` (`tipo`,`timestamp`);

--
-- Indices de la tabla `logros`
--
ALTER TABLE `logros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha_obtencion`),
  ADD KEY `idx_logro` (`logro`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `simulacion_id` (`simulacion_id`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha_generacion`),
  ADD KEY `idx_precision` (`precision_diagnostica`),
  ADD KEY `idx_reportes_precision_fecha` (`precision_diagnostica`,`fecha_generacion`);

--
-- Indices de la tabla `simulaciones`
--
ALTER TABLE `simulaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha_inicio`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_puntuacion` (`puntuacion`),
  ADD KEY `idx_simulaciones_fecha_estado` (`fecha_inicio`,`estado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_nivel` (`nivel`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `casos_clinicos`
--
ALTER TABLE `casos_clinicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `interacciones`
--
ALTER TABLE `interacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT de la tabla `logros`
--
ALTER TABLE `logros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `simulaciones`
--
ALTER TABLE `simulaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `casos_clinicos`
--
ALTER TABLE `casos_clinicos`
  ADD CONSTRAINT `casos_clinicos_ibfk_1` FOREIGN KEY (`creador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `interacciones`
--
ALTER TABLE `interacciones`
  ADD CONSTRAINT `interacciones_ibfk_1` FOREIGN KEY (`simulacion_id`) REFERENCES `simulaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `logros`
--
ALTER TABLE `logros`
  ADD CONSTRAINT `logros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reportes_ibfk_2` FOREIGN KEY (`simulacion_id`) REFERENCES `simulaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `simulaciones`
--
ALTER TABLE `simulaciones`
  ADD CONSTRAINT `simulaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `simulaciones_ibfk_2` FOREIGN KEY (`caso_id`) REFERENCES `casos_clinicos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
