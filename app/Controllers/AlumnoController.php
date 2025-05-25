<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;   
use App\Models\AlumnoModel; 
use PDO;                 


class AlumnoController
{

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
    }