<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Maestros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap Icons (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- ================== NAVBAR SUPERIOR ================== -->
<nav class="navbar navbar-dark bg-light px-5">
  <a href="#">
    <img src="NexoLearn_logo_narvar.png"
         class="img-fluid rounded"
         alt="Logo"
         style="max-width: 200px;">
  </a>
  <ul class="navbar-nav flex-row ms-auto">
    <li class="nav-item px-4">
      <a class="nav-link text-dark fw-bold fs-5" href="#conocenos">Conócenos</a>
    </li>
    <li class="vr mx-2"></li>
    <li class="nav-item px-4">
      <a class="nav-link text-dark fw-bold fs-5" href="#dashboard">Dashboard</a>
    </li>
    <li class="vr mx-2"></li>
    <li class="nav-item px-4">
      <a class="nav-link text-dark fw-bold fs-5" href="#alumnos">Alumnos</a>
    </li>
    <li class="vr mx-2"></li>
    <li class="nav-item px-4">
      <a class="nav-link text-dark fw-bold fs-5" href="#form">Form</a>
    </li>
  </ul>
</nav>


<!-- ================== CONTENIDO ================== -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 mx-auto p-4 mt-5">
            <!-- ===== GRÁFICAS ===== -->
             <div class="titulo px-5 ms-5 mb-4">
                <h1 class="text-dark fw-bold fs-2" >DASHBOARD MAESTRO</h1>
             </div>
             
            <!-- ===== SECCIÓN DINÁMICA DE GRÁFICA ===== -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                    <!-- Gráfica principal a la izquierda -->
                        <div class="col-md-7 d-flex flex-column align-items-center">
                        <!-- Botones de la gráfica -->
                            <div class="d-flex justify-content-center mt-3">
                                <select id="dataSelect" class="form-select me-2 text-center w-auto">
                                    <option value="cursos">Cursos</option>
                                    <option value="alumnos">Alumnos</option>
                                    <option value="progreso">Progreso</option>
                                </select>
                                <button class="btn btn-outline-primary" id="Cambiar_Gráfico">
                                    Cambiar gráfico
                                </button>
                            </div>
                        </div>

                        <!-- Recuadro adicional a la derecha -->
                        <div class="col-md-5 d-flex flex-column align-items-start">
                            <div class="card border-secondary w-100">
                                <div class="card-header bg-secondary text-white text-center">
                                    Anexos
                                </div>
                                <div class="card-body">
                        
                                    <!-- Controles para la segunda gráfica -->
                                    <div class="d-flex mt-3">
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
</body>
</html>