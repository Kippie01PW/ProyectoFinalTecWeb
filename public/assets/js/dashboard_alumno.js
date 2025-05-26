
document.addEventListener('DOMContentLoaded', function() {

    let cursosChart = null;
    const alertContainer = document.getElementById('alertContainer');
    

    const datosIniciales = {
        estadisticas: {
            total: parseInt(document.getElementById('totalCursos')?.textContent) || 0,
            asignados: parseInt(document.getElementById('cursosAsignados')?.textContent) || 0,
            completados: parseInt(document.getElementById('cursosCompletados')?.textContent) || 0
        }
    };
    

    init();
    
    function init() {

        actualizarGrafica(datosIniciales.estadisticas);
        
        cargarEstadisticas();

        setInterval(cargarEstadisticas, 300000);
    }
    

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
    

    function animarNumero(element, valorFinal) {
        const valorInicial = parseInt(element.textContent) || 0;
        const diferencia = valorFinal - valorInicial;
        const duracion = 1000; 
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
                labels: ['Completados', 'Asignados (Pendientes)'],
                datasets: [{
                    data: [
                        completados,
                        asignados , 
                        
                    ],
                    

                   backgroundColor: [
                       '#29b6f6', 
                       '#4dd0e1', 
                       '#80deea'  
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
    
   
    window.addEventListener('online', function() {
        mostrarAlerta('Conexión restaurada', 'success');
        cargarEstadisticas();
    });
    
    window.addEventListener('offline', function() {
        mostrarAlerta('Sin conexión a internet', 'error');
    });

    window.addEventListener('beforeunload', function() {
        if (cursosChart) {
            cursosChart.destroy();
        }
    });
});