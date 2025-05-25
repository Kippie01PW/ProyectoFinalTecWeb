<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="..\assets\css\styleMostrarCursos.css">
    <script src="/PROYECTOFINALTECWEB/public/assets/js/alumnos.js"></script>
    
    
</head>
<body>
    <div class="container">
        <h1>Mis Cursos</h1>

        <div class="cursos-container">
            <div class="curso-seccion asignados">
                <h2>Cursos Asignados</h2>
                <div id="loading-asignados">Cargando cursos asignados...</div>
                <table id="tabla-asignados" style="display:none;">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Clase</th>
                            <th>Acceso al Curso</th>
                            <th>Subir Evidencia</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="curso-seccion completados">
                <h2>Cursos Completados</h2>
                <div id="loading-completados">Cargando cursos completados...</div>
                <table id="tabla-completados" style="display:none;">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Fecha Completado</th>
                            <th>Evidencia</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/ProyectoFinalTecWeb/public/assets/js/MostrarCursos.js"></script>
    <script src="/PROYECTOFINALTECWEB/public/assets/js/botonID.js"></script>

</body>
</html>