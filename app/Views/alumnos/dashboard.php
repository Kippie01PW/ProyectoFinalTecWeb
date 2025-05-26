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
    
    <div style="background-color: #e0f7fa; padding: 15px; text-align: center; margin-bottom: 20px; border-bottom: 1px solid #b2ebf2;">
        <h2 style="color: #00838f; margin: 0; font-size: 3em;">
            ¡Bienvenido, <?php echo htmlspecialchars($perfil['nombre'] ?? $_SESSION['username'] ?? 'Alumno'); ?>!</h3>
    </div>

    <div class="dashboard-container" style="display: flex; justify-content: center; gap: 20px; padding: 20px; max-width: 1200px; margin: 0 auto; flex-wrap: wrap;">
        
        <div class="buttons-container" style="flex: 0 0 auto; width: 200px; padding: 15px; background: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: flex-start; justify-content: flex-start; gap: 10px;">
            
            <div style="text-align: center; width: 100%; margin-bottom: 15px; padding-top: 15px; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
                <h3 style="color: #00838f; margin: 0; font-size: 2.3em;">Completa tus cursos ¡Tú puedes! 🤠</h3>
            </div>

            <p style="margin-bottom: 0;"><a href="cursos" class="btn btn-secondary" style="display: block; width: 100%; text-align: left;">Ver Mis Cursos</a></p>
            <p style="margin-bottom: 0;"><a href="/ProyectoFinalTecWeb/public/logout" class="btn btn-secondary" style="display: block; width: 100%; text-align: left;">Cerrar Sesión</a></p>
        </div>

        <div class="main-content" style="flex: 1; min-width: 600px; max-width: 900px; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
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
        </div>
    </div>
    
    <script src="../assets/js/dashboard_alumno.js"></script>
    <script src="/PROYECTOFINALTECWEB/public/assets/js/botonID.js"></script>
    
    <?php require_once APP_ROOT . '/Views/layouts/footer.php'; ?>
</body>
</html>