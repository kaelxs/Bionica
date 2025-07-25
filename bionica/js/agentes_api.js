// Cliente JavaScript para interactuar con los agentes API
class ClienteAgentes {
    constructor() {
        // Asegúrate de que la ruta sea correcta
        this.baseUrl = 'api/'; // Sin /edumedia/ si estás en la raíz
    }
    
    // Interactuar con el Agente Paciente
    async obtenerRespuestaPaciente(mensaje, contexto) {
        try {
            const response = await fetch(this.baseUrl + 'agente_paciente.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accion: 'responder',
                    mensaje: mensaje,
                    contexto: contexto
                })
            });
            
            // Verificar si la respuesta es válida
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                return {
                    respuesta: data.respuesta,
                    timestamp: data.timestamp
                };
            } else {
                throw new Error(data.error || 'Error en el agente paciente');
            }
        } catch (error) {
            console.error('Error al comunicarse con el Agente Paciente:', error);
            return {
                respuesta: "Lo siento, no puedo responder en este momento.",
                error: true
            };
        }
    }
    
    // Obtener análisis del Agente Mentor
    async obtenerAnalisisMentor(pregunta, contexto, tipo = 'pregunta') {
        try {
            const response = await fetch(this.baseUrl + 'agente_mentor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accion: tipo === 'pregunta' ? 'analizar_pregunta' : 'evaluar_diagnostico',
                    pregunta: pregunta,
                    diagnostico: pregunta, // Para diagnóstico
                    contexto: contexto
                })
            });
            
            // Verificar si la respuesta es válida
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                return {
                    analisis: data.analisis || data.evaluacion,
                    feedback: data.feedback
                };
            } else {
                throw new Error(data.error || 'Error en el agente mentor');
            }
        } catch (error) {
            console.error('Error al comunicarse con el Agente Mentor:', error);
            return {
                analisis: null,
                feedback: "No se pudo generar el análisis en este momento.",
                error: true
            };
        }
    }
    
    // Guardar interacción
    async guardarInteraccion(simulacion_id, tipo, contenido) {
        try {
            const response = await fetch(this.baseUrl + 'guardar_interaccion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    simulacion_id: simulacion_id,
                    tipo: tipo,
                    contenido: contenido
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Error al guardar interacción:', error);
            return { success: false, error: error.message };
        }
    }
}

// Singleton para usar en toda la aplicación
const clienteAgentes = new ClienteAgentes();