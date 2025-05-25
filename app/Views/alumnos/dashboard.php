<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php 
        require_once APP_ROOT . '/Views/layouts/header_alumnos.php'; 

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    ?>
    
    <div class="container mt-5">
        <h1 class="mb-4">Dashboard del Alumno</h1>
        <p>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Alumno'); ?>!</p>
        <p>Tu rol es: <?php echo htmlspecialchars($_SESSION['role'] ?? 'desconocido'); ?></p>

        <hr>

        <h3 class="mt-4 mb-3">Tus Cursos y Progreso</h3>

        <!-- Tabla vacía con columnas "Curso" y "Estado" -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-secondary">
                    <tr>
                        <th>Curso</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se llenarán las filas dinámicamente -->
                </tbody>
            </table>
        </div>

        <hr>

        <p><a href="cursos" class="btn btn-outline-primary">Ver Mis Cursos</a></p>
        <p><a href="/ProyectoFinalTecWeb/public/logout" class="btn btn-outline-danger">Cerrar Sesión</a></p>
    </div>

    <?php require_once APP_ROOT . '/Views/layouts/footer.php'; ?>
</body>
</html>
