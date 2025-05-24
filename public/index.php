<?php

session_start(); // Iniciamos sesión al principio

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\AlumnoController;
use App\Controllers\AuthController; // <-- Podemos prever su uso

// 1. Cargar Autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Cargar Configuración
require __DIR__ . '/../app/config/config.php';

// 3. Crear Instancia de Slim
$app = AppFactory::create();

// 4. Establecer Base Path (¡Muy importante!)
$app->setBasePath('/ProyectoFinalTecWeb/public'); // <-- Ajusta si es necesario

// 5. Añadir Middleware de Ruteo y Errores
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true); // true para desarrollo

// --- 6. Definir Rutas ---

// Ruta / (Conócenos)
$app->get('/', function (Request $request, Response $response, $args) {
    ob_start();
    require APP_ROOT . '/Views/home.php';
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

// Rutas para Alumnos (¡Las tuyas!)
$app->group('/api/alumnos', function ($group) {
    $group->get('/cursos/asignados', AlumnoController::class . ':getCursosAsignados');
    $group->get('/cursos/completados', AlumnoController::class . ':getCursosCompletados');
    $group->get('/clases', AlumnoController::class . ':getMisClases');
    $group->post('/clases/unirse', AlumnoController::class . ':unirseAClase');
});

// Rutas para Autenticación (¡Las adaptaremos luego!)
// Por ahora, podemos definir la ruta GET para mostrar el formulario
$app->get('/register', AuthController::class . ':showRegisterForm'); // <-- ¡NUEVO!
$app->post('/api/auth/register', AuthController::class . ':register'); // <-- ¡NUEVO!

// --- Fin de Rutas ---

// 7. Ejecutar la Aplicación
$app->run();