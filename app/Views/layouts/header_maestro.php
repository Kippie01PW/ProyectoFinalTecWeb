<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexoLearn - Panel Maestro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/PROYECTOFINALTECWEB/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
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
                    data-bs-target="#navbarMaestro">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarMaestro">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="../maestros/dashboard">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="../clases/">
                            <i class="bi bi-people-fill me-1"></i>
                            Mis Alumnos
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="btn btn-success" href="../cursos/nuevo">
                            <i class="bi bi-plus-circle me-2"></i>
                            Nuevo Curso
                        </a>
                    </li>
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           id="configDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false"> 
                            <i class="bi bi-gear-fill me-1"></i>
                            Configuración
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="configDropdown"> 
                            <li><a class="dropdown-item" href="/ProyectoFinalTecWeb/public/">
                                <i class="bi bi-house-door-fill me-2"></i>Regresar al Home
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/ProyectoFinalTecWeb/public/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                        
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">