<?php

session_start(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\AlumnoController;
use App\Controllers\AuthController; 

// 1. Cargar Autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Cargar Configuración
require __DIR__ . '/../app/config/config.php';


$app = AppFactory::create();


$app->setBasePath('/ProyectoFinalTecWeb/public'); 

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

// Rutas para Autenticación

$app->get('/register', AuthController::class . ':showRegisterForm'); // <-- ¡NUEVO!
$app->post('/api/auth/register', \App\Controllers\AuthController::class . ':processRegistration');

// --- Fin de Rutas ---

// 7. Ejecutar la Aplicación
$app->run();