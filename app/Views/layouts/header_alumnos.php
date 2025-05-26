<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexoLearn - Panel Alumno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/PROYECTOFINALTECWEB/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body data-alumno-id="<?php echo htmlspecialchars($_SESSION['alumno_id'] ?? ''); ?>">
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-green py-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="/ProyectoFinalTecWeb/public">
                <img src="/PROYECTOFINALTECWEB/public/assets/images/NexoLearn_logo_narvar.png"  
                     class="navbar-logo" 
                     alt="Logo Educación ODS">
            </a>

            <button class="navbar-toggler" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarAlumno">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarAlumno">
                <ul class="navbar-nav">
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="/ProyectoFinalTecWeb/public/alumnos/cursos">
                            <i class="bi bi-journal-bookmark me-1"></i>
                            Mis Cursos
                        </a>
                    </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="/ProyectoFinalTecWeb/public/alumnos/preferencias/formulario">
                        <i class="bi bi-mortarboard me-1"></i>
                        Formulario
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="#" id="compartirMiId">
                        <i class="bi bi-share me-1"></i> Mostrar ID
                    </a>
                </li>
                <li class="nav-item mx-2">
                     <a class="nav-link" href="/ProyectoFinalTecWeb/public/alumnos/dashboard">
                     <i class="bi bi-bar-chart-steps"></i>
                        Dashboard
                    </a>
                </li>
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link dropdown-toggle" 
                           href="##" 
                           role="button" 
                           data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            Perfil
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/ProyectoFinalTecWeb/public/logout">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4"></div>