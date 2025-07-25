# Sistema BIONICA - Plataforma Educativa Médica con IA

BIONICA es una plataforma educativa innovadora que utiliza Inteligencia Artificial para simular consultas clínicas reales, permitiendo a estudiantes de medicina y enfermería practicar habilidades diagnósticas y comunicativas de forma segura e inmersiva.

## 🚀 Características Principales

*   **Simulación Realista:** Interacción con un "Paciente Virtual" impulsado por IA que responde de forma realista.
*   **Agentes Inteligentes Cooperativos:**
    *   **Agente Paciente:** Simula síntomas, emociones y comportamientos.
    *   **Agente Educador:** Analiza preguntas, brinda retroalimentación inmediata y sugiere la próxima pregunta.
*   **Límite de Preguntas:** Sesiones estructuradas con un máximo de 5 preguntas para fomentar la eficiencia.
*   **Retroalimentación Inteligente:** Análisis automático del desempeño con sugerencias personalizadas.
*   **Interfaz Intuitiva:** Chat conversacional, avatar del paciente y controles sencillos.
*   **Gestión de Casos:** Base de datos configurable de casos clínicos por especialidad y dificultad.
*   **Persistencia de Datos:** Registro de simulaciones e interacciones en una base de datos MySQL.

## 🛠️ Tecnologías Utilizadas

*   **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
*   **Backend:** PHP
*   **Base de Datos:** MySQL
*   **IA Conversacional:** OpenAI API (para lógica de agentes)
*   **Síntesis de Voz:** Google Cloud Text-to-Speech API (opcional, para audios en español, aymara, quechua)
*   **APIs:** Comunicación basada en REST y JSON
*   **Servidor:** Apache/Nginx

## 📋 Requisitos

*   Servidor web (Apache/Nginx)
*   PHP 7.4 o superior
*   MySQL 5.7 o superior
*   Conexión a internet (para servicios de IA)
*   Navegador web moderno

## 📦 Instalación

1.  **Clonar el repositorio:**
    ```bash
    git clone https://tu-repositorio/bionica.git
    cd bionica
    ```
2.  **Configurar la Base de Datos:**
    *   Crear una base de datos MySQL.
    *   Importar el esquema desde `database/edumedia.sql`.
3.  **Configurar la Aplicación:**
    *   Editar `includes/conexion.php` con tus credenciales de base de datos.
    *   (Opcional) Configurar claves API en `includes/openai_config.php` y `api/tts_google.php`.
4.  **Desplegar:**
    *   Colocar los archivos en tu servidor web.

## 🧪 Uso

1.  **Registro/Inicio de Sesión:** Accede a la plataforma mediante `index.php`.
2.  **Seleccionar un Caso:** Ve a "Simulaciones" y elige un caso clínico.
3.  **Iniciar Simulación:** Comienza la interacción con el paciente virtual.
4.  **Interactuar:** Escribe preguntas (máximo 5) en el chat.
5.  **Recibir Feedback:** El Agente Educador analiza tus preguntas y ofrece sugerencias.
6.  **Finalizar:** Termina la simulación para generar un reporte de desempeño.

## 📁 Estructura del Proyecto
bionica/
├── api/ # APIs para agentes y servicios (PHP)
├── assets/ # Imágenes, avatares
├── css/ # Hojas de estilo
├── database/ # Scripts de base de datos (.sql)
├── includes/ # Conexión a BD, funciones comunes (PHP)
├── js/ # Scripts de JavaScript
├── index.php # Página principal
├── login.php # Inicio de sesión
├── registro.php # Registro de usuarios
├── dashboard.php # Panel de control del usuario
├── simulacion.php # Lista de casos clínicos
├── iniciar_simulacion.php # Núcleo de la simulación interactiva
├── perfil.php # Gestión de perfil de usuario
├── logout.php # Cierre de sesión
└── generar_reporte.php # Generación de reportes
