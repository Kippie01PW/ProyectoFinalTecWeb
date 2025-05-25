<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;   
use App\Models\AlumnoModel; 
use PDO;                 


class AlumnoController
{
    
    private $datosProgreso;
    /**
     * Ruta: GET /api/alumnos/cursos/asignados
     */
    public function getCursosAsignados(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }


        $alumno_id = $_SESSION['alumno_id'] ?? 1; // Usamos 1 como default para pruebas

        try {
            // 3. Obtener Conexión PDO
            //    Creamos una nueva instancia de nuestra clase Conexion y obtenemos el objeto PDO.
            $pdo = (new Conexion())->getConexion();

            // 4. Instanciar el Modelo, pasando la conexión PDO
            $alumnoModel = new AlumnoModel($pdo);

            // 5. Llamar al método del Modelo para obtener los cursos
            $cursos = $alumnoModel->findCursosAsignados($alumno_id);

            // 6. Preparar la respuesta JSON con los datos reales
            $payload = json_encode($cursos);
            $response->getBody()->write($payload);

            // 7. Devolver la respuesta
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200); // 200 OK

        } catch (\Exception $e) {
            // Si algo sale mal (ej: error de BD), devolvemos un error 500.
            $errorData = ['error' => 'No se pudieron obtener los cursos asignados.', 'message' => $e->getMessage()];
            $payload = json_encode($errorData);
            $response->getBody()->write($payload);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500); // 500 Internal Server Error
        }
    }

/**
     * Obtiene los cursos completados por el alumno.
     * Ruta: GET /api/alumnos/cursos/completados
     */
    public function getCursosCompletados(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $alumno_id = $_SESSION['alumno_id'] ?? 1; // Usamos 1 para pruebas

        try {
            $pdo = (new Conexion())->getConexion();
            $alumnoModel = new AlumnoModel($pdo);
            // ¡Llamamos al nuevo método!
            $cursos = $alumnoModel->findCursosCompletados($alumno_id); 

            $payload = json_encode($cursos);
            $response->getBody()->write($payload);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);

        } catch (\Exception $e) {
            $errorData = ['error' => 'No se pudieron obtener los cursos completados.', 'message' => $e->getMessage()];
            $payload = json_encode($errorData);
            $response->getBody()->write($payload);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
        }
    }

/**
     * Obtiene las clases a las que pertenece el alumno.
     * Ruta: GET /api/alumnos/clases
     */
    public function getMisClases(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $alumno_id = $_SESSION['alumno_id'] ?? 1; // Usamos 1 para pruebas

        try {
            $pdo = (new Conexion())->getConexion();
            $alumnoModel = new AlumnoModel($pdo);
            // ¡Llamamos al nuevo método!
            $clases = $alumnoModel->findMisClases($alumno_id); 

            $payload = json_encode($clases);
            $response->getBody()->write($payload);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);

        } catch (\Exception $e) {
            $errorData = ['error' => 'No se pudieron obtener las clases.', 'message' => $e->getMessage()];
            $payload = json_encode($errorData);
            $response->getBody()->write($payload);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
        }
    }

/**
     * Permite al alumno unirse a una clase.
     * Ruta: POST /api/alumnos/clases/unirse
     */
    
    
    /**
     * Muestra la página HTML principal de cursos para el alumno.
     */
    public function showCursosPage(Request $request, Response $response, $args)
    {
        ob_start();
        require_once APP_ROOT . '/Views/layouts/header_alumnos.php'; 
        require_once APP_ROOT . '/Views/alumnos/cursos.php';       
        require_once APP_ROOT . '/Views/layouts/footer.php';          
        $output = ob_get_clean(); 
        $response->getBody()->write($output); 
        return $response;
    }    
    public function showDashboard(Request $request, Response $response, $args) {
        ob_start();
        require_once APP_ROOT . '/Views/alumnos/dashboard.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
    public function generarJavaScript() {
    $etiquetasProgreso = json_encode($this->datosProgreso['etiquetas']);
    $valoresProgreso = json_encode($this->datosProgreso['valores']);

    return "
    <script>
    const datos = {
        progreso: {
            etiquetas: {$etiquetasProgreso},
            valores: {$valoresProgreso}
        }
    };

    let chart;
    let tipoGrafico = 'bar';

    function generarColores(cantidad) {
        const baseColor = [75, 192, 192];
        return Array.from({ length: cantidad }, (_, i) => {
            const factor = 0.5 + (i / cantidad) * 0.5;
            return `rgba(\${baseColor[0]}, \${baseColor[1]}, \${baseColor[2]}, \${factor})`;
        });
    }

    function crearGrafico(etiquetas, valores, titulo = '') {
        const ctx = document.getElementById('grafico').getContext('2d');
        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: tipoGrafico,
            data: {
                labels: etiquetas,
                datasets: [{
                    label: titulo,
                    data: valores,
                    backgroundColor: generarColores(valores.length),
                    borderColor: generarColores(valores.length).map(color =>
                        color.replace(/[\\d\\.]+\\)\$/g, '1)')
                    ),
                    borderWidth: 1,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: tipoGrafico === 'pie' || tipoGrafico === 'doughnut',
                        position: 'top',
                        labels: { padding: 20 }
                    },
                    datalabels: tipoGrafico !== 'line' ? {
                        color: '#000',
                        anchor: 'center',
                        align: 'center',
                        formatter: (value, context) => {
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            return total ? (value / total * 100).toFixed(1) + '%' : '0%';
                        }
                    } : false
                },
                scales: tipoGrafico !== 'pie' && tipoGrafico !== 'doughnut' ? {
                    y: { beginAtZero: true }
                } : {}
            },
            plugins: [ChartDataLabels]
        });
    }

    function cambiarTipoGrafico() {
        const tipos = ['bar', 'line', 'doughnut', 'pie'];
        const index = tipos.indexOf(tipoGrafico);
        tipoGrafico = tipos[(index + 1) % tipos.length];
        
        const datosActuales = chart.data;
        crearGrafico(datosActuales.labels, datosActuales.datasets[0].data, datosActuales.datasets[0].label);
    }

    function actualizarGrafico(etiquetas, valores, titulo = '') {
        if (chart) {
            chart.data.labels = etiquetas;
            chart.data.datasets[0].data = valores;
            chart.data.datasets[0].label = titulo;
            chart.data.datasets[0].backgroundColor = generarColores(valores.length);
            chart.data.datasets[0].borderColor = generarColores(valores.length).map(color => 
                color.replace(/[\\d\\.]+\\)\$/g, '1)')
            );
            chart.update();
        } else {
            crearGrafico(etiquetas, valores, titulo);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('Cambiar_Gráfico')) {
            document.getElementById('Cambiar_Gráfico').addEventListener('click', cambiarTipoGrafico);
        }

        if (datos.progreso.etiquetas.length > 0) {
            crearGrafico(datos.progreso.etiquetas, datos.progreso.valores, 'Progreso');
        }
    });
    </script>";
}

}