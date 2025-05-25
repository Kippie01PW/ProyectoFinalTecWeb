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
use Psr\Http\Server\RequestHandlerInterface;
////NUEVO
use App\Controllers\PreferenciasAlumnoController;
///Nuevo
use App\Controllers\CursoController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/config/config.php';

$app = AppFactory::create();

$app->setBasePath('/ProyectoFinalTecWeb/public'); 

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true); 

$requireAuth = function (Request $request, RequestHandlerInterface $handler) {
    if (session_status() == PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user_id'])) {
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/login')->withStatus(302);
    }
    return $handler->handle($request);
};

$requireAlumno = function (Request $request, RequestHandlerInterface $handler) {
    if ($_SESSION['role'] !== 'alumno') {
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/')->withStatus(302);
    }

    return $handler->handle($request);
};

$requireMaestro = function (Request $request, RequestHandlerInterface $handler) {
    if ($_SESSION['role'] !== 'maestro') {
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/')->withStatus(302);
    }
    return $handler->handle($request);
};

$app->get('/', function (Request $request, Response $response, $args) {
    ob_start();
    require APP_ROOT . '/Views/home.php';
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

$app->group('/alumnos', function ($group) {
    $group->get('/dashboard', \App\Controllers\AlumnoController::class . ':showDashboard');
    $group->get('/cursos', \App\Controllers\AlumnoController::class . ':showCursosPage');
    //NUEVO
    $group->get('/preferencias/formulario', \App\Controllers\PreferenciasAlumnoController::class . ':showFormulario');
    $group->post('/preferencias/formulario', \App\Controllers\PreferenciasAlumnoController::class . ':showFormulario');
    $group->post('/preferencias/guardar', \App\Controllers\PreferenciasAlumnoController::class . ':guardarPreferencias');
})->add($requireAlumno)->add($requireAuth); 

// Rutas para Alumnos 
$app->group('/api/alumnos', function ($group) {
    $group->get('/cursos/asignados', AlumnoController::class . ':getCursosAsignados');
    $group->get('/cursos/completados', AlumnoController::class . ':getCursosCompletados');
    $group->get('/clases', AlumnoController::class . ':getMisClases');
    $group->post('/clases/unirse', AlumnoController::class . ':unirseAClase');

    //NUEVO
   // $group->get('/preferencias/formulario', \App\Controllers\PreferenciasAlumnoController::class . ':showFormulario');
   // $group->post('/preferencias/guardar', \App\Controllers\PreferenciasAlumnoController::class . ':guardarPreferencias');
});

// Rutas para Autenticación

$app->get('/register', AuthController::class . ':showRegisterForm'); // <-- ¡NUEVO!
$app->post('/api/auth/register', \App\Controllers\AuthController::class . ':processRegistration');
$app->get('/login', \App\Controllers\AuthController::class . ':showLoginForm');
$app->post('/api/auth/login', \App\Controllers\AuthController::class . ':processLogin');
$app->get('/logout', \App\Controllers\AuthController::class . ':logout');

///Nuevo
// Rutas para Cursos 

$app->group('/cursos', function ($group) {
    // Mostrar formulario para crear nuevo curso
    $group->get('/nuevo', CursoController::class . ':showForm');
    // Procesar creación de curso
    $group->post('/guardar', CursoController::class . ':guardarCurso');
    // Listar todos los cursos
    $group->get('/listar', CursoController::class . ':listarCursos');
    // Listar cursos por categoría
    $group->get('/categoria/{id}', CursoController::class . ':listarCursos');
});

// --- Fin de Rutas ---

// 7. Ejecutar la Aplicación
$app->run();