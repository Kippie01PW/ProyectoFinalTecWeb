<?php 
// Incluir el header
include __DIR__ . '/layouts/header_maestro.php';

// Incluir las clases
require_once __DIR__ . '/../Models/Data_Maestro.php';
require_once __DIR__ . '/../Controllers/MaestroController.php';

// ID del maestro (puedes obtenerlo de la sesión o parámetro)
$maestro_id = 4;

// Crear instancia de la clase de consultas (ya no necesita parámetros de conexión)
$dashboard = new Data_Maestro($maestro_id);

// Obtener todos los datos necesarios
$datosPreguntas = $dashboard->getDatosPreguntas();
$preguntasTexto = $dashboard->getPreguntasTexto();
$totalAlumnos = $dashboard->getTotalAlumnos();
$totalCursos = $dashboard->getTotalCursos();
$alumnosTerminados = $dashboard->getAlumnosTerminados();
$maestro = $dashboard->getDatosMaestro();
$datosProgreso = $dashboard->getDatosProgreso();

// Crear instancia del manejador de JavaScript
$jsHandler = new MaestroController($datosPreguntas, $preguntasTexto, $datosProgreso);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Maestros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <!-- SweetAlert2 para popups más elegantes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- ================== CONTENIDO ================== -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 mx-auto p-4 mt-5">
            <!-- ===== TÍTULO ===== -->
             <div class="titulo px-5 ms-5 mb-4">
                <h1 class="text-dark fw-bold fs-2">DASHBOARD MAESTRO</h1>
             </div>
             
            <!-- ===== SECCIÓN DINÁMICA DE GRÁFICA ===== -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <!-- Gráfica principal a la izquierda -->
                        <div class="col-md-7 d-flex flex-column align-items-center">
                            <!-- Título de la gráfica -->
                            <h5 id="tituloGrafica" class="fw-bold text-center mb-4"></h5>
                            <div class="grafico-container mx-auto" style="width: 100%; height: 400px;">
                                <canvas id="grafico"></canvas>
                            </div>

                            <!-- Controles de la gráfica -->
                            <div class="d-flex justify-content-center mt-4">
                                <select id="dataSelect" class="form-select me-2 text-center w-auto">
                                    <option value="formulario">Formulario</option>
                                    <option value="progreso">Progreso</option>
                                </select>
                                <button class="btn btn-outline-primary" id="Cambiar_Gráfico">
                                    Cambiar gráfico
                                </button>
                                <button class="btn btn-outline-secondary ms-2" id="Cambiar_Pregunta">
                                    Cambiar pregunta
                                </button>
                            </div>
                        </div>

                        <!-- Panel de estadísticas a la derecha -->
                        <div class="col-md-5" style="display: flex; align-items: center; height: 400px;">
                            <div class="card border-secondary w-100" style="margin: auto;">
                                <div class="card-header bg-secondary text-white text-center">
                                    Estadísticas
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="fw-semibold">Total de alumnos:</span>
                                            <span><?= $totalAlumnos ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="fw-semibold">Total de cursos:</span>
                                            <span><?= $totalCursos ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="fw-semibold">Alumnos que terminaron un curso:</span>
                                            <span><?= $alumnosTerminados ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== SECCIÓN DE PERFIL Y CONFIGURACIÓN ===== -->
            <div class="row">
                <!-- Perfil del maestro -->
                <div class="col-md-5">
                    <div class="card mb-4 mt-4">
                        <div class="card-body">
                            <i class="bi bi-person-circle fs-1"></i>
                            <div class="card-body">
                                <form id="formularioMaestro" action="actualizar_maestro.php" method="POST">
                                    <input type="hidden" name="maestro_id" value="<?= $maestro_id ?>">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nombre:</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($maestro['username']) ?>" readonly>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div style="flex: 1;">
                                            <label class="form-label fw-semibold">Correo:</label>
                                            <input type="email" name="correo" id="inputCorreo" class="form-control" value="<?= htmlspecialchars($maestro['email']) ?>">
                                        </div>
                                        <button type="button" class="btn btn-outline-primary ms-3 mt-4" onclick="actualizarDato('correo')">Actualizar</button>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div style="flex: 1;">
                                            <label class="form-label fw-semibold">Contraseña:</label>
                                            <input type="password" name="contrasena" id="inputContrasena" class="form-control" placeholder="Nueva contraseña">
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary ms-3 mt-4" onclick="actualizarDato('contrasena')">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel de información adicional -->
                <div class="col-md-7">
                    <div class="card mb-4 mt-4">
                        <div class="card-body">
                            <i class="bi bi-info-circle fs-1"></i>
                            <div class="card-body">
                                <h5 class="card-title">Información del Dashboard</h5>
                                <p class="card-text">
                                    Este dashboard te permite visualizar las estadísticas de tus alumnos y cursos. 
                                    Puedes cambiar entre diferentes tipos de gráficos y explorar las respuestas 
                                    del formulario de preferencias de aprendizaje.
                                </p>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <strong>Tipos de gráfico disponibles:</strong><br>
                                            • Barras<br>
                                            • Línea<br>
                                            • Dona<br>
                                            • Circular
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <strong>Datos disponibles:</strong><br>
                                            • Respuestas del formulario<br>
                                            • Progreso de alumnos<br>
                                            • Estadísticas generales
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para manejar las actualizaciones con popup
function actualizarDato(tipo) {
    let titulo, mensaje, valor, inputId;
    
    if (tipo === 'correo') {
        titulo = 'Actualizar Correo Electrónico';
        mensaje = '¿Estás seguro de que deseas actualizar tu correo electrónico?';
        inputId = 'inputCorreo';
        valor = document.getElementById(inputId).value;
        
        // Validar email
        if (!validarEmail(valor)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor ingresa un correo electrónico válido'
            });
            return;
        }
    } else if (tipo === 'contrasena') {
        titulo = 'Actualizar Contraseña';
        mensaje = '¿Estás seguro de que deseas actualizar tu contraseña?';
        inputId = 'inputContrasena';
        valor = document.getElementById(inputId).value;
        
        // Validar contraseña
        if (valor.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La contraseña debe tener al menos 6 caracteres'
            });
            return;
        }
    }
    
    // Mostrar popup de confirmación
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizar la actualización
            realizarActualizacion(tipo, valor);
        }
    });
}

// Función para realizar la actualización vía AJAX
function realizarActualizacion(tipo, valor) {
    const formData = new FormData();
    formData.append('maestro_id', <?= $maestro_id ?>);
    formData.append('accion', tipo);
    
    if (tipo === 'correo') {
        formData.append('correo', valor);
    } else if (tipo === 'contrasena') {
        formData.append('contrasena', valor);
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Actualizando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Realizar petición AJAX
    fetch('actualizar_maestro.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: data.message || 'Los datos se han actualizado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Limpiar campo de contraseña si fue actualizada
            if (tipo === 'contrasena') {
                document.getElementById('inputContrasena').value = '';
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Ocurrió un error al actualizar los datos'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error de conexión'
        });
    });
}

// Función para validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// También puedes usar Bootstrap Modal si prefieres no usar SweetAlert2
function mostrarModalBootstrap(tipo) {
    // Código alternativo usando Bootstrap Modal nativo
    const modalHtml = `
        <div class="modal fade" id="modalActualizar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Actualización</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas actualizar tu ${tipo}?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="confirmarActualizacion('${tipo}')">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar modal al DOM si no existe
    if (!document.getElementById('modalActualizar')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalActualizar'));
    modal.show();
}
</script>

<?php 
// Generar el JavaScript
echo $jsHandler->generarJavaScript();

// Cerrar la conexión
$dashboard->cerrarConexion();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
    include __DIR__ . '/layouts/footer.php'; 
?>