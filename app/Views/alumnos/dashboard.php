<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="..\assets\css\dashboardAlumno.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>

</head>
<body>
    <?php require_once APP_ROOT . '/Views/layouts/header_alumnos.php'; ?>
    
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($perfil['nombre'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div>
                        <h3><?php echo htmlspecialchars($perfil['nombre'] ?? 'Alumno'); ?></h3>
                        <p style="color: #666; margin: 0;">Estudiante</p>
                    </div>
                </div>
                
                <div id="alertContainer"></div>
                
                <p>Tu perfil está configurado para mostrar solo las estadísticas.</p>

            </div>
        </div>
        
        <div class="main-content">
            <h1>¡Bienvenido, <?php echo htmlspecialchars($perfil['nombre'] ?? $_SESSION['username'] ?? 'Alumno'); ?>!</h1>
            
            <div class="stats-summary">
                <div class="stat-card">
                    <div class="stat-number" id="totalCursos"><?php echo $estadisticas['total'] ?? 0; ?></div>
                    <div class="stat-label">Total Cursos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="cursosAsignados"><?php echo $estadisticas['asignados'] ?? 0; ?></div>
                    <div class="stat-label">Asignados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="cursosCompletados"><?php echo $estadisticas['completados'] ?? 0; ?></div>
                    <div class="stat-label">Completados</div>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="cursosChart"></canvas>
            </div>
            
            <hr>
            <div>
                <p><a href="cursos" class="btn btn-secondary">Ver Mis Cursos</a></p>
                <p><a href="/ProyectoFinalTecWeb/public/logout" class="btn btn-secondary">Cerrar Sesión</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/dashboard_alumno.js"></script>
    
    <?php require_once APP_ROOT . '/Views/layouts/footer.php'; ?>
</body>
</html>