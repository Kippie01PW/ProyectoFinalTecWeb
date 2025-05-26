<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Alumno</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
    <?php 
        //SOLO ES UN PLACEHOLDER
        //ACA PONGAN EL DASHBOARD
        require_once APP_ROOT . '/Views/layouts/header_alumnos.php'; 
    ?>

    
    <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Alumno'); ?>!</h1>
    <p>Tu rol es: <?php echo htmlspecialchars($_SESSION['role'] ?? 'desconocido'); ?></p>

    <hr>
    <div>

        <p>aqui va todo el dashboard</p>
    </div>

    <p><a href="cursos">Ver Mis Cursos</a></p>
    <p><a href="/ProyectoFinalTecWeb/public/logout">Cerrar Sesión</a></p>

    <?php 

        require_once APP_ROOT . '/Views/layouts/footer.php'; 
    ?>
</body>
</html>