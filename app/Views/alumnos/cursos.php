

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        
        h2 { 
            text-align: center; 
            color: #495057; 
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        
        .cursos-container { 
            display: flex; 
            justify-content: space-between; 
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .curso-seccion { 
            background: white;
            border: 1px solid #e9ecef; 
            border-radius: 12px; 
            padding: 25px; 
            width: 48%; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            min-height: 400px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            background: white;
        }
        
        th, td { 
            border: 1px solid #dee2e6; 
            padding: 12px; 
            text-align: left;
            vertical-align: top;
        }
        
        th { 
            background-color: #f8f9fa; 
            font-weight: 600;
            color: #495057;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        #loading-asignados, #loading-completados { 
            text-align: center; 
            padding: 40px; 
            font-size: 1.1em; 
            color: #6c757d;
        }
        
        .btn-primary { 
            background-color: #007bff; 
            color: white; 
            padding: 8px 15px; 
            text-decoration: none; 
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-info { 
            background-color: #17a2b8; 
            color: white; 
            padding: 8px 15px; 
            text-decoration: none; 
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn-info:hover {
            background-color: #117a8b;
        }
        
        .alert-danger { 
            color: #721c24; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb;
            padding: 15px; 
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .cursos-container {
                flex-direction: column;
            }
            
            .curso-seccion {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mis Cursos</h1>

        <div class="cursos-container">
            <div class="curso-seccion">
                <h2>Cursos Asignados</h2>
                <div id="loading-asignados">Cargando cursos asignados...</div>
                <table id="tabla-asignados" style="display:none;">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Acceso</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="curso-seccion">
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
    <script src="/PROYECTOFINALTECWEB/public/assets/js/alumnos.js"></script>
</body>
</html>