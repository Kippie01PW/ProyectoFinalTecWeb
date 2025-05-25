<?php 

include __DIR__ . '/layouts/header_maestro.php';

require_once __DIR__ . '/../Models/Data_Maestro.php';
require_once __DIR__ . '/../Controllers/MaestroController.php';


session_start();
$maestro_id = $_SESSION['user_id'] ?? 4;

$dashboard = new Data_Maestro($maestro_id);

$datosPreguntas = $dashboard->getDatosPreguntas();
$preguntasTexto = $dashboard->getPreguntasTexto();
$totalAlumnos = $dashboard->getTotalAlumnos();
$totalCursos = $dashboard->getTotalCursos();
$alumnosTerminados = $dashboard->getAlumnosTerminados();
$maestro = $dashboard->getDatosMaestro();
$datosProgreso = $dashboard->getDatosProgreso();

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
                                <form id="formularioMaestro" action="Maestro_actu.php" method="POST">
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

function actualizarDato(tipo) {
    let valor, inputId;
    
    if (tipo === 'correo') {
        inputId = 'inputCorreo';
        valor = document.getElementById(inputId).value.trim();
        
        if (!valor || !validarEmail(valor)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor ingresa un correo electrónico válido'
            });
            return;
        }
    } else if (tipo === 'contrasena') {
        inputId = 'inputContrasena';
        valor = document.getElementById(inputId).value;
        
        if (!valor || valor.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La contraseña debe tener al menos 6 caracteres'
            });
            return;
        }
        
        if (!validarContrasena(valor)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La contraseña debe contener al menos una mayúscula, una minúscula y un número'
            });
            return;
        }
    }
    
    realizarActualizacion(tipo, valor);
}

function realizarActualizacion(tipo, valor) {
    const formData = new FormData();
    formData.append('maestro_id', <?= $maestro_id ?>);
    formData.append('accion', tipo);
    
    if (tipo === 'correo') {
        formData.append('correo', valor);
    } else if (tipo === 'contrasena') {
        formData.append('contrasena', valor);
    }

    console.log('Datos enviados:', {
        maestro_id: <?= $maestro_id ?>,
        accion: tipo,
        valor: valor
    });

    Swal.fire({
        title: 'Actualizando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('../app/Controllers/Maestro_actu.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text(); 
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: data.message || 'Los datos se han actualizado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });

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
        } catch (e) {
            console.error('Error parsing JSON:', e);
            console.error('Raw response:', text);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la respuesta del servidor'
            });
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error de conexión'
        });
    });
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarContrasena(password) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/;
    return regex.test(password);
}

function mostrarModalBootstrap(tipo) {
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
    
    if (!document.getElementById('modalActualizar')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalActualizar'));
    modal.show();
}
</script>

<?php 

echo $jsHandler->generarJavaScript();


$dashboard->cerrarConexion();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
    include __DIR__ . '/layouts/footer.php'; 
?>