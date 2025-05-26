// Dashboard JavaScript - Manejo de estadísticas y gráfica
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let cursosChart = null;
    const alertContainer = document.getElementById('alertContainer');
    
    // Obtener datos iniciales del PHP (insertados en la vista)
    const datosIniciales = {
        estadisticas: {
            total: parseInt(document.getElementById('totalCursos')?.textContent) || 0,
            asignados: parseInt(document.getElementById('cursosAsignados')?.textContent) || 0,
            completados: parseInt(document.getElementById('cursosCompletados')?.textContent) || 0
        }
    };
    
    // Inicializar dashboard
    init();
    
    function init() {
        // Crear gráfica con datos iniciales
        actualizarGrafica(datosIniciales.estadisticas);
        
        // Cargar estadísticas actualizadas desde el servidor
        cargarEstadisticas();
        
        // Actualizar estadísticas cada 5 minutos
        setInterval(cargarEstadisticas, 300000);
    }
    
    /**
     * Carga las estadísticas desde el servidor
     */
    async function cargarEstadisticas() {
        try {
            const baseUrl = window.location.origin + '/ProyectoFinalTecWeb/public/alumnos/dashboard/';
            const response = await fetch(baseUrl + 'estadisticas', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/ProyectoFinalTecWeb/public/login';
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const estadisticas = await response.json();
            
            if (estadisticas && typeof estadisticas === 'object') {
                actualizarEstadisticas(estadisticas);
                actualizarGrafica(estadisticas);
            } else {
                console.warn('Datos de estadísticas inválidos:', estadisticas);
            }
            
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
            if (document.getElementById('totalCursos').textContent === '0') {
                mostrarAlerta('Error al cargar las estadísticas del servidor', 'error');
            }
        }
    }
    
    /**
     * Actualiza los números de estadísticas en la UI
     */
    function actualizarEstadisticas(datos) {
        const totalElement = document.getElementById('totalCursos');
        const asignadosElement = document.getElementById('cursosAsignados');
        const completadosElement = document.getElementById('cursosCompletados');
        
        if (totalElement) {
            animarNumero(totalElement, datos.total || 0);
        }
        if (asignadosElement) {
            animarNumero(asignadosElement, datos.asignados || 0);
        }
        if (completadosElement) {
            animarNumero(completadosElement, datos.completados || 0);
        }
    }
    
    /**
     * Anima el cambio de números con efecto contador
     */
    function animarNumero(element, valorFinal) {
        const valorInicial = parseInt(element.textContent) || 0;
        const diferencia = valorFinal - valorInicial;
        const duracion = 1000; // 1 segundo
        const pasos = 20;
        const incremento = diferencia / pasos;
        let paso = 0;
        
        const timer = setInterval(() => {
            paso++;
            const valorActual = Math.round(valorInicial + (incremento * paso));
            element.textContent = valorActual;
            
            if (paso >= pasos) {
                clearInterval(timer);
                element.textContent = valorFinal;
            }
        }, duracion / pasos);
    }
    
    /**
     * Actualiza la gráfica de cursos
     */
    function actualizarGrafica(datos) {
        const ctx = document.getElementById('cursosChart');
        if (!ctx) return;
        
        if (cursosChart) {
            cursosChart.destroy();
        }
        
        const total = datos.total || 0;
        const completados = datos.completados || 0;
        const asignados = datos.asignados || 0;
        
        cursosChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completados', 'Asignados (Pendientes)', 'Sin Asignar'],
                datasets: [{
                    data: [
                        completados,
                        asignados - completados, // Cursos asignados pero no completados
                        Math.max(0, total - asignados) // Cursos totales menos los asignados
                    ],
                    backgroundColor: [
                        '#28a745', // Verde para completados
                        '#ffc107', // Amarillo para asignados/pendientes
                        '#dc3545'  // Rojo para sin asignar
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }
    
    /**
     * Muestra alertas al usuario (Mantenemos por si se usa para errores de carga de estadísticas)
     */
    function mostrarAlerta(mensaje, tipo = 'info') {
        if (!alertContainer) return;
        
        alertContainer.innerHTML = '';
        
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo === 'error' ? 'error' : 'success'}`;
        alerta.textContent = mensaje;
        alerta.style.display = 'block';
        
        alertContainer.appendChild(alerta);
        
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.style.opacity = '0';
                alerta.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    if (alerta.parentNode) {
                        alerta.remove();
                    }
                }, 300);
            }
        }, 5000);
        
        alerta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    /**
     * Manejo de errores de red
     */
    window.addEventListener('online', function() {
        mostrarAlerta('Conexión restaurada', 'success');
        cargarEstadisticas();
    });
    
    window.addEventListener('offline', function() {
        mostrarAlerta('Sin conexión a internet', 'error');
    });
    
    /**
     * Limpieza al cerrar la página
     */
    window.addEventListener('beforeunload', function() {
        if (cursosChart) {
            cursosChart.destroy();
        }
    });
    
    $('#compartirMiId').on('click', function(e) {
        e.preventDefault();
        let alumnoId = $('body').data('alumno-id');
        if (alumnoId) {
            alert("Comparte este ID con tu profesor por si lo necesita: " + alumnoId);
        } else {
            alert("No se pudo obtener tu ID. Asegúrate de haber iniciado sesión como alumno.");
        }
    });
});