<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\AlumnoController;

// 1. Cargar Autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Cargar Configuración
require __DIR__ . '/../app/config/config.php'; // Asegúrate de que APP_ROOT está definido aquí.

// 3. Crear Instancia de Slim
$app = AppFactory::create();
$app->setBasePath('/ProyectoFinalTecWeb/public'); // <--- AÑADIR ESTO

// 4. Añadir Middleware de Ruteo
$app->addRoutingMiddleware();

// 5. Añadir Middleware de Errores
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// --- 6. Definir Rutas ---

/**
 * @GET /
 * Descripción: Muestra la página principal "Conócenos".
 */
$app->get('/', function (Request $request, Response $response, $args) {
    // --- Manera simple de incluir tu vista home.php ---
    ob_start(); // Inicia el buffer de salida



    require APP_ROOT . '/Views/home.php';


    $output = ob_get_clean(); // Obtiene el contenido del buffer y lo limpia
    $response->getBody()->write($output); // Escribe el HTML en la respuesta
    return $response;
    // --- Fin manera simple ---
});

/**
 * Grupo de Rutas para la API de Alumnos
 * Estas rutas SÍ serán usadas por tu módulo (vía AJAX probablemente).
 */
$app->group('/api/alumnos', function ($group) {
    $group->get('/cursos/asignados', AlumnoController::class . ':getCursosAsignados');
    $group->get('/cursos/completados', AlumnoController::class . ':getCursosCompletados');
    $group->get('/clases', AlumnoController::class . ':getMisClases');
    $group->post('/clases/unirse', AlumnoController::class . ':unirseAClase');
});

// --- Aquí irían las rutas de Login, Maestro, etc. ---


// 7. Ejecutar la Aplicación
$app->run();