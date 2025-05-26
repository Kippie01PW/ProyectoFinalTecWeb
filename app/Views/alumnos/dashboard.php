<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .dashboard-container {
            display: flex;
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .sidebar {
            width: 300px; /* Mantenemos el sidebar aunque esté más vacío */
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .main-content {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-avatar {
            width: 60px;
            height: 60px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 20px;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .alert { /* Mantenemos estilos de alerta por si el JS los usa, aunque la lógica de perfil se fue */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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
                <p><a href="cursos" class="btn">Ver Mis Cursos</a></p>
                <p><a href="/ProyectoFinalTecWeb/public/logout" class="btn btn-secondary">Cerrar Sesión</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/dashboard_alumno.js"></script>
    
    <?php require_once APP_ROOT . '/Views/layouts/footer.php'; ?>
</body>
</html>