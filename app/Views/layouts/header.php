<?php
// Nos aseguramos de que la sesión esté iniciada ANTES de intentar leerla.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// URL base para construir enlaces (¡Ajusta si es necesario!)
$baseUrl = "/ProyectoFinalTecWeb/public"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexoLearn - Educación ODS</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ProyectoFinalTecWeb/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-green py-3">
        <div class="container">
        
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>/">
                <img src="/ProyectoFinalTecWeb/public/assets/images/NexoLearn_logo_narvar.png"  
                     class="navbar-logo" 
                     alt="Logo NexoLearn">
            </a>
            
            <button class="navbar-toggler" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">

                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <?php // --- Usuario CON Sesión Iniciada --- ?>
                        
                        <?php 

                            $dashboard_link = ($_SESSION['role'] === 'alumno') 
                                            ? $baseUrl . '/alumnos/dashboard' 
                                            : $baseUrl . '/maestros/dashboard'; 
                        ?>

                        <?php if ($_SESSION['role'] === 'alumno') : ?>
                            <li class="nav-item me-3">
                                <a class="nav-link btn btn-outline-light" href="<?php echo $baseUrl; ?>/alumnos/cursos">Mis Cursos</a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link" href="#">Mis Grupos</a> </li>
                            <li class="nav-item me-3">
                                <a class="nav-link" href="#">Mi Aprendizaje</a> </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] === 'maestro') : ?>
                             <li class="nav-item me-3">
                                <a class="nav-link" href="<?php echo $baseUrl; ?>/maestros/dashboard">Gestionar</a> </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link btn btn-outline-light dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo $dashboard_link; ?>">Mi Perfil / Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>/logout">Cerrar Sesión</a></li>
                            </ul>
                        </li>


                    <?php else : ?>
                        <?php // --- Usuario SIN Sesión Iniciada --- ?>

                        <li class="nav-item me-3">
                            <a class="nav-link btn btn-outline-light" href="<?php echo $baseUrl; ?>/login">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light" 
                               href="<?php echo $baseUrl; ?>/register">
                                Registrarse
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">