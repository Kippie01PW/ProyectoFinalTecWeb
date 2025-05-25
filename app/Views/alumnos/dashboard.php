<?php 

include __DIR__ . '/../layouts/header_alumnos.php';

require_once __DIR__ . '/../../Models/Data_Alumno.php';
require_once __DIR__ . '/../../Controllers/AlumnoController.php';

use App\Controllers\AlumnoController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$alumno_id = $_SESSION['user_id'] ?? 4;


$dashboard = new Data_Alumno($alumno_id);


$datosProgreso = $dashboard->getDatosProgreso();
$datosCursos = $dashboard->getCursosAlumno();
$datosProgresoDetallado = $dashboard->getProgresoDetallado();
$cursosConProgreso = $dashboard->getCursosConProgreso();
$alumno = $dashboard->getDatosAlumno();
$alumnoController = new AlumnoController($datosProgreso);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/ProyectoFinalTecWeb/public/assets/js/dashboard_alumno.js"></script>
</head>
<body>
    <div id="mensajeDashboard" style="display:none; padding: 15px; margin-bottom: 20px; border-radius: 5px;"></div>

    <div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 mx-auto p-4 mt-5">
            <!-- ===== TÍTULO ===== -->
             <div class="titulo px-5 ms-5 mb-4">
                <h1 class="text-dark fw-bold fs-2">DASHBOARD ALUMNO</h1>
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
                                    <option value="cursos">Mis Cursos</option>
                                    <option value="progreso">Progreso</option>
                                </select>
                                <button class="btn btn-outline-primary" id="Cambiar_Gráfico">
                                    Cambiar gráfico
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
                                        <?php foreach ($cursosConProgreso as $curso): ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span class="fw-semibold"><?= htmlspecialchars($curso['nombre']) ?>:</span>
                                                <span><?= $curso['progreso'] ?>% 
                                                    <small class="text-muted">(<?= ucfirst($curso['estado']) ?>)</small>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== SECCIÓN DE PERFIL Y CONFIGURACIÓN ===== -->
            <div class="row">
                <!-- Perfil del alumno -->
                <div class="col-md-5">
                    <div class="card mb-4 mt-4">
                        <div class="card-body">
                            <i class="bi bi-person-circle fs-1"></i>
                            <div class="card-body">
                                <form id="formularioAlumno" action="Alumno_actu.php" method="POST">
                                    <input type="hidden" name="alumno_id" value="<?= $alumno_id ?>">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nombre:</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($alumno['username']) ?>" readonly>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div style="flex: 1;">
                                            <label class="form-label fw-semibold">Correo:</label>
                                            <input type="email" name="correo" id="inputCorreo" class="form-control" value="<?= htmlspecialchars($alumno['email']) ?>">
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
    const ALUMNO_ID = <?= json_encode($alumno_id) ?>;
    window.ALUMNO_ID = ALUMNO_ID;
</script>


<?php 
echo $jsHandler->generarJavaScript();
$dashboard->cerrarConexion();
?>
    
    <p>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Alumno'); ?>!</p>
    <p>Tu rol es: <?php echo htmlspecialchars($_SESSION['role'] ?? 'desconocido'); ?></p>

    <hr>

    <p><a href="cursos">Ver Mis Cursos</a></p>
    <p><a href="/ProyectoFinalTecWeb/public/logout">Cerrar Sesión</a></p>

    <?php 

        require_once APP_ROOT . '/Views/layouts/footer.php'; 
    ?>
</body>
    <script src="/ProyectoFinalTecWeb/public/assets/js/app.js"></script>

</html>