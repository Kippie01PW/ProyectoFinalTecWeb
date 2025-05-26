<?php 
// 1. Incluye el header (que ya abre <html>, <head>, <body> y carga CSS/JS comunes)
include __DIR__ . '/layouts/header_maestro.php';

// 2. Prepara tus datos PHP
require_once __DIR__ . '/../Models/Data_Maestro.php';
require_once __DIR__ . '/../Controllers/MaestroController.php';

use App\Controllers\MaestroController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$maestro_id = $_SESSION['maestro_id'] ?? 4;

$dashboard = new Data_Maestro($maestro_id);
$datosPreguntas = $dashboard->getDatosPreguntas();
$preguntasTexto = $dashboard->getPreguntasTexto();
$totalAlumnos = $dashboard->getTotalAlumnos();
$totalCursos = $dashboard->getTotalCursos();
$alumnosTerminados = $dashboard->getAlumnosTerminados();
$maestro = $dashboard->getDatosMaestro();
$datosProgreso = $dashboard->getDatosProgreso();
$jsHandler = new \App\Controllers\MaestroController($datosPreguntas, $preguntasTexto, $datosProgreso);

// ---> ¡NO MÁS <head> NI <body> AQUÍ! <---

// 3. Añade CSS/JS específicos SI ES NECESARIO (Idealmente en Header/Footer)
//    PERO NO VUELVAS A CARGAR BOOTSTRAP/JQUERY
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 mx-auto p-4 mt-5">
            <div class="titulo px-5 ms-5 mb-4">
                <h1 class="text-dark fw-bold fs-2">DASHBOARD MAESTRO</h1>
             </div>
             
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 d-flex flex-column align-items-center">
                            <h5 id="tituloGrafica" class="fw-bold text-center mb-4"></h5>
                            <div class="grafico-container mx-auto" style="width: 100%; height: 400px;">
                                <canvas id="grafico"></canvas>
                            </div>

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

            <div class="row">
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

                <div class="col-md-7">
                    <div class="card mb-4 mt-4">
                        <div class="card-body" style="padding-bottom: 17px;">
                            <i class="bi bi-info-circle fs-1"></i>
                            <div class="card-body">
                                <h5 class="card-title">Información del Dashboard</h5>
                                <p class="card-text">
                                    Este dashboard te permite visualizar las estadísticas de tus alumnos y cursos. 
                                    Puedes cambiar entre diferentes tipos de gráficos y explorar las respuestas 
                                    del formulario de preferencias de aprendizaje.
                                </p>
                                <div class="row">
                                    <div class="col-6 mt-3">
                                        <small class="text-muted">
                                            <strong>Tipos de gráfico disponibles:</strong><br>
                                            • Barras<br>
                                            • Línea<br>
                                            • Dona<br>
                                            • Circular
                                        </small>
                                    </div>
                                    <div class="col-6 mt-3">
                                        <small class="text-muted">
                                            <strong>Datos disponibles:</strong><br>
                                            • Respuestas del formulario<br>
                                            • Progreso de alumnos<br>
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
    // 4. Pasa variables PHP a JS si es necesario
    const MAESTRO_ID = <?= json_encode($maestro_id) ?>;
    window.MAESTRO_ID = MAESTRO_ID;
</script>

<?php 
// 5. Genera JS dinámico si es necesario
echo $jsHandler->generarJavaScript();
$dashboard->cerrarConexion();

// 6. Incluye el footer (que ya cierra <body>, <html> y carga JS comunes)
include __DIR__ . '/layouts/footer.php'; 
?>