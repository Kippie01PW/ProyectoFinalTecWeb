// Dashboard JavaScript - Manejo completo de funcionalidades
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
        
        // Configurar event listeners para formularios
        configurarFormularios();
        
        // Actualizar estadísticas cada 5 minutos
        setInterval(cargarEstadisticas, 300000);
    }
    
    /**
     * Carga las estadísticas desde el servidor
     */
    async function cargarEstadisticas() {
        try {
            // Usar la ruta completa basada en la estructura del proyecto
            const baseUrl = window.location.origin + '/ProyectoFinalTecWeb/public/dashboard/';
            const response = await fetch(baseUrl + 'estadisticas', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin' // Incluir cookies de sesión
            });
            
            if (!response.ok) {
                if (response.status === 401) {
                    // Usuario no autorizado, redirigir al login
                    window.location.href = '/ProyectoFinalTecWeb/public/login';
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const estadisticas = await response.json();
            
            // Verificar que los datos sean válidos
            if (estadisticas && typeof estadisticas === 'object') {
                actualizarEstadisticas(estadisticas);
                actualizarGrafica(estadisticas);
            } else {
                console.warn('Datos de estadísticas inválidos:', estadisticas);
            }
            
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
            // No mostrar error si es la primera carga, usar datos iniciales
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
        
        // Destruir gráfica anterior si existe
        if (cursosChart) {
            cursosChart.destroy();
        }
        
        const total = datos.total || 0;
        const completados = datos.completados || 0;
        const asignados = datos.asignados || 0;
        const pendientes = total - completados;
        
        cursosChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completados', 'Pendientes', 'Sin Asignar'],
                datasets: [{
                    data: [
                        completados,
                        asignados - completados,
                        Math.max(0, total - asignados)
                    ],
                    backgroundColor: [
                        '#28a745', // Verde para completados
                        '#ffc107', // Amarillo para pendientes
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
     * Configura los event listeners para los formularios
     */
    function configurarFormularios() {
        // Formulario de perfil
        const perfilForm = document.getElementById('perfilForm');
        if (perfilForm) {
            perfilForm.addEventListener('submit', manejarActualizacionPerfil);
        }
        
        // Formulario de contraseña
        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.addEventListener('submit', manejarActualizacionPassword);
        }
        
        // Validación en tiempo real para confirmación de contraseña
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        if (passwordInput && confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validarConfirmacionPassword);
        }
    }
    
    /**
     * Maneja la actualización del perfil
     */
    async function manejarActualizacionPerfil(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const datos = {
            nombre: formData.get('nombre').trim(),
            email: formData.get('email').trim()
        };
        
        // Validación del lado cliente
        if (!validarDatosPerfil(datos)) {
            return;
        }
        
        // Deshabilitar botón durante la petición
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const textoOriginal = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Actualizando...';
        
        try {
            const baseUrl = window.location.origin + '/ProyectoFinalTecWeb/public/dashboard/';
            const response = await fetch(baseUrl + 'actualizar-perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(datos)
            });
            
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/ProyectoFinalTecWeb/public/login';
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const resultado = await response.json();
            
            if (resultado.success) {
                mostrarAlerta(resultado.message, 'success');
                // Actualizar avatar si cambió el nombre
                actualizarAvatar(datos.nombre);
                // Actualizar título de bienvenida
                actualizarTituloBienvenida(datos.nombre);
            } else {
                mostrarAlerta(resultado.message, 'error');
            }
            
        } catch (error) {
            console.error('Error al actualizar perfil:', error);
            mostrarAlerta('Error de conexión al actualizar el perfil', 'error');
        } finally {
            // Rehabilitar botón
            submitBtn.disabled = false;
            submitBtn.textContent = textoOriginal;
        }
    }
    
    /**
     * Maneja la actualización de contraseña
     */
    async function manejarActualizacionPassword(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const datos = {
            password: formData.get('password'),
            confirm_password: formData.get('confirm_password')
        };
        
        // Validación del lado cliente
        if (!validarDatosPassword(datos)) {
            return;
        }
        
        // Deshabilitar botón durante la petición
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const textoOriginal = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Actualizando...';
        
        try {
            const baseUrl = window.location.origin + '/ProyectoFinalTecWeb/public/dashboard/';
            const response = await fetch(baseUrl + 'actualizar-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(datos)
            });
            
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/ProyectoFinalTecWeb/public/login';
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const resultado = await response.json();
            
            if (resultado.success) {
                mostrarAlerta(resultado.message, 'success');
                // Limpiar formulario
                event.target.reset();
            } else {
                mostrarAlerta(resultado.message, 'error');
            }
            
        } catch (error) {
            console.error('Error al actualizar contraseña:', error);
            mostrarAlerta('Error de conexión al actualizar la contraseña', 'error');
        } finally {
            // Rehabilitar botón
            submitBtn.disabled = false;
            submitBtn.textContent = textoOriginal;
        }
    }
    
    /**
     * Valida los datos del perfil
     */
    function validarDatosPerfil(datos) {
        const errores = [];
        
        if (!datos.nombre || datos.nombre.length < 2) {
            errores.push('El nombre debe tener al menos 2 caracteres');
        }
        
        if (!datos.email) {
            errores.push('El email es obligatorio');
        } else if (!validarEmail(datos.email)) {
            errores.push('El formato del email no es válido');
        }
        
        if (errores.length > 0) {
            mostrarAlerta(errores.join(', '), 'error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida los datos de contraseña
     */
    function validarDatosPassword(datos) {
        const errores = [];
        
        if (!datos.password) {
            errores.push('La contraseña es obligatoria');
        } else if (datos.password.length < 6) {
            errores.push('La contraseña debe tener al menos 6 caracteres');
        }
        
        if (datos.password !== datos.confirm_password) {
            errores.push('Las contraseñas no coinciden');
        }
        
        if (errores.length > 0) {
            mostrarAlerta(errores.join(', '), 'error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida formato de email
     */
    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    /**
     * Validación en tiempo real para confirmación de contraseña
     */
    function validarConfirmacionPassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const confirmInput = document.getElementById('confirm_password');
        
        if (confirmPassword && password !== confirmPassword) {
            confirmInput.style.borderColor = '#dc3545';
            confirmInput.title = 'Las contraseñas no coinciden';
        } else {
            confirmInput.style.borderColor = '#ddd';
            confirmInput.title = '';
        }
    }
    
    /**
     * Actualiza el avatar con la nueva inicial
     */
    function actualizarAvatar(nombre) {
        const avatar = document.querySelector('.profile-avatar');
        if (avatar && nombre) {
            avatar.textContent = nombre.charAt(0).toUpperCase();
        }
    }
    
    /**
     * Actualiza el título de bienvenida
     */
    function actualizarTituloBienvenida(nombre) {
        const titulo = document.querySelector('.main-content h1');
        if (titulo && nombre) {
            titulo.textContent = `¡Bienvenido, ${nombre}!`;
        }
        
        // También actualizar el nombre en el sidebar
        const nombreSidebar = document.querySelector('.profile-header h3');
        if (nombreSidebar && nombre) {
            nombreSidebar.textContent = nombre;
        }
    }
    
    /**
     * Muestra alertas al usuario
     */
    function mostrarAlerta(mensaje, tipo = 'info') {
        if (!alertContainer) return;
        
        // Limpiar alertas anteriores
        alertContainer.innerHTML = '';
        
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo === 'error' ? 'error' : 'success'}`;
        alerta.textContent = mensaje;
        alerta.style.display = 'block';
        
        alertContainer.appendChild(alerta);
        
        // Auto-ocultar después de 5 segundos
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
        
        // Scroll hacia la alerta si es necesario
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
});