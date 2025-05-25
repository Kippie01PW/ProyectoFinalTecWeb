<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <style>
        /* Estilos básicos para que se vea algo ordenado */
        body { font-family: sans-serif; }
        h1, h2 { text-align: center; color: #333; }
        .cursos-container { display: flex; justify-content: space-around; padding: 20px; gap: 20px; }
        .curso-seccion { border: 1px solid #ccc; border-radius: 8px; padding: 15px; width: 48%; box-shadow: 2px 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        #loading-asignados, #loading-completados { text-align: center; padding: 20px; font-size: 1.1em; color: #555; }
        .btn-primary { background-color: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-info { background-color: #17a2b8; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="cursos-container">

        <div class="curso-seccion">
            <h2>Cursos Asignados</h2>
            <div id="loading-asignados">Cargando...</div>
            <table id="tabla-asignados" style="display:none;">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Ir al Curso</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>

        <div class="curso-seccion">
            <h2>Cursos Completados</h2>
            <div id="loading-completados">Cargando...</div>
            <table id="tabla-completados" style="display:none;">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Completado</th>
                        <th>Ver Evidencia</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <script src="../assets/js/alumnos.js"></script> 

    </body>
</html>