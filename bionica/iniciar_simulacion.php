<?php
// Asegurarse de iniciar la sesion de forma robusta
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificacion adicional para asegurar que $_SESSION existe y el usuario esta logueado
if (!isset($_SESSION) || !isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/funciones.php';

$caso_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$caso = obtenerCasoPorId($caso_id);

if (!$caso) {
    die('Caso clínico no encontrado');
}

// Iniciar nueva simulación
$simulacion_id = iniciarSimulacion($_SESSION['usuario_id'], $caso_id);

// Guardar interacción inicial
$mensaje_bienvenida = "Hola doctor, soy " . ($caso['genero_paciente'] == 'masculino' ? 'el paciente' : 'la paciente') . " virtual. Me llamo Juan y tengo " . $caso['edad_paciente'] . " años. Necesito ayuda médica.";
guardarInteraccion($simulacion_id, 'respuesta', $mensaje_bienvenida);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulación - <?php echo htmlspecialchars($caso['titulo']); ?></title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sugerencia-mentor {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 10px;
            margin-top: 10px;
            border-radius: 0 8px 8px 0;
            font-size: 0.9em;
        }
        .btn-sugerencia {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
            margin-top: 5px;
        }
        .btn-sugerencia:hover {
            background-color: #0d8bf2;
        }
        /* Estilos para controles de idioma y voz */
        .controles-idioma-voz {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
            padding: 12px;
            background-color: #f0f4f8;
            border-radius: 8px;
            font-size: 0.9em;
            flex-wrap: wrap;
        }
        .control-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-voz {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85em;
        }
        .btn-voz:hover {
            background: #45a049;
        }
        .btn-voz.pausa {
             background: #f44336;
        }
        .btn-voz.pausa:hover {
             background: #d32f2f;
        }
        .selector-idioma {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 0.85em;
        }
        .slider-voz {
            width: 80px;
        }
        .indicador-traduciendo {
            color: #ff9800;
            font-size: 0.8em;
            margin-left: 5px;
        }
    </style>
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
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 class="card-title"><?php echo htmlspecialchars($caso['titulo']); ?></h1>
                    <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($caso['especialidad']); ?></p>
                    <p><strong>Dificultad:</strong>
                        <span class="badge badge-<?php echo $caso['dificultad']; ?>">
                            <?php echo ucfirst($caso['dificultad']); ?>
                        </span>
                    </p>
                     <!-- Contador de preguntas -->
                    <p id="contador-preguntas"><strong>Preguntas restantes:</strong> <span id="numero-preguntas">5</span></p>
                </div>
                <div>
                    <button class="btn btn-warning" onclick="finalizarSimulacion()">
                        <i class="fas fa-flag-checkered"></i> Finalizar Simulación
                    </button>
                </div>
            </div>
        </div>

        <div class="simulacion-container">
            <div class="chat-container">
                <div class="chat-header">
                    <i class="fas fa-comments"></i> Conversación Clínica (5 preguntas máx.)
                </div>
                <div class="chat-messages" id="chat-messages">
                    <div class="message message-paciente">
                        <strong>Paciente:</strong> <?php echo htmlspecialchars($mensaje_bienvenida); ?>
                    </div>
                </div>
                <div class="chat-input">
                    <input type="text" id="mensaje-input" placeholder="Escribe tu pregunta..." >
                    <button onclick="enviarMensaje()" id="btn-enviar-chat">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                 <!-- Controles de Idioma y Voz -->
                <div class="controles-idioma-voz">
                    <div class="control-group">
                        <span><i class="fas fa-globe"></i> Idioma:</span>
                        <select id="selector-idioma" class="selector-idioma">
                            <option value="español">Español</option>
                            <option value="aimara">Aimara</option>
                            <option value="quechua">Quechua</option>
                        </select>
                        <span id="indicador-traduciendo" class="indicador-traduciendo" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Traduciendo...
                        </span>
                    </div>
                    
                    <div class="control-group">
                        <span><i class="fas fa-volume-up"></i> Voz:</span>
                        <button id="btn-toggle-voz" class="btn-voz pausa" title="Reanudar/Pausar Voz">
                            <i class="fas fa-play"></i>
                        </button>
                        <label for="slider-velocidad">Velocidad:</label>
                        <input type="range" id="slider-velocidad" class="slider-voz" min="0.5" max="2" step="0.1" value="1">
                        <span id="valor-velocidad">1.0</span>
                        <span id="indicador-tts" class="indicador-traduciendo" style="display: none;">
                            <i class="fas fa-robot"></i> TTS
                        </span>
                    </div>
                </div>
            </div>

            <div class="avatar-container">
                <img src="assets/avatares/paciente_<?php echo strtolower($caso['especialidad']); ?>.png"
                     alt="Avatar Paciente" class="avatar-img"
                     onerror="this.src='assets/avatares/paciente_default.png'">
                <div class="avatar-info">
                    <h3>Paciente Virtual</h3>
                    <p><i class="fas fa-user"></i> <?php echo $caso['edad_paciente']; ?> años • <?php echo ucfirst($caso['genero_paciente']); ?></p>
                    <p style="margin-top: 1rem; font-size: 0.9rem;">
                        <strong><i class="fas fa-heartbeat"></i> Síntomas principales:</strong><br>
                        <?php echo htmlspecialchars($caso['sintomas_principales']); ?>
                    </p>
                    <div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Información
                        </h4>
                        <p style="font-size: 0.85rem; margin: 0;">
                            Tienes un máximo de <strong>5 preguntas</strong> para interactuar.
                            El Agente Mentor evaluará tus respuestas y te sugerirá la siguiente.
                        </p>
                         <p style="font-size: 0.85rem; margin: 0.5rem 0 0 0; color: #e74c3c; font-weight: bold;">
                            <i class="fas fa-exclamation-circle"></i> Preguntas restantes: <span id="info-numero-preguntas">5</span>
                        </p>
                        <p style="font-size: 0.85rem; margin: 0.5rem 0 0 0; color: #2196F3;">
                            <i class="fas fa-language"></i> Idioma actual: <span id="idioma-actual">Español</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Contexto base para los agentes
        const contextoSimulacion = {
            simulacion_id: <?php echo $simulacion_id; ?>,
            datos_caso: <?php echo $caso['datos_json']; ?>,
            especialidad: '<?php echo $caso['especialidad']; ?>',
            dificultad: '<?php echo $caso['dificultad']; ?>',
            edad_paciente: <?php echo $caso['edad_paciente']; ?>,
            genero_paciente: '<?php echo $caso['genero_paciente']; ?>'
        };

        let preguntasRestantes = 5; // Contador de preguntas
        let idiomaSeleccionado = 'español'; // Idioma por defecto

        const chatMessages = document.getElementById('chat-messages');
        const mensajeInput = document.getElementById('mensaje-input');
        const btnEnviarChat = document.getElementById('btn-enviar-chat');
        const selectorIdioma = document.getElementById('selector-idioma');
        const idiomaActualSpan = document.getElementById('idioma-actual');
        const indicadorTraduciendo = document.getElementById('indicador-traduciendo');
        const indicadorTTS = document.getElementById('indicador-tts');

        // --- Variables para la Voz ---
        let vozHabilitada = false; // Inicialmente pausada
        let synth = window.speechSynthesis;
        let utteranceEnCurso = null;
        let colaMensajesMentor = []; // Cola para mensajes del mentor que esperan a que termine la voz
        let esperandoFinVozPaciente = false; // Bandera para saber si estamos esperando que termine la voz del paciente
        const sliderVelocidad = document.getElementById('slider-velocidad');
        const valorVelocidad = document.getElementById('valor-velocidad');
        const btnToggleVoz = document.getElementById('btn-toggle-voz');

        // Verificar si la API de voz está disponible
        if (!synth) {
             console.warn("La API Web Speech no está disponible en este navegador.");
             btnToggleVoz.disabled = true;
             btnToggleVoz.title = "Voz no disponible";
        } else {
            // Inicializar velocidad
            sliderVelocidad.addEventListener('input', function() {
                valorVelocidad.textContent = this.value;
            });
        }

        // Evento para cambio de idioma
        selectorIdioma.addEventListener('change', function() {
            idiomaSeleccionado = this.value;
            idiomaActualSpan.textContent = this.options[this.selectedIndex].text;
            console.log('Idioma cambiado a:', idiomaSeleccionado);
        });

        // Función para alternar la voz
        btnToggleVoz.addEventListener('click', function() {
            vozHabilitada = !vozHabilitada;
            if (vozHabilitada) {
                this.classList.remove('pausa');
                this.innerHTML = '<i class="fas fa-pause"></i>';
                this.title = "Pausar Voz";
            } else {
                this.classList.add('pausa');
                this.innerHTML = '<i class="fas fa-play"></i>';
                this.title = "Reanudar Voz";
                // Detener cualquier voz en curso
                if (synth.speaking) {
                    synth.cancel();
                    utteranceEnCurso = null;
                    esperandoFinVozPaciente = false;
                    // Limpiar la cola de mensajes del mentor
                    colaMensajesMentor = [];
                }
            }
        });

        // Función para obtener el código de idioma para síntesis de voz
        function getVoiceLanguageCode(idioma) {
            const codes = {
                'español': 'es-ES',
                'aimara': 'es-BO', // Fallback a español de Bolivia
                'quechua': 'es-PE'  // Fallback a español de Perú
            };
            return codes[idioma] || 'es-ES';
        }

        // Función mejorada para hablar texto con Google TTS o fallback a navegador
        async function hablarTexto(texto, callback = null) {
            if (!vozHabilitada) {
                if (callback) callback();
                return;
            }

            if (texto === '') {
                if (callback) callback();
                return;
            }

            // Limpiar el texto de etiquetas HTML para la voz
            const textoLimpio = texto.replace(/<[^>]*>?/gm, '');
            
            // Detener cualquier voz anterior
            if (synth && synth.speaking) {
                synth.cancel();
            }
            
            // Si el idioma es español, usar directamente el navegador
            if (idiomaSeleccionado === 'español') {
                usarVozNavegador(textoLimpio, callback);
                return;
            }
            
            // Para Aimara y Quechua, intentar usar Google TTS primero
            try {
                // Mostrar indicador de que se está usando TTS
                if (indicadorTTS) {
                    indicadorTTS.style.display = 'inline';
                    indicadorTTS.innerHTML = '<i class="fas fa-robot fa-spin"></i> Generando...';
                }
                
                const response = await fetch('api/sintetizar_voz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        text: textoLimpio,
                        language: idiomaSeleccionado
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.audio_base64) {
                    // Actualizar indicador
                    if (indicadorTTS) {
                        indicadorTTS.innerHTML = '<i class="fas fa-robot"></i> Google TTS';
                    }
                    // Reproducir audio de Google TTS
                    reproducirAudioBase64(data.audio_base64, callback);
                } else {
                    // Fallback a voz del navegador
                    if (indicadorTTS) {
                        indicadorTTS.innerHTML = '<i class="fas fa-volume-up"></i> Navegador';
                    }
                    console.log('Fallback a voz del navegador para ' + idiomaSeleccionado);
                    usarVozNavegador(textoLimpio, callback);
                }
                
                // Ocultar indicador después de 3 segundos
                setTimeout(() => {
                    if (indicadorTTS) indicadorTTS.style.display = 'none';
                }, 3000);
                
            } catch (error) {
                console.error('Error con Google TTS, usando fallback:', error);
                if (indicadorTTS) {
                    indicadorTTS.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error TTS';
                    setTimeout(() => {
                        indicadorTTS.style.display = 'none';
                    }, 3000);
                }
                // Fallback a voz del navegador
                usarVozNavegador(textoLimpio, callback);
            }
        }
        
        // Función para usar la voz del navegador (fallback)
        function usarVozNavegador(texto, callback = null) {
            if (!synth) {
                if (callback) callback();
                return;
            }
            
            const utterThis = new SpeechSynthesisUtterance(texto);
            utterThis.lang = getVoiceLanguageCode(idiomaSeleccionado);
            utterThis.pitch = 1;
            utterThis.rate = parseFloat(sliderVelocidad.value);

            utterThis.onend = function (event) {
                utteranceEnCurso = null;
                if (callback) callback();
            }

            utterThis.onerror = function (event) {
                console.error('Error en la voz:', event);
                utteranceEnCurso = null;
                if (callback) callback();
            }

            utteranceEnCurso = utterThis;
            synth.speak(utterThis);
        }
        
        // Función para reproducir audio desde Base64
        function reproducirAudioBase64(audioBase64, callback = null) {
            try {
                // Crear blob de audio desde Base64
                const audioData = atob(audioBase64);
                const arrayBuffer = new ArrayBuffer(audioData.length);
                const uint8Array = new Uint8Array(arrayBuffer);
                
                for (let i = 0; i < audioData.length; i++) {
                    uint8Array[i] = audioData.charCodeAt(i);
                }
                
                const audioBlob = new Blob([uint8Array], { type: 'audio/mp3' });
                const audioUrl = URL.createObjectURL(audioBlob);
                
                // Crear elemento de audio y reproducir
                const audio = new Audio(audioUrl);
                audio.playbackRate = parseFloat(sliderVelocidad.value);
                
                audio.onended = function() {
                    URL.revokeObjectURL(audioUrl); // Limpiar memoria
                    if (callback) callback();
                };
                
                audio.onerror = function(e) {
                    console.error('Error reproduciendo audio TTS:', e);
                    URL.revokeObjectURL(audioUrl);
                    if (callback) callback();
                };
                
                audio.play().catch(error => {
                    console.error('Error al reproducir audio:', error);
                    URL.revokeObjectURL(audioUrl);
                    if (callback) callback();
                });
                
            } catch (error) {
                console.error('Error procesando audio Base64:', error);
                if (callback) callback();
            }
        }

        // Función para procesar la cola de mensajes del Mentor
        function procesarColaMentor() {
            if (colaMensajesMentor.length > 0 && !esperandoFinVozPaciente && !synth.speaking) {
                const mensajeMentor = colaMensajesMentor.shift();
                // Mostrar y leer el mensaje del Mentor
                agregarMensaje('mentor', mensajeMentor.feedback);
                
                // Mostrar sugerencia si existe
                if (mensajeMentor.pregunta_sugerida) {
                    const sugerenciaDiv = document.createElement('div');
                    sugerenciaDiv.className = 'sugerencia-mentor';
                    sugerenciaDiv.innerHTML = `
                        <strong><i class="fas fa-lightbulb"></i> Sugerencia del Mentor:</strong> 
                        "${mensajeMentor.pregunta_sugerida}"
                        <br>
                        <button class="btn-sugerencia" onclick="usarSugerencia('${mensajeMentor.pregunta_sugerida.replace(/'/g, "\\'")}')">
                            <i class="fas fa-paper-plane"></i> Usar esta pregunta
                        </button>
                    `;
                    const mensajeSugerencia = document.createElement('div');
                    mensajeSugerencia.className = 'message message-sistema';
                    mensajeSugerencia.appendChild(sugerenciaDiv);
                    chatMessages.appendChild(mensajeSugerencia);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                
                // Guardar feedback en la BD (usar el texto original para almacenamiento)
                const feedbackParaGuardar = mensajeMentor.feedback_original || mensajeMentor.feedback;
                const sugerenciaParaGuardar = mensajeMentor.pregunta_sugerida_original || mensajeMentor.pregunta_sugerida;
                
                fetch('api/guardar_interaccion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        simulacion_id: contextoSimulacion.simulacion_id,
                        tipo: 'feedback',
                        contenido: feedbackParaGuardar + (sugerenciaParaGuardar ? " Sugerencia: " + sugerenciaParaGuardar : "")
                    })
                }).catch(error => console.error('Error guardando feedback:', error));
            }
        }

        function actualizarContador() {
            document.getElementById('numero-preguntas').textContent = preguntasRestantes;
            document.getElementById('info-numero-preguntas').textContent = preguntasRestantes;

            if (preguntasRestantes <= 0) {
                // Deshabilitar input y botón
                mensajeInput.disabled = true;
                btnEnviarChat.disabled = true;
                mensajeInput.placeholder = "Límite de 5 preguntas alcanzado";
                agregarMensaje('sistema', '🏁 Límite de 5 preguntas alcanzado. Finaliza la simulación cuando desees.');
            }
        }

        // Inicializar el contador en la UI al cargar la página
        actualizarContador();

        async function enviarMensaje() {
            if (preguntasRestantes <= 0) {
                 agregarMensaje('sistema', '❌ Ya has realizado 5 preguntas. No puedes hacer más.');
                return;
            }

            const mensaje = mensajeInput.value.trim();
            if (!mensaje) return;

            // Mostrar mensaje del estudiante
            agregarMensaje('estudiante', mensaje);
            mensajeInput.value = '';

            // Guardar interacción en la base de datos
            try {
                await fetch('api/guardar_interaccion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        simulacion_id: contextoSimulacion.simulacion_id,
                        tipo: 'pregunta',
                        contenido: mensaje
                    })
                });
            } catch (error) {
                console.error('Error al guardar interacción:', error);
            }

            // Reducir el contador de preguntas
            preguntasRestantes--;
            actualizarContador();

            // Mostrar indicador de "escribiendo..."
            const escribiendoDiv = document.createElement('div');
            escribiendoDiv.className = 'message message-sistema';
            escribiendoDiv.id = 'escribiendo-indicador';
            escribiendoDiv.innerHTML = '<strong>Paciente:</strong> <i class="fas fa-circle-notch fa-spin"></i> Escribiendo...';
            chatMessages.appendChild(escribiendoDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Obtener respuesta del Agente Paciente vía API OpenAI
            try {
                const response = await fetch('api/agente_paciente_openai.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        accion: 'responder',
                        mensaje: mensaje,
                        contexto: contextoSimulacion,
                        idioma: idiomaSeleccionado
                    })
                });

                // Remover indicador de "escribiendo"
                const indicador = document.getElementById('escribiendo-indicador');
                if (indicador) indicador.remove();

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Activar la bandera para esperar el fin de la voz del paciente
                    esperandoFinVozPaciente = true;

                    // Mostrar y leer la respuesta del paciente (traducida), luego procesar el mentor
                    agregarMensaje('paciente', data.respuesta, function() {
                        // Guardar respuesta del paciente en la BD (usar texto original)
                        const respuestaParaGuardar = data.respuesta_original || data.respuesta;
                        fetch('api/guardar_interaccion.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                simulacion_id: contextoSimulacion.simulacion_id,
                                tipo: 'respuesta',
                                contenido: respuestaParaGuardar
                            })
                        }).catch(error => console.error('Error guardando respuesta:', error));
                    });

                    // Mostrar indicador de análisis del mentor
                    const analizandoDiv = document.createElement('div');
                    analizandoDiv.className = 'message message-sistema';
                    analizandoDiv.id = 'analizando-indicador';
                    analizandoDiv.innerHTML = '<strong>Mentor IA:</strong> <i class="fas fa-circle-notch fa-spin"></i> Analizando...';
                    chatMessages.appendChild(analizandoDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    // Obtener análisis del Agente Mentor
                    setTimeout(async () => {
                        const analisisResponse = await fetch('api/agente_mentor_openai.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                accion: 'analizar_pregunta',
                                pregunta: mensaje,
                                contexto: contextoSimulacion,
                                idioma: idiomaSeleccionado
                            })
                        });

                        // Remover indicador de análisis
                        const indicadorAnalisis = document.getElementById('analizando-indicador');
                        if (indicadorAnalisis) indicadorAnalisis.remove();

                        if (!analisisResponse.ok) {
                            throw new Error(`HTTP error! status: ${analisisResponse.status}`);
                        }

                        const analisisData = await analisisResponse.json();

                        if (analisisData.success) {
                            // En lugar de mostrar inmediatamente, lo ponemos en la cola
                            colaMensajesMentor.push({
                                feedback: analisisData.feedback,
                                feedback_original: analisisData.feedback_original,
                                pregunta_sugerida: analisisData.pregunta_sugerida,
                                pregunta_sugerida_original: analisisData.pregunta_sugerida_original
                            });
                            
                            // Intentar procesar la cola (si ya terminó la voz del paciente)
                            if (!esperandoFinVozPaciente && !synth.speaking) {
                                procesarColaMentor();
                            }
                        }
                    }, 2000);
                } else {
                    // Remover indicadores si hay error
                    const indicador = document.getElementById('escribiendo-indicador');
                    if (indicador) indicador.remove();

                    agregarMensaje('sistema', 'Error: ' + (data.error || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error de comunicación:', error);
                // Remover indicadores si hay error
                const indicador = document.getElementById('escribiendo-indicador');
                if (indicador) indicador.remove();

                agregarMensaje('sistema', '❌ Error en la comunicación con los agentes. (' + error.message + ')');
            }
        }

        // Función para usar la sugerencia del mentor
        function usarSugerencia(pregunta) {
            mensajeInput.value = pregunta;
            // Opcional: enviar automáticamente
            // enviarMensaje();
        }

        // Función modificada para agregar mensajes y hablarlos, con callback opcional
        function agregarMensaje(tipo, contenido, callback = null) {
            const div = document.createElement('div');
            div.className = `message message-${tipo}`;
            div.innerHTML = `<strong>${tipo === 'paciente' ? 'Paciente' : tipo === 'estudiante' ? 'Tú' : tipo === 'mentor' ? 'Mentor IA' : 'Sistema'}:</strong> ${contenido}`;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Si es un mensaje del paciente o del mentor, leerlo en voz alta
            if ((tipo === 'paciente' || tipo === 'mentor') && vozHabilitada) {
                 // Extraer solo el texto del contenido (sin el <strong>)
                 const textoParaLeer = contenido.replace(/<[^>]*>?/gm, '');
                 
                 // Si es el paciente, al terminar la voz, procesamos el mentor
                 if (tipo === 'paciente') {
                    hablarTexto(textoParaLeer, function() {
                        esperandoFinVozPaciente = false;
                        // Intentar procesar cualquier mensaje del mentor en cola
                        procesarColaMentor();
                        if (callback) callback();
                    });
                 } else {
                     // Si es el mentor, simplemente lo lee
                     hablarTexto(textoParaLeer, callback);
                 }
            } else {
                // Si la voz está deshabilitada o es otro tipo de mensaje, llamar al callback inmediatamente
                if (callback) callback();
                
                // También intentar procesar la cola del mentor por si acaso
                if (tipo !== 'paciente') { // Evitar bucles innecesarios
                     procesarColaMentor();
                }
            }
        }

        function finalizarSimulacion() {
            if (confirm('¿Finalizar simulación?')) {
                // Detener cualquier voz antes de salir
                if (synth && synth.speaking) {
                    synth.cancel();
                }
                window.location.href = 'generar_reporte.php?id=' + contextoSimulacion.simulacion_id;
            }
        }

        mensajeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensaje();
            }
        });
        
        // Verificación periódica para procesar la cola del mentor (fallback)
        setInterval(procesarColaMentor, 1000);

        // Traducir el mensaje de bienvenida inicial si el idioma no es español
        window.addEventListener('load', function() {
            if (idiomaSeleccionado !== 'español') {
                // Aquí podrías traducir el mensaje de bienvenida inicial si es necesario
            }
        });
    </script>
</body>
</html>