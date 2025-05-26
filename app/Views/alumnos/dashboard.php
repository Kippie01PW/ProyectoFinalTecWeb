<!DOCTYPE html>
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
            width: 300px;
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
        
        .profile-section {
            margin-bottom: 30px;
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
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
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
        
        .alert {
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
        
        .password-section {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php require_once APP_ROOT . '/Views/layouts/header_alumnos.php'; ?>
    
    <div class="dashboard-container">
        <!-- Sidebar - Perfil -->
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
                
                <!-- Formulario de actualización de perfil -->
                <form id="perfilForm">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($perfil['nombre'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($perfil['email'] ?? ''); ?>" required>
                    </div>
                    
                    <button type="submit" class="btn">Actualizar Perfil</button>
                </form>
                
                <!-- Sección de contraseña -->
                <div class="password-section">
                    <h4>Cambiar Contraseña</h4>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="password">Nueva Contraseña:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Contraseña:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">Actualizar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Contenido Principal -->
        <div class="main-content">
            <h1>¡Bienvenido, <?php echo htmlspecialchars($perfil['nombre'] ?? $_SESSION['username'] ?? 'Alumno'); ?>!</h1>
            
            <!-- Resumen de estadísticas -->
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
            
            <!-- Gráfica de cursos -->
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