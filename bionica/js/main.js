// Funciones generales para toda la aplicación

document.addEventListener('DOMContentLoaded', function() {
    // Manejo de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            }
        });
    });

    // Manejo de mensajes de alerta
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(alerta => {
        setTimeout(() => {
            alerta.style.opacity = '0';
            setTimeout(() => {
                alerta.remove();
            }, 300);
        }, 5000);
    });
});

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo}`;
    notificacion.innerHTML = `<i class="fas ${tipo === 'success' ? 'fa-check-circle' : tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i> ${mensaje}`;
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        border-radius: 8px;
        background-color: ${tipo === 'success' ? '#d4edda' : tipo === 'error' ? '#f8d7da' : '#cce5ff'};
        color: ${tipo === 'success' ? '#155724' : tipo === 'error' ? '#721c24' : '#004085'};
        border: 1px solid ${tipo === 'success' ? '#c3e6cb' : tipo === 'error' ? '#f5c6cb' : '#b8daff'};
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 400px;
    `;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.style.opacity = '0';
        setTimeout(() => {
            notificacion.remove();
        }, 300);
    }, 3000);
}

// Función para validar formularios
function validarFormulario(form) {
    let valido = true;
    const campos = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    campos.forEach(campo => {
        if (!campo.value.trim()) {
            campo.style.borderColor = '#dc3545';
            valido = false;
        } else {
            campo.style.borderColor = '#ced4da';
        }
    });
    
    return valido;
}

// Función para cargar contenido dinámico
function cargarContenido(url, contenedorId) {
    const contenedor = document.getElementById(contenedorId);
    if (contenedor) {
        contenedor.innerHTML = '<div class="loading"><div class="loading-spinner"></div>Cargando...</div>';
        
        fetch(url)
            .then(response => response.text())
            .then(data => {
                contenedor.innerHTML = data;
            })
            .catch(error => {
                contenedor.innerHTML = '<div class="alert alert-error">Error al cargar el contenido</div>';
                console.error('Error:', error);
            });
    }
}

// Función para confirmar acciones peligrosas
function confirmarAccion(mensaje, callback) {
    if (confirm(mensaje)) {
        if (typeof callback === 'function') {
            callback();
        }
    }
}

// Función para formatear números como porcentajes
function formatearPorcentaje(numero) {
    return Math.round(numero) + '%';
}

// Función para formatear tiempo
function formatearTiempo(segundos) {
    const minutos = Math.floor(segundos / 60);
    const segundosRestantes = segundos % 60;
    return `${minutos}m ${segundosRestantes}s`;
}

// Función para copiar texto al portapapeles
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        mostrarNotificacion('Texto copiado al portapapeles', 'success');
    }).catch(err => {
        mostrarNotificacion('Error al copiar: ' + err, 'error');
    });
}

// Función para exportar datos (simulación)
function exportarDatos(datos, nombreArchivo) {
    const blob = new Blob([JSON.stringify(datos, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = nombreArchivo + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}